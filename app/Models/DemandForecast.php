<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class DemandForecast extends Model
{
    protected $fillable = [
        'product_id',
        'period',
        'actual_demand',
        'forecasted_demand',
        'alpha',
        'mae',
        'mape',
    ];

    protected function casts(): array
    {
        return [
            'actual_demand'     => 'decimal:3',
            'forecasted_demand' => 'decimal:3',
            'alpha'             => 'decimal:4',
            'mae'               => 'decimal:3',
            'mape'              => 'decimal:4',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to the next forecast period (current month or next month).
     */
    public function scopeForNextPeriod(Builder $query): Builder
    {
        $currentPeriod = now()->format('Y-m');
        $nextPeriod = now()->addMonth()->format('Y-m');

        return $query->whereIn('period', [$currentPeriod, $nextPeriod])
            ->orderByDesc('period');
    }

    /**
     * Scope forecasts for a specific product.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    public function getPeriodDateAttribute(): \Carbon\CarbonImmutable
    {
        try {
            return \Carbon\CarbonImmutable::createFromFormat('Y-m', $this->period)->startOfMonth();
        } catch (\Throwable) {
            return \Carbon\CarbonImmutable::now()->startOfMonth();
        }
    }

    public function getForecastDemandAttribute(): float
    {
        return (float) ($this->forecasted_demand ?? 0.0);
    }

    public function getSafetyStockAttribute(): float
    {
        return (float) ($this->product?->minimum_stock ?? 0.0);
    }
}
