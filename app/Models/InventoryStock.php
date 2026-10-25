<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Materialized current inventory balance for one product.
 *
 * This table is a projection of the immutable inventory ledger. It is only
 * mutated by InventoryTransactionService inside the same database transaction
 * that records a ledger entry.
 */
class InventoryStock extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'product_id',
        'quantity',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
        ];
    }

    /**
     * Get the product whose balance is represented.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
