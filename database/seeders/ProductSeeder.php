<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the products table with verified master products from Excel reports.
     */
    public function run(): void
    {
        $filePath = database_path('data/products.json');
        if (! file_exists($filePath)) {
            return;
        }

        /** @var array<int, array<string, mixed>>|null $products */
        $products = json_decode((string) file_get_contents($filePath), true);
        if (! is_array($products)) {
            return;
        }

        foreach ($products as $productData) {
            Product::query()->updateOrCreate(
                ['product_code' => $productData['product_code']],
                $productData
            );
        }
    }
}
