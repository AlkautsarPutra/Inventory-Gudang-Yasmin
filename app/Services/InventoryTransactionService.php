<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Inventory\Exceptions\InsufficientInventoryStock;
use App\Domain\Inventory\Exceptions\InvalidInventoryTransaction;
use App\Enums\InventoryMovementDirection;
use App\Enums\InventoryTransactionType;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Domain-focused service for the immutable inventory ledger.
 *
 * All write operations hold locks on both the product and its materialized
 * balance, so the ledger entry and current stock always commit atomically.
 */
class InventoryTransactionService
{
    /**
     * Retrieve all inventory transactions, newest first.
     *
     * @return Collection<int, InventoryTransaction>
     */
    public function getAll(): Collection
    {
        return $this->queryWithRelations()
            ->latestTransaction()
            ->get();
    }

    /**
     * Paginate inventory transactions, newest first.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->queryWithRelations()
            ->latestTransaction()
            ->paginate($perPage);
    }

    /**
     * Find a transaction by its primary key.
     *
     * @throws ModelNotFoundException
     */
    public function findById(int $id): InventoryTransaction
    {
        return $this->queryWithRelations()->findOrFail($id);
    }

    /**
     * Find a transaction by its public transaction number.
     *
     * @throws ModelNotFoundException
     */
    public function findByTransactionNumber(string $transactionNumber): InventoryTransaction
    {
        return $this->queryWithRelations()
            ->where('transaction_number', $transactionNumber)
            ->firstOrFail();
    }

    /**
     * Get the materialized current stock of a product.
     */
    public function currentStockForProduct(int $productId): string
    {
        return (string) (InventoryStock::query()
            ->where('product_id', $productId)
            ->value('quantity') ?? '0.000');
    }

    /**
     * Record a new ledger entry.
     *
     * The caller must authorize the negative-stock override before passing
     * true. This keeps authorization at the application boundary while the
     * domain invariant remains enforced here.
     *
     * @param array<string, mixed> $data
     */
    public function create(
        array $data,
        User $actor,
        bool $mayOverrideNegativeStock = false,
    ): InventoryTransaction {
        return DB::transaction(function () use ($data, $actor, $mayOverrideNegativeStock): InventoryTransaction {
            $type = $this->transactionType($data['transaction_type'] ?? null);

            if ($type === InventoryTransactionType::REVERSAL) {
                throw new InvalidInventoryTransaction(
                    'Reversals must be created through the reversal operation.'
                );
            }

            if ($type === InventoryTransactionType::STOCK_ADJUSTMENT) {
                $this->assertAdjustmentEvidence($data);
            }

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($this->requiredId($data['product_id'] ?? null, 'product'));

            if (! $product->isActive()) {
                throw new InvalidInventoryTransaction('Only active products may be transacted.');
            }

            $this->assertProductUnit($product, $data['unit'] ?? null);

            $supplier = $this->lockedSupplier($data['supplier_id'] ?? null, $type);
            $stock = $this->lockedStock($product);
            $currentQuantity = $this->toMilliunits($stock->quantity, true, true);
            $transactionDate = $this->transactionDate($data['transaction_date'] ?? null);

            [$quantity, $direction] = $this->movement(
                $type,
                $data,
                $currentQuantity,
            );

            $signedQuantity = $direction->isInbound() ? $quantity : -$quantity;

            $this->assertStockIsAllowed(
                $product->getKey(),
                $transactionDate,
                $signedQuantity,
                $mayOverrideNegativeStock,
            );

            $transaction = InventoryTransaction::query()->create([
                'transaction_number' => $this->newTransactionNumber(),
                'transaction_date' => $transactionDate,
                'product_id' => $product->getKey(),
                'supplier_id' => $supplier?->getKey(),
                'transaction_type' => $type,
                'movement_direction' => $direction,
                'quantity' => $this->fromMilliunits($quantity),
                'unit' => $product->unit,
                'reference_type' => $this->nullableTrimmed($data['reference_type'] ?? null),
                'reference_number' => $this->nullableTrimmed($data['reference_number'] ?? null),
                'notes' => $this->nullableTrimmed($data['notes'] ?? null),
                'created_by' => $actor->getKey(),
            ]);

            $stock->forceFill([
                'quantity' => $this->fromMilliunits($currentQuantity + $signedQuantity),
            ])->save();

            try {
                app(\App\Services\StockAlertService::class)->checkProductStock($product);
            } catch (\Throwable) {}

            return $transaction->load(['product', 'supplier', 'createdBy']);
        }, attempts: 3);
    }

