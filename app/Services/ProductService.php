<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryStock;
use App\Enums\ProductStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Get paginated products.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with('inventoryStock')
            ->orderBy('product_name')
            ->paginate($perPage);
    }

    /**
     * Create a new product.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Product
    {
        return DB::transaction(
            static function () use ($data): Product {
                $product = Product::query()->create($data);

                InventoryStock::query()->create([
                    'product_id' => $product->getKey(),
                    'quantity' => '0.000',
                ]);

                return $product;
            }
        );
    }

    /**
     * Update an existing product.
     *
     * @param array<string, mixed> $data
     */
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(
            static function () use ($product, $data): Product {
                $product->update($data);

                return $product->refresh();
            }
        );
    }

    /**
     * Delete a product.
     */
    public function delete(Product $product): void
    {
        DB::transaction(
            static function () use ($product): void {
                $product->update(['status' => ProductStatus::INACTIVE]);
            }
        );
    }

    /**
     * Find a product by ID.
     */
    public function find(int $id): Product
    {
        return Product::query()->findOrFail($id);
    }
}
