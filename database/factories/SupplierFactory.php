<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SupplierStatus;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $companyName = fake()->company();

        return [

            /*
            |--------------------------------------------------------------------------
            | Supplier Identity
            |--------------------------------------------------------------------------
            */

            'supplier_code' => 'SUP-' . fake()->unique()->numerify('######'),

            'supplier_name' => fake()->name(),

            'company_name' => $companyName,

            /*
            |--------------------------------------------------------------------------
            | Business Information
            |--------------------------------------------------------------------------
            */

            'tax_identification_number' => fake()->optional()->numerify(
                '##.###.###.#-###.###'
            ),

            'supplier_category' => fake()->randomElement([
                'Manufacturer',
                'Distributor',
                'Wholesaler',
                'Retailer',
            ]),

            'business_type' => fake()->randomElement([
                'Local',
                'International',
            ]),

            /*
            |--------------------------------------------------------------------------
            | Contact Information
            |--------------------------------------------------------------------------
            */

            'contact_person' => fake()->name(),

            'phone_number' => fake()->phoneNumber(),

            'mobile_number' => fake()->phoneNumber(),

            'email' => fake()->unique()->safeEmail(),

            'website' => fake()->optional()->url(),

            /*
            |--------------------------------------------------------------------------
            | Business Address
            |--------------------------------------------------------------------------
            */

            'street_address' => fake()->streetAddress(),

            'city' => fake()->city(),

            'province' => fake()->state(),

            'postal_code' => fake()->postcode(),

            'country' => fake()->country(),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' => SupplierStatus::ACTIVE,
        ];
    }
}
