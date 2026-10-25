<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DemandForecast;
use App\Models\Product;
use App\Services\ForecastingService;
use App\Enums\ProductStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForecastController extends Controller
{
    public function __construct(
        private readonly ForecastingService $forecastingService,
    ) {}

    /**
     * Display forecast overview for all active products.
     */
    public function index(): View
    {
        $products = Product::query()
            ->where('status', ProductStatus::ACTIVE)
            ->with(['inventoryStock'])
            ->withCount('inventoryTransactions')
            ->orderBy('product_name')
            ->get();

        // Get latest forecast for each product
        $latestForecasts = DemandForecast::query()
            ->whereIn('product_id', $products->pluck('id'))
            ->whereNull('actual_demand') // next period forecasts
            ->get()
            ->keyBy('product_id');

        return view('forecasts.index', compact('products', 'latestForecasts'));
    }

    /**
     * Display detailed forecast for a specific product.
     */
    public function show(Product $product): View
    {
        $monthlyUsage = $this->forecastingService->getMonthlyUsage($product->id, 12);
        $demandValues = array_column($monthlyUsage, 'total');
        $optimalAlpha = $this->forecastingService->findOptimalAlpha($demandValues);
        $sesResult = $this->forecastingService->calculateSES($demandValues, $optimalAlpha);
        $safetyStockResult = $this->forecastingService->calculateSafetyStock($product->id);

        $forecasts = DemandForecast::query()
            ->where('product_id', $product->id)
            ->orderBy('period')
            ->get();

        return view('forecasts.show', compact(
            'product',
            'monthlyUsage',
            'sesResult',
            'safetyStockResult',
            'optimalAlpha',
            'forecasts',
        ));
    }

    /**
     * Run SES forecast calculation for a specific product.
     */
    public function calculate(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'alpha' => ['nullable', 'numeric', 'min:0.01', 'max:0.99'],
            'service_level' => ['nullable', 'numeric', 'min:0.80', 'max:0.99'],
        ]);

        $alpha = $request->filled('alpha') ? (float) $request->input('alpha') : null;
        $serviceLevel = $request->filled('service_level') ? (float) $request->input('service_level') : 0.95;
        $productId = (int) $request->input('product_id');

        // Run SES forecast
        $this->forecastingService->forecastProduct($productId, $alpha);

        // Calculate and update Safety Stock + ROP
        $this->forecastingService->calculateAndUpdateStockPolicy($productId, $serviceLevel);

        return redirect()
            ->route('forecasts.show', $productId)
            ->with('success', 'Peramalan dan perhitungan Safety Stock / ROP berhasil diperbarui.');
    }

    /**
     * Run forecast for ALL active products.
     */
    public function calculateAll(Request $request): RedirectResponse
    {
        $request->validate([
            'service_level' => ['nullable', 'numeric', 'min:0.80', 'max:0.99'],
        ]);

        $serviceLevel = $request->filled('service_level') ? (float) $request->input('service_level') : 0.95;

        $products = Product::query()
            ->where('status', ProductStatus::ACTIVE)
            ->get();

        $count = 0;
        foreach ($products as $product) {
            $this->forecastingService->forecastProduct($product->id);
            $this->forecastingService->calculateAndUpdateStockPolicy($product->id, $serviceLevel);
            $count++;
        }

        return redirect()
            ->route('forecasts.index')
            ->with('success', "Peramalan berhasil dihitung untuk {$count} produk.");
    }
}
