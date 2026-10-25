<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\WarehouseType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Product master data.
 */
class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_code',
        'product_name',
        'description',
        'category',
        'brand',
        'unit',
        'warehouse_type',
        'minimum_stock',
        'maximum_stock',
        'reorder_point',
        'lead_time_days',
        'status',
    ];

    /**
     * Get the model's attribute casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'minimum_stock' => 'integer',
            'maximum_stock' => 'integer',
            'reorder_point' => 'integer',
            'lead_time_days' => 'integer',
            'status' => ProductStatus::class,
            'warehouse_type' => WarehouseType::class,
        ];
    }

    /**
     * Get all demand forecasts for the product.
     */
    public function demandForecasts(): HasMany
    {
        return $this->hasMany(DemandForecast::class);
    }

    /**
     * Get all inventory transactions for the product.
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Get the materialized current inventory balance.
     */
    public function inventoryStock(): HasOne
    {
        return $this->hasOne(InventoryStock::class);
    }

    /**
     * Determine whether the product is active.
     */
    public function isActive(): bool
    {
        return $this->status === ProductStatus::ACTIVE;
    }

    /**
     * Determine whether the product has reached its reorder point.
     */
    public function needsReorder(int $currentStock): bool
    {
        return $currentStock <= $this->reorder_point;
    }

    public function scopeRawMaterial(Builder $query): Builder
    {
        return $query->where('warehouse_type', WarehouseType::RAW_MATERIAL);
    }

    public function scopeFinishedGoods(Builder $query): Builder
    {
        return $query->where('warehouse_type', WarehouseType::FINISHED_GOODS);
    }

    public function isRawMaterial(): bool
    {
        return $this->warehouse_type === WarehouseType::RAW_MATERIAL;
    }

    public function isFinishedGoods(): bool
    {
        return $this->warehouse_type === WarehouseType::FINISHED_GOODS;
    }

    public function getNameAttribute(): string
    {
        return $this->product_name;
    }

    public function getSkuAttribute(): string
    {
        return $this->product_code;
    }

    public function getUnitOfMeasureAttribute(): string
    {
        return $this->unit;
    }
}