    /**
     * Reverse a ledger entry instead of editing or deleting it.
     */
    public function reverse(
        InventoryTransaction $transaction,
        User $actor,
        bool $mayOverrideNegativeStock = false,
        ?string $notes = null,
    ): InventoryTransaction {
        return DB::transaction(function () use ($transaction, $actor, $mayOverrideNegativeStock, $notes): InventoryTransaction {
            $original = InventoryTransaction::query()
                ->lockForUpdate()
                ->findOrFail($transaction->getKey());

            if ($original->transaction_type === InventoryTransactionType::REVERSAL) {
                throw new InvalidInventoryTransaction('A reversal cannot itself be reversed.');
            }

            if (InventoryTransaction::query()
                ->where('reversal_of_id', $original->getKey())
                ->exists()) {
                throw new InvalidInventoryTransaction('This transaction has already been reversed.');
            }

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($original->product_id);
            $stock = $this->lockedStock($product);
            $currentQuantity = $this->toMilliunits($stock->quantity, true, true);
            $quantity = $this->toMilliunits($original->quantity);
            $direction = $original->movement_direction->opposite();
            $signedQuantity = $direction->isInbound() ? $quantity : -$quantity;
            $transactionDate = CarbonImmutable::now();

            $this->assertStockIsAllowed(
                $product->getKey(),
                $transactionDate,
                $signedQuantity,
                $mayOverrideNegativeStock,
            );

            $reversal = InventoryTransaction::query()->create([
                'transaction_number' => $this->newTransactionNumber(),
                'transaction_date' => $transactionDate,
                'product_id' => $product->getKey(),
                'supplier_id' => $original->supplier_id,
                'transaction_type' => InventoryTransactionType::REVERSAL,
                'movement_direction' => $direction,
                'quantity' => $this->fromMilliunits($quantity),
                'unit' => $original->unit,
                'reference_type' => 'REVERSAL',
                'reference_number' => $original->transaction_number,
                'notes' => $this->nullableTrimmed($notes) ?? 'Reversal of '.$original->transaction_number,
                'created_by' => $actor->getKey(),
                'reversal_of_id' => $original->getKey(),
            ]);

            $stock->forceFill([
                'quantity' => $this->fromMilliunits($currentQuantity + $signedQuantity),
            ])->save();

            try {
                app(\App\Services\StockAlertService::class)->checkProductStock($product);
            } catch (\Throwable) {}

            return $reversal->load(['product', 'supplier', 'createdBy', 'reversalOf']);
        }, attempts: 3);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<InventoryTransaction>
     */
    private function queryWithRelations(): \Illuminate\Database\Eloquent\Builder
    {
        return InventoryTransaction::query()->with([
            'product',
            'supplier',
            'createdBy',
            'reversalOf',
        ]);
    }

    private function transactionType(mixed $value): InventoryTransactionType
    {
        if ($value instanceof InventoryTransactionType) {
            return $value;
        }

        $type = is_string($value) ? InventoryTransactionType::tryFrom($value) : null;

        if ($type === null) {
            throw new InvalidInventoryTransaction('The transaction type is invalid.');
        }

        return $type;
    }

    private function requiredId(mixed $value, string $name): int
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < 1) {
            throw new InvalidInventoryTransaction("A valid {$name} is required.");
        }

