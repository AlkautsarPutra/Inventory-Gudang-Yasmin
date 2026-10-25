<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Inventory\Exceptions\InsufficientInventoryStock;
use App\Domain\Inventory\Exceptions\InvalidInventoryTransaction;
use App\Enums\InventoryMovementDirection;
use App\Enums\InventoryTransactionType;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_goods_receipt_updates_the_immutable_ledger_and_current_stock(): void
    {
        $product = Product::factory()->create(['unit' => 'PCS']);
        $supplier = Supplier::factory()->create();
        $actor = User::factory()->create();

        $transaction = app(InventoryTransactionService::class)->create([
            'transaction_date' => now()->subDay(),
            'product_id' => $product->getKey(),
            'supplier_id' => $supplier->getKey(),
            'transaction_type' => InventoryTransactionType::GOODS_RECEIPT,
            'quantity' => '12.500',
            'unit' => 'PCS',
            'reference_type' => 'PURCHASE_ORDER',
            'reference_number' => 'PO-001',
        ], $actor);

        $this->assertSame(InventoryMovementDirection::IN, $transaction->movement_direction);
        $this->assertStringStartsWith('INV-', $transaction->transaction_number);
        $this->assertSame('12.500', InventoryStock::query()
            ->where('product_id', $product->getKey())
            ->firstOrFail()
            ->quantity);
    }

    public function test_issue_cannot_make_current_or_historical_stock_negative(): void
    {
        $product = Product::factory()->create(['unit' => 'PCS']);
        $actor = User::factory()->create();
        $service = app(InventoryTransactionService::class);

        $service->create([
            'transaction_date' => now()->subDays(2),
            'product_id' => $product->getKey(),
            'transaction_type' => InventoryTransactionType::INITIAL_STOCK,
            'quantity' => '5',
            'unit' => 'PCS',
        ], $actor);

        $this->expectException(InsufficientInventoryStock::class);

        $service->create([
            'transaction_date' => now()->subDay(),
            'product_id' => $product->getKey(),
            'transaction_type' => InventoryTransactionType::GOODS_ISSUE,
            'quantity' => '5.001',
            'unit' => 'PCS',
        ], $actor);
    }

    public function test_stock_adjustment_derives_its_movement_from_physical_quantity(): void
    {
        $product = Product::factory()->create(['unit' => 'PCS']);
        $actor = User::factory()->create();
        $service = app(InventoryTransactionService::class);

        $service->create([
            'transaction_date' => now()->subDay(),
            'product_id' => $product->getKey(),
            'transaction_type' => InventoryTransactionType::INITIAL_STOCK,
            'quantity' => '10',
            'unit' => 'PCS',
        ], $actor);

        $adjustment = $service->create([
            'transaction_date' => now(),
            'product_id' => $product->getKey(),
            'transaction_type' => InventoryTransactionType::STOCK_ADJUSTMENT,
            'physical_quantity' => '7.250',
            'unit' => 'PCS',
            'reference_type' => 'CYCLE_COUNT',
            'reference_number' => 'CC-001',
            'notes' => 'Cycle count discrepancy.',
        ], $actor);

        $this->assertSame(InventoryMovementDirection::OUT, $adjustment->movement_direction);
        $this->assertSame('2.750', $adjustment->quantity);
        $this->assertSame('7.250', InventoryStock::query()
            ->where('product_id', $product->getKey())
            ->firstOrFail()
            ->quantity);
    }

    public function test_reversal_creates_a_new_opposite_entry_without_mutating_the_original(): void
    {
        $product = Product::factory()->create(['unit' => 'PCS']);
        $actor = User::factory()->create();
        $service = app(InventoryTransactionService::class);

        $original = $service->create([
            'transaction_date' => now()->subDay(),
            'product_id' => $product->getKey(),
            'transaction_type' => InventoryTransactionType::INITIAL_STOCK,
            'quantity' => '10',
            'unit' => 'PCS',
        ], $actor);

        $reversal = $service->reverse($original, $actor);

        $this->assertSame(InventoryTransactionType::REVERSAL, $reversal->transaction_type);
        $this->assertSame(InventoryMovementDirection::OUT, $reversal->movement_direction);
        $this->assertSame($original->getKey(), $reversal->reversal_of_id);
        $this->assertSame('10.000', $original->fresh()->quantity);
        $this->assertSame('0.000', InventoryStock::query()
            ->where('product_id', $product->getKey())
            ->firstOrFail()
            ->quantity);
    }

    public function test_transaction_unit_must_match_the_product_master_unit(): void
    {
        $product = Product::factory()->create(['unit' => 'PCS']);
        $actor = User::factory()->create();

        $this->expectException(InvalidInventoryTransaction::class);

        app(InventoryTransactionService::class)->create([
            'transaction_date' => now(),
            'product_id' => $product->getKey(),
            'transaction_type' => InventoryTransactionType::INITIAL_STOCK,
            'quantity' => '1',
            'unit' => 'BOX',
        ], $actor);
    }
}
