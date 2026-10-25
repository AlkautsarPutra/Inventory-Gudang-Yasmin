<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\InventoryTransactionType;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryTransactionService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class InventoryTransactionSeeder extends Seeder
{
    /**
     * Seed a valid inventory ledger through the domain service.
     */
    public function run(): void
    {
        $products = Product::query()->orderBy('id')->get();
        $suppliers = Supplier::query()->where('status', 'ACTIVE')->get();
        $actor = User::query()->where('role', 'admin')->first();

        if ($products->isEmpty() || $suppliers->isEmpty() || $actor === null) {
            return;
        }

        $service = app(InventoryTransactionService::class);
        $now = CarbonImmutable::now();
        $startDate = $now->subMonths(12)->startOfMonth();

        // 1. Initial balance 12 months ago
        foreach ($products as $index => $product) {
            $service->create([
                'transaction_date' => $startDate->addHours($index % 24),
                'product_id' => $product->getKey(),
                'transaction_type' => InventoryTransactionType::INITIAL_STOCK,
                'quantity' => (string) fake()->numberBetween(100, 300),
                'unit' => $product->unit,
                'reference_type' => 'INITIAL',
                'reference_number' => 'INIT-'.str_pad((string) $product->getKey(), 6, '0', STR_PAD_LEFT),
                'notes' => 'Saldo awal persediaan parfum & bahan baku Toko Parfum Yasmin Golden Care.',
            ], $actor);
        }

        // 2. Monotonically increasing monthly transactions for past 12 months
        for ($monthOffset = 11; $monthOffset >= 0; $monthOffset--) {
            $monthStart = $now->subMonths($monthOffset)->startOfMonth();
            $maxDay = $monthOffset === 0 ? max(2, $now->day - 2) : $monthStart->daysInMonth;

            // Day 2 of each month: Goods Receipt
            $receiptDate = $monthStart->addDays(1)->setHour(10)->setMinute(0);
            if ($receiptDate->lt($now)) {
                foreach ($products as $product) {
                    $curStock = (float) $service->currentStockForProduct($product->getKey());
                    if ($curStock < ($product->maximum_stock * 0.6)) {
                        $qty = fake()->numberBetween(40, 80);
                        $service->create([
                            'transaction_date' => $receiptDate,
                            'product_id' => $product->getKey(),
                            'transaction_type' => InventoryTransactionType::GOODS_RECEIPT,
                            'supplier_id' => $suppliers->random()->getKey(),
                            'quantity' => (string) $qty,
                            'unit' => $product->unit,
                            'reference_type' => 'PURCHASE_ORDER',
                            'reference_number' => 'PO-'.fake()->unique()->numerify('######'),
                            'notes' => 'Penerimaan bibit parfum & bahan kemasan dari supplier resmi.',
                        ], $actor);
                    }
                }
            }

            // Weeks 2, 3, 4 of each month: Goods Issue (Outbound)
            $issueDays = [8, 15, 22];
            foreach ($issueDays as $day) {
                if ($day > $maxDay) {
                    continue;
                }
                $issueDate = $monthStart->addDays($day - 1)->setHour(14)->setMinute(0);
                if ($issueDate->gte($now)) {
                    continue;
                }

                foreach ($products as $product) {
                    $curStock = (float) $service->currentStockForProduct($product->getKey());
                    $safeIssue = (int) floor(min($curStock - 5, fake()->numberBetween(5, 20)));
                    if ($safeIssue > 0) {
                        $service->create([
                            'transaction_date' => $issueDate,
                            'product_id' => $product->getKey(),
                            'transaction_type' => InventoryTransactionType::GOODS_ISSUE,
                            'quantity' => (string) $safeIssue,
                            'unit' => $product->unit,
                            'reference_type' => 'WORK_ORDER',
                            'reference_number' => 'WO-'.fake()->unique()->numerify('######'),
                            'notes' => 'Pengeluaran bahan untuk produksi racikan parfum / pesanan outlet.',
                        ], $actor);
                    }
                }
            }
        }

        // 3. Demo Stock Alerts:
        // Adjust 3 products at the very end of timeline (yesterday)
        $masterProducts = $products->take(5);
        if ($masterProducts->count() >= 3) {
            $adjustDate = $now->subHours(12);

            // Out of stock
            $p0 = $masterProducts[0];
            $service->create([
                'transaction_date' => $adjustDate,
                'product_id' => $p0->getKey(),
                'transaction_type' => InventoryTransactionType::STOCK_ADJUSTMENT,
                'physical_quantity' => '0',
                'unit' => $p0->unit,
                'reference_type' => 'CYCLE_COUNT',
                'reference_number' => 'CC-'.fake()->unique()->numerify('######'),
                'notes' => 'Penyesuaian stok opname fisik habis (Demo Stockout).',
            ], $actor);

            // Critical below safety stock
            $p1 = $masterProducts[1];
            $targetStock1 = max(1, (int) floor($p1->minimum_stock / 2));
            $service->create([
                'transaction_date' => $adjustDate->addMinutes(5),
                'product_id' => $p1->getKey(),
                'transaction_type' => InventoryTransactionType::STOCK_ADJUSTMENT,
                'physical_quantity' => (string) $targetStock1,
                'unit' => $p1->unit,
                'reference_type' => 'CYCLE_COUNT',
                'reference_number' => 'CC-'.fake()->unique()->numerify('######'),
                'notes' => 'Penyesuaian stok opname kritis di bawah safety stock.',
            ], $actor);

            // Reorder point reached
            $p2 = $masterProducts[2];
            $targetStock2 = max($p2->minimum_stock + 1, (int) floor($p2->reorder_point - 2));
            $service->create([
                'transaction_date' => $adjustDate->addMinutes(10),
                'product_id' => $p2->getKey(),
                'transaction_type' => InventoryTransactionType::STOCK_ADJUSTMENT,
                'physical_quantity' => (string) $targetStock2,
                'unit' => $p2->unit,
                'reference_type' => 'CYCLE_COUNT',
                'reference_number' => 'CC-'.fake()->unique()->numerify('######'),
                'notes' => 'Penyesuaian stok fisik mendekati batas reorder point (ROP).',
            ], $actor);
        }
    }
}