        return (int) $value;
    }

    private function lockedSupplier(mixed $supplierId, InventoryTransactionType $type): ?Supplier
    {
        if ($supplierId === null || $supplierId === '') {
            if ($type->requiresSupplier()) {
                throw new InvalidInventoryTransaction('A supplier is required for a goods receipt.');
            }

            return null;
        }

        $supplier = Supplier::query()
            ->lockForUpdate()
            ->findOrFail($this->requiredId($supplierId, 'supplier'));

        if (! $supplier->canBeUsed()) {
            throw new InvalidInventoryTransaction('Only active suppliers may be selected.');
        }

        return $supplier;
    }

    private function lockedStock(Product $product): InventoryStock
    {
        $stock = InventoryStock::query()
            ->where('product_id', $product->getKey())
            ->lockForUpdate()
            ->first();

        if ($stock !== null) {
            return $stock;
        }

        // The product row lock serializes creation for legacy products that
        // predate the inventory_stocks migration.
        InventoryStock::query()->create([
            'product_id' => $product->getKey(),
            'quantity' => '0.000',
        ]);

        return InventoryStock::query()
            ->where('product_id', $product->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * @param array<string, mixed> $data
     * @return array{0: int, 1: InventoryMovementDirection}
     */
    private function movement(
        InventoryTransactionType $type,
        array $data,
        int $currentQuantity,
    ): array {
        if ($type === InventoryTransactionType::STOCK_ADJUSTMENT) {
            $physicalQuantity = $this->toMilliunits(
                $data['physical_quantity'] ?? null,
                true,
            );
            $difference = $physicalQuantity - $currentQuantity;

            if ($difference === 0) {
                throw new InvalidInventoryTransaction(
                    'The physical quantity matches the current stock; no adjustment is needed.'
                );
            }

            return [
                abs($difference),
                $difference > 0
                    ? InventoryMovementDirection::IN
                    : InventoryMovementDirection::OUT,
            ];
        }

        $direction = $type->fixedDirection();

        if ($direction === null) {
            throw new InvalidInventoryTransaction('This transaction type cannot be created directly.');
        }

        return [
            $this->toMilliunits($data['quantity'] ?? null),
            $direction,
        ];
    }

    private function assertProductUnit(Product $product, mixed $unit): void
    {
        $submittedUnit = is_string($unit) ? strtoupper(trim($unit)) : '';

        if ($submittedUnit === '' || $submittedUnit !== $product->unit) {
            throw new InvalidInventoryTransaction(
                "The transaction unit must match the product unit ({$product->unit})."
            );
        }
    }

    private function transactionDate(mixed $value): CarbonImmutable
    {
        if (! is_string($value) && ! $value instanceof \DateTimeInterface) {
            throw new InvalidInventoryTransaction('A valid transaction date is required.');
        }

        try {
            $date = $value instanceof \DateTimeInterface
                ? CarbonImmutable::instance($value)
                : CarbonImmutable::parse($value);
        } catch (\Throwable) {
            throw new InvalidInventoryTransaction('A valid transaction date is required.');
        }

        if ($date->isFuture()) {
            throw new InvalidInventoryTransaction('A transaction date cannot be in the future.');
        }

        return $date;
    }

    private function assertStockIsAllowed(
        int $productId,
        CarbonImmutable $transactionDate,
        int $signedQuantity,
        bool $mayOverrideNegativeStock,
    ): void {
        if ($mayOverrideNegativeStock || $signedQuantity >= 0) {
            return;
        }

        $balance = 0;
        $entryApplied = false;

        $transactions = InventoryTransaction::query()
            ->forProduct($productId)
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get(['transaction_date', 'movement_direction', 'quantity']);

        foreach ($transactions as $transaction) {
            if (! $entryApplied && $transaction->transaction_date->greaterThan($transactionDate)) {
                $balance += $signedQuantity;
                $entryApplied = true;

                if ($balance < 0) {
                    throw new InsufficientInventoryStock(
                        'This transaction would make historical inventory stock negative.'
                    );
                }
            }

            $quantity = $this->toMilliunits($transaction->quantity);
            $balance += $transaction->movement_direction->isInbound()
                ? $quantity
                : -$quantity;

            if ($balance < 0) {
                throw new InsufficientInventoryStock(
                    'This transaction would make historical inventory stock negative.'
                );
            }
        }

        if (! $entryApplied) {
            $balance += $signedQuantity;
        }

        if ($balance < 0) {
            throw new InsufficientInventoryStock(
                'This transaction would make inventory stock negative.'
            );
        }
    }

    private function toMilliunits(
        mixed $value,
        bool $allowZero = false,
        bool $allowNegative = false,
    ): int
    {
        $quantity = is_int($value) || is_float($value) || is_string($value)
            ? trim((string) $value)
            : '';

        if (! preg_match('/^-?\d+(?:\.\d{1,3})?$/', $quantity)) {
            throw new InvalidInventoryTransaction('Quantity must have at most three decimal places.');
        }

        $isNegative = str_starts_with($quantity, '-');
        $quantity = ltrim($quantity, '-');
        [$whole, $fraction] = array_pad(explode('.', $quantity, 2), 2, '');
        $milliunits = ((int) $whole * 1000) + (int) str_pad($fraction, 3, '0');

        if ($isNegative) {
            $milliunits *= -1;
        }

        if ((! $allowNegative && $milliunits < 0) || (! $allowZero && $milliunits === 0)) {
            throw new InvalidInventoryTransaction('Quantity must be greater than zero.');
        }

        return $milliunits;
    }

    private function fromMilliunits(int $milliunits): string
    {
        $sign = $milliunits < 0 ? '-' : '';
        $milliunits = abs($milliunits);

        return sprintf('%s%d.%03d', $sign, intdiv($milliunits, 1000), $milliunits % 1000);
    }

    private function newTransactionNumber(): string
    {
        return 'INV-'.Str::ulid()->toBase32();
    }

    private function nullableTrimmed(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /** @param array<string, mixed> $data */
    private function assertAdjustmentEvidence(array $data): void
    {
        foreach (['reference_type', 'reference_number', 'notes'] as $field) {
            if ($this->nullableTrimmed($data[$field] ?? null) === null) {
                throw new InvalidInventoryTransaction(
                    'Stock adjustments require a reference type, reference number, and reason.'
                );
            }
        }
    }
}
