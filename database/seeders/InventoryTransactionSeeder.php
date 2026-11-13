<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryTransactionSeeder extends Seeder
{
    /**
     * Seed inventory transactions and synchronize materialized stock balances.
     */
    public function run(): void
    {
        $filePath = database_path('data/inventory_transactions.json');
        if (! file_exists($filePath)) {
            return;
        }

        /** @var array<int, array<string, mixed>>|null $rawTransactions */
        $rawTransactions = json_decode((string) file_get_contents($filePath), true);
        if (! is_array($rawTransactions) || empty($rawTransactions)) {
            return;
        }

        $products = Product::query()->get()->keyBy('product_code');
        $suppliers = Supplier::query()->get()->keyBy('supplier_code');
        $actor = User::query()->where('role', 'admin')->first() ?? User::query()->first();

        if ($products->isEmpty() || $actor === null) {
            return;
        }

        $now = now();
        $records = [];

        foreach ($rawTransactions as $tx) {
            $productCode = (string) ($tx['product_code'] ?? '');
            if (! isset($products[$productCode])) {
                continue;
            }

            $product = $products[$productCode];
            $supplierCode = $tx['supplier_code'] ?? null;
            $supplierId = ($supplierCode && isset($suppliers[$supplierCode]))
                ? $suppliers[$supplierCode]->id
                : null;

            $records[] = [
                'transaction_number' => (string) $tx['transaction_number'],
                'transaction_date' => (string) $tx['transaction_date'],
                'product_id' => $product->id,
                'supplier_id' => $supplierId,
                'transaction_type' => (string) $tx['transaction_type'],
                'movement_direction' => (string) $tx['movement_direction'],
                'quantity' => (string) $tx['quantity'],
                'unit' => (string) ($tx['unit'] ?? $product->unit),
                'reference_type' => $tx['reference_type'] ?? null,
                'reference_number' => $tx['reference_number'] ?? null,
                'notes' => $tx['notes'] ?? null,
                'created_by' => $actor->id,
                'reversal_of_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert in chunks within a transaction for speed and reliability
        DB::transaction(function () use ($records, $products): void {
            foreach (array_chunk($records, 200) as $chunk) {
                DB::table('inventory_transactions')->insert($chunk);
            }

            // Materialize stock balances
            foreach ($products as $product) {
                $balance = DB::table('inventory_transactions')
                    ->where('product_id', $product->id)
                    ->selectRaw("COALESCE(SUM(CASE WHEN movement_direction = 'IN' THEN quantity ELSE -quantity END), 0) AS bal")
                    ->value('bal') ?? '0.000';

                InventoryStock::query()->updateOrCreate(
                    ['product_id' => $product->id],
                    ['quantity' => $balance]
                );
            }
        });
    }
}
