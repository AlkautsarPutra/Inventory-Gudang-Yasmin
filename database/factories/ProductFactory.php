<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Enums\WarehouseType;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $minimumStock = fake()->numberBetween(5, 20);

        $maximumStock = fake()->numberBetween(
            $minimumStock + 30,
            500
        );

        $reorderPoint = fake()->numberBetween(
            $minimumStock,
            min($minimumStock + 20, $maximumStock)
        );

        return [

            /*
            |--------------------------------------------------------------------------
            | Product Identity
            |--------------------------------------------------------------------------
            */

            'product_code' => 'PRD-' . fake()->unique()->numerify('######'),

            'product_name' => fake()->words(
                fake()->numberBetween(2, 4),
                true
            ),

            /*
            |--------------------------------------------------------------------------
            | Product Information
            |--------------------------------------------------------------------------
            */

            'description' => fake()->optional()->sentence(),

            'category' => fake()->randomElement([
                'Bibit Parfum',
                'Pelarut & Campuran',
                'Penguat Aroma (Fixative)',
                'Botol & Kemasan',
                'Eau De Parfum (EDP)',
                'Roll-On Perfume Oil',
            ]),

            'brand' => fake()->randomElement([
                'Yasmin Golden Care',
                'Mane',
                'Firmenich',
                'Givaudan',
                'Symrise',
                'IFF',
                'Brataco Chemical',
                'Multi Indokemas',
                'Aroma Nusantara',
            ]),

            'unit' => fake()->randomElement([
                'PCS',
                'LTR',
                'KG',
                'SET',
                'BTL',
            ]),

            'warehouse_type' => fake()->randomElement(WarehouseType::cases()),

            /*
            |--------------------------------------------------------------------------
            | Inventory Policy
            |--------------------------------------------------------------------------
            */

            'minimum_stock' => $minimumStock,

            'maximum_stock' => $maximumStock,

            'reorder_point' => $reorderPoint,

            'lead_time_days' => fake()->numberBetween(3, 14),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' => ProductStatus::ACTIVE,
        ];
    }
}
