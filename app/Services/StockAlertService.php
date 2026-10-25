<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\StockAlert;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StockAlertService
{
    public function checkProductStock(Product $product): ?StockAlert
    {
        $currentStock = (float) ($product->inventoryStock?->quantity ?? 0);
        $minStock = (float) $product->minimum_stock;
        $rop = (float) $product->reorder_point;

        $alertType = null;
        $threshold = 0.0;

        if ($currentStock <= 0) {
            $alertType = 'out_of_stock';
            $threshold = 0.0;
        } elseif ($currentStock <= $minStock && $minStock > 0) {
            $alertType = 'below_safety';
            $threshold = $minStock;
        } elseif ($currentStock <= $rop && $rop > 0) {
            $alertType = 'below_rop';
            $threshold = $rop;
        }

        if ($alertType !== null) {
            $existingAlert = StockAlert::unacknowledged()
                ->where('product_id', $product->id)
                ->where('alert_type', $alertType)
                ->first();

            if ($existingAlert) {
                $existingAlert->update(['current_stock' => $currentStock]);
                return $existingAlert;
            }

            return StockAlert::create([
                'product_id' => $product->id,
                'alert_type' => $alertType,
                'current_stock' => $currentStock,
                'threshold' => $threshold,
                'is_acknowledged' => false,
            ]);
        }

        // If stock is healthy, resolve older alerts?
        // Let's resolve previous unacknowledged alerts for this product if stock > rop
        if ($currentStock > $rop) {
            StockAlert::unacknowledged()
                ->where('product_id', $product->id)
                ->update([
                    'is_acknowledged' => true,
                    'acknowledged_at' => now(),
                    // We don't have an actor here, keep acknowledged_by null or a system user
                ]);
        }

        return null;
    }

    public function checkAllProducts(): int
    {
        $products = Product::with('inventoryStock')
            ->where('status', \App\Enums\ProductStatus::ACTIVE)
            ->get();
        $count = 0;

        foreach ($products as $product) {
            $alert = $this->checkProductStock($product);
            if ($alert && $alert->wasRecentlyCreated) {
                $count++;
            }
        }

        return $count;
    }

    public function acknowledge(StockAlert $alert, User $actor): StockAlert
    {
        $alert->update([
            'is_acknowledged' => true,
            'acknowledged_by' => $actor->id,
            'acknowledged_at' => now(),
        ]);

        return $alert;
    }

    /**
     * @return Collection<int, StockAlert>
     */
    public function getUnacknowledgedAlerts(): Collection
    {
        return StockAlert::unacknowledged()->with(['product'])->latest()->get();
    }

    /**
     * @return LengthAwarePaginator<StockAlert>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return StockAlert::with(['product', 'acknowledgedBy'])
            ->orderBy('is_acknowledged', 'asc')
            ->latest()
            ->paginate($perPage);
    }
}
