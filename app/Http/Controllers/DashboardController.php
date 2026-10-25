<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $products = Product::query()->with('inventoryStock')->get();
        $lowStockProducts = $products->filter(
            static fn (Product $product): bool => $product->needsReorder(
                (int) floor((float) ($product->inventoryStock?->quantity ?? 0))
            )
        );

        $rawMaterialCount = $products->where('warehouse_type', \App\Enums\WarehouseType::RAW_MATERIAL)->count();
        $finishedGoodsCount = $products->where('warehouse_type', \App\Enums\WarehouseType::FINISHED_GOODS)->count();

        $rawMaterialLowStockCount = $products->where('warehouse_type', \App\Enums\WarehouseType::RAW_MATERIAL)
            ->filter(fn (Product $product): bool => $product->needsReorder((int) floor((float) ($product->inventoryStock?->quantity ?? 0))))->count();

        $finishedGoodsLowStockCount = $products->where('warehouse_type', \App\Enums\WarehouseType::FINISHED_GOODS)
            ->filter(fn (Product $product): bool => $product->needsReorder((int) floor((float) ($product->inventoryStock?->quantity ?? 0))))->count();

        $activeAlerts = \App\Models\StockAlert::unacknowledged()->with('product')->latest()->take(5)->get();
        $upcomingForecasts = \App\Models\DemandForecast::whereNull('actual_demand')->with('product')->take(5)->get();

        return view('dashboard', [
            'productCount' => $products->count(),
            'activeSupplierCount' => Supplier::query()->where('status', 'ACTIVE')->count(),
            'lowStockProducts' => $lowStockProducts,
            'recentTransactions' => InventoryTransaction::query()
                ->with(['product', 'createdBy'])
                ->latestTransaction()
                ->limit(8)
                ->get(),
            'rawMaterialCount' => $rawMaterialCount,
            'finishedGoodsCount' => $finishedGoodsCount,
            'rawMaterialLowStockCount' => $rawMaterialLowStockCount,
            'finishedGoodsLowStockCount' => $finishedGoodsLowStockCount,
            'activeAlerts' => $activeAlerts,
            'upcomingForecasts' => $upcomingForecasts,
        ]);
    }
}
