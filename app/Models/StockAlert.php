<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'alert_type',
        'current_stock',
        'threshold',
        'is_acknowledged',
        'acknowledged_by',
        'acknowledged_at',
    ];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'threshold' => 'decimal:3',
        'is_acknowledged' => 'boolean',
        'acknowledged_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeUnacknowledged(Builder $query): Builder
    {
        return $query->where('is_acknowledged', false);
    }

    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeAcknowledged(Builder $query): Builder
    {
        return $query->where('is_acknowledged', true);
    }

    public function label(): string
    {
        return match ($this->alert_type) {
            'out_of_stock' => 'Stok Habis',
            'below_safety' => 'Di Bawah Safety Stock',
            'below_rop' => 'Mencapai Reorder Point',
            default => 'Unknown',
        };
    }

    public function badgeColor(): string
    {
        return match ($this->alert_type) {
            'out_of_stock', 'below_safety' => 'rose',
            'below_rop' => 'amber',
            default => 'slate',
        };
    }
}
