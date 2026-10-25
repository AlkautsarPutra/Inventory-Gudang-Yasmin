<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InventoryMovementDirection;
use App\Enums\InventoryTransactionType;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryTransaction>
 */
class InventoryTransactionFactory extends Factory
{
    protected $model = InventoryTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $transactionType = fake()->randomElement(
            InventoryTransactionType::cases()
        );

        $movementDirection = match ($transactionType) {
            InventoryTransactionType::INITIAL_STOCK,
            InventoryTransactionType::GOODS_RECEIPT
                => InventoryMovementDirection::IN,

            InventoryTransactionType::GOODS_ISSUE
                => InventoryMovementDirection::OUT,

            InventoryTransactionType::STOCK_ADJUSTMENT
                => fake()->randomElement([
                    InventoryMovementDirection::IN,
                    InventoryMovementDirection::OUT,
                ]),
        };

        $referenceType = match ($transactionType) {
            InventoryTransactionType::INITIAL_STOCK
                => 'INITIAL',

            InventoryTransactionType::GOODS_RECEIPT
                => 'PURCHASE_ORDER',

            InventoryTransactionType::GOODS_ISSUE
                => 'WORK_ORDER',

            InventoryTransactionType::STOCK_ADJUSTMENT
                => 'ADJUSTMENT',
        };

        $referenceNumber = match ($referenceType) {
            'INITIAL'
                => 'INIT-' . fake()->numerify('######'),

            'PURCHASE_ORDER'
                => 'PO-' . fake()->numerify('######'),

            'WORK_ORDER'
                => 'WO-' . fake()->numerify('######'),

            'ADJUSTMENT'
                => 'ADJ-' . fake()->numerify('######'),
        };

        return [
            'transaction_number' => 'TRX-' . fake()->unique()->numerify('########'),

            'transaction_date' => fake()->dateTimeBetween('-1 year', 'now'),

            'product_id' => Product::factory(),

            'supplier_id' => $transactionType === InventoryTransactionType::GOODS_RECEIPT
                ? Supplier::factory()
                : null,

            'transaction_type' => $transactionType,

            'movement_direction' => $movementDirection,

            'quantity' => fake()->randomFloat(
                3,
                1,
                500
            ),

            'unit' => fake()->randomElement([
                'PCS',
                'BOX',
                'SET',
                'LTR',
                'KG',
            ]),

            'reference_type' => $referenceType,

            'reference_number' => $referenceNumber,

            'notes' => fake()->optional()->sentence(),

            'created_by' => User::factory(),
        ];
    }
}
