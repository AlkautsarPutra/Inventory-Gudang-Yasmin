<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DemandForecast;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Enums\InventoryMovementDirection;
use App\Enums\ProductStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ForecastingService
{
    /**
     * Get monthly outbound (usage/demand) data for a product.
     * Returns an array of ['period' => 'YYYY-MM', 'total' => float] sorted chronologically.
     *
     * @return array<int, array{period: string, total: float}>
     */
    public function getMonthlyUsage(int $productId, int $months = 12): array
    {
        $startDate = CarbonImmutable::now()->subMonths($months)->startOfMonth();

        $dateFormatSql = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', transaction_date)"
            : "DATE_FORMAT(transaction_date, '%Y-%m')";

        $results = InventoryTransaction::query()
            ->where('product_id', $productId)
            ->where('movement_direction', InventoryMovementDirection::OUT)
            ->where('transaction_date', '>=', $startDate)
            ->selectRaw("{$dateFormatSql} as period")
            ->selectRaw('SUM(quantity) as total')
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Fill in zero-months for gaps
        $allMonths = [];
        $current = $startDate;
        $end = CarbonImmutable::now()->startOfMonth();
        while ($current->lte($end)) {
            $allMonths[$current->format('Y-m')] = 0.0;
            $current = $current->addMonth();
        }

        foreach ($results as $row) {
            $allMonths[$row->period] = (float) $row->total;
        }

        $data = [];
        foreach ($allMonths as $period => $total) {
            $data[] = ['period' => $period, 'total' => $total];
        }

        return $data;
    }

    /**
     * Calculate Single Exponential Smoothing.
     *
     * Formula: F(t+1) = α * X(t) + (1 - α) * F(t)
     *
     * @param  array<int, float>  $data  Actual demand values per period
     * @param  float  $alpha  Smoothing constant (0 < α < 1)
     * @return array{forecasts: array<int, float>, next_forecast: float, alpha: float, mae: float, mape: float}
     */
    public function calculateSES(array $data, float $alpha = 0.3): array
    {
        if (count($data) < 2) {
            $lastValue = count($data) === 1 ? $data[0] : 0.0;
            return [
                'forecasts' => [],
                'next_forecast' => $lastValue,
                'alpha' => $alpha,
                'mae' => 0.0,
                'mape' => 0.0,
            ];
        }

        // Initialize first forecast with first actual value
        $forecasts = [];
        $forecasts[0] = $data[0];

        // Calculate forecasts for subsequent periods
        for ($t = 1; $t < count($data); $t++) {
            $forecasts[$t] = $alpha * $data[$t - 1] + (1 - $alpha) * $forecasts[$t - 1];
        }

        // Next period forecast
        $lastIndex = count($data) - 1;
        $nextForecast = $alpha * $data[$lastIndex] + (1 - $alpha) * $forecasts[$lastIndex];

        // Calculate MAE and MAPE
        $absoluteErrors = [];
        $percentageErrors = [];

        for ($t = 1; $t < count($data); $t++) {
            $error = abs($data[$t] - $forecasts[$t]);
            $absoluteErrors[] = $error;

            if ($data[$t] > 0) {
                $percentageErrors[] = ($error / $data[$t]) * 100;
            }
        }

        $mae = count($absoluteErrors) > 0 ? array_sum($absoluteErrors) / count($absoluteErrors) : 0.0;
        $mape = count($percentageErrors) > 0 ? array_sum($percentageErrors) / count($percentageErrors) : 0.0;

        return [
            'forecasts' => $forecasts,
            'next_forecast' => round($nextForecast, 3),
            'alpha' => $alpha,
            'mae' => round($mae, 3),
            'mape' => round($mape, 4),
        ];
    }

    /**
     * Find the optimal alpha by minimizing MAE across a range of values.
     *
     * @param  array<int, float>  $data
     */
    public function findOptimalAlpha(array $data, float $step = 0.01): float
    {
        if (count($data) < 3) {
            return 0.3;
        }

        $bestAlpha = 0.1;
        $bestMae = PHP_FLOAT_MAX;

        for ($alpha = 0.01; $alpha <= 0.99; $alpha += $step) {
            $result = $this->calculateSES($data, round($alpha, 2));
            if ($result['mae'] < $bestMae) {
                $bestMae = $result['mae'];
                $bestAlpha = round($alpha, 2);
            }
        }

        return $bestAlpha;
    }

    /**
     * Run SES forecast for a specific product and persist results.
     *
     * @return array{product: Product, ses_result: array, monthly_usage: array, forecasts_saved: int}
     */
    public function forecastProduct(int $productId, ?float $alpha = null, int $months = 12): array
    {
        $product = Product::query()->findOrFail($productId);
        $monthlyUsage = $this->getMonthlyUsage($productId, $months);
        $demandValues = array_column($monthlyUsage, 'total');

        if ($alpha === null) {
            $alpha = $this->findOptimalAlpha($demandValues);
        }

        $sesResult = $this->calculateSES($demandValues, $alpha);

        // Persist forecast records
        $savedCount = 0;
        foreach ($monthlyUsage as $index => $usage) {
            DemandForecast::query()->updateOrCreate(
                [
                    'product_id' => $productId,
                    'period' => $usage['period'],
                ],
                [
                    'actual_demand' => $usage['total'],
                    'forecasted_demand' => $sesResult['forecasts'][$index] ?? $usage['total'],
                    'alpha' => $alpha,
                    'mae' => $sesResult['mae'],
                    'mape' => $sesResult['mape'],
                ]
            );
            $savedCount++;
        }

        // Save next period forecast
        $nextPeriod = CarbonImmutable::now()->addMonth()->format('Y-m');
        DemandForecast::query()->updateOrCreate(
            [
                'product_id' => $productId,
                'period' => $nextPeriod,
            ],
            [
                'actual_demand' => null,
                'forecasted_demand' => $sesResult['next_forecast'],
                'alpha' => $alpha,
                'mae' => $sesResult['mae'],
                'mape' => $sesResult['mape'],
            ]
        );
        $savedCount++;

        return [
            'product' => $product,
            'ses_result' => $sesResult,
            'monthly_usage' => $monthlyUsage,
            'forecasts_saved' => $savedCount,
        ];
    }

    /**
     * Calculate Safety Stock for a product.
     *
     * Formula: SS = Z × σd × √L
     *
     * @return array{safety_stock: float, std_deviation: float, lead_time_days: int, z_score: float, avg_daily_demand: float}
     */
    public function calculateSafetyStock(
        int $productId,
        float $serviceLevel = 0.95,
        ?int $leadTimeDays = null,
    ): array {
        $product = Product::query()->findOrFail($productId);
        $leadTime = $leadTimeDays ?? $product->lead_time_days ?? 7;

        // Z-score lookup for common service levels
        $zScore = $this->zScoreForServiceLevel($serviceLevel);

        // Get daily demand data for standard deviation calculation
        $dailyDemands = $this->getDailyDemand($productId, 90); // last 90 days

        $stdDeviation = $this->standardDeviation($dailyDemands);
        $avgDailyDemand = count($dailyDemands) > 0
            ? array_sum($dailyDemands) / count($dailyDemands)
            : 0.0;

        // SS = Z × σd × √L
        $safetyStock = $zScore * $stdDeviation * sqrt((float) $leadTime);

        return [
            'safety_stock' => round($safetyStock, 3),
            'std_deviation' => round($stdDeviation, 3),
            'lead_time_days' => $leadTime,
            'z_score' => $zScore,
            'avg_daily_demand' => round($avgDailyDemand, 3),
        ];
    }

    /**
     * Calculate and auto-update both Safety Stock (minimum_stock) and ROP (reorder_point) for a product.
     *
     * ROP = (d × L) + SS
     *
     * @return array{safety_stock: float, reorder_point: float, avg_daily_demand: float, lead_time_days: int, product: Product}
     */
    public function calculateAndUpdateStockPolicy(
        int $productId,
        float $serviceLevel = 0.95,
        ?int $leadTimeDays = null,
    ): array {
        $ssResult = $this->calculateSafetyStock($productId, $serviceLevel, $leadTimeDays);

        // ROP = (d × L) + SS
        $reorderPoint = ($ssResult['avg_daily_demand'] * $ssResult['lead_time_days']) + $ssResult['safety_stock'];
        $reorderPoint = round($reorderPoint, 3);

        // Update product
        $product = Product::query()->findOrFail($productId);
        $product->update([
            'minimum_stock' => (int) ceil($ssResult['safety_stock']),
            'reorder_point' => (int) ceil($reorderPoint),
        ]);

        return [
            'safety_stock' => $ssResult['safety_stock'],
            'reorder_point' => $reorderPoint,
            'avg_daily_demand' => $ssResult['avg_daily_demand'],
            'lead_time_days' => $ssResult['lead_time_days'],
            'product' => $product->refresh(),
        ];
    }

    /**
     * Get daily outbound quantities for a product over the last N days.
     *
     * @return array<int, float>
     */
    private function getDailyDemand(int $productId, int $days = 90): array
    {
        $startDate = CarbonImmutable::now()->subDays($days)->startOfDay();

        $results = InventoryTransaction::query()
            ->where('product_id', $productId)
            ->where('movement_direction', InventoryMovementDirection::OUT)
            ->where('transaction_date', '>=', $startDate)
            ->selectRaw('DATE(transaction_date) as day')
            ->selectRaw('SUM(quantity) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Fill zeros for days with no outbound
        $dailyData = [];
        $current = $startDate;
        $end = CarbonImmutable::now()->startOfDay();

        while ($current->lte($end)) {
            $dailyData[$current->format('Y-m-d')] = 0.0;
            $current = $current->addDay();
        }

        foreach ($results as $row) {
            $dailyData[$row->day] = (float) $row->total;
        }

        return array_values($dailyData);
    }

    /**
     * Calculate population standard deviation.
     *
     * @param  array<int, float>  $values
     */
    private function standardDeviation(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }

        $mean = array_sum($values) / $n;
        $sumSquaredDiffs = 0.0;

        foreach ($values as $value) {
            $sumSquaredDiffs += ($value - $mean) ** 2;
        }

        return sqrt($sumSquaredDiffs / $n);
    }

    /**
     * Map service level to Z-score.
     */
    private function zScoreForServiceLevel(float $serviceLevel): float
    {
        return match (true) {
            $serviceLevel >= 0.99 => 2.33,
            $serviceLevel >= 0.975 => 1.96,
            $serviceLevel >= 0.95 => 1.65,
            $serviceLevel >= 0.90 => 1.28,
            $serviceLevel >= 0.85 => 1.04,
            $serviceLevel >= 0.80 => 0.84,
            default => 1.65,
        };
    }
}
