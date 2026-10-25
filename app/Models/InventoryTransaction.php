<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InventoryMovementDirection;
use App\Enums\InventoryTransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Inventory transaction ledger.
 */
class InventoryTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'product_id',
        'supplier_id',
        'transaction_type',
        'movement_direction',
        'quantity',
        'unit',
        'reference_type',
        'reference_number',
        'notes',
        'created_by',
        'reversal_of_id',
    ];

    /**
     * Get the model's attribute casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'quantity' => 'decimal:3',
            'transaction_type' => InventoryTransactionType::class,
            'movement_direction' => InventoryMovementDirection::class,
        ];
    }

    /**
     * Get the related product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the related supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user who created the transaction.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the transaction that this entry reverses.
     */
    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_of_id');
    }

    /**
     * Get the reversal created for this transaction, if one exists.
     */
    public function reversal(): HasOne
    {
        return $this->hasOne(self::class, 'reversal_of_id');
    }

    /**
     * Scope inbound inventory transactions.
     */
    public function scopeInbound(Builder $query): Builder
    {
        return $query->where(
            'movement_direction',
            InventoryMovementDirection::IN
        );
    }

    /**
     * Scope outbound inventory transactions.
     */
    public function scopeOutbound(Builder $query): Builder
    {
        return $query->where(
            'movement_direction',
            InventoryMovementDirection::OUT
        );
    }

    /**
     * Scope transactions by product.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope transactions by supplier.
     */
    public function scopeForSupplier(Builder $query, int $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Scope transactions by type.
     */
    public function scopeOfType(
        Builder $query,
        InventoryTransactionType $type
    ): Builder {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope transactions within a date range.
     */
    public function scopeForDateRange(
        Builder $query,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): Builder {
        return $query->whereBetween(
            'transaction_date',
            [$startDate, $endDate]
        );
    }

    /**
     * Scope transactions ordered from newest to oldest.
     */
    public function scopeLatestTransaction(Builder $query): Builder
    {
        return $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');
    }
}
