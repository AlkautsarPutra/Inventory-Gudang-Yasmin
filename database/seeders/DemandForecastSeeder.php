<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Services\ForecastingService;
use Illuminate\Database\Seeder;

class DemandForecastSeeder extends Seeder
{
    /**
     * Seed Single Exponential Smoothing demand forecasts for products.
     */
    public function run(): void
    {
        $service = app(ForecastingService::class);
        $products = Product::query()
            ->where('status', ProductStatus::ACTIVE)
            ->orderBy('id')
            ->get();

        foreach ($products as $product) {
            try {
                $service->forecastProduct($product->id);
                $service->calculateAndUpdateStockPolicy($product->id, 0.95);
            } catch (\Throwable $e) {
                // If a product lacks enough historical transactions, skip gracefully
                continue;
            }
        }
    }
}
