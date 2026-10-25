<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SupplierStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Supplier master data.
 */
class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'company_name',
        'tax_identification_number',
        'contact_person',
        'phone_number',
        'mobile_number',
        'email',
        'website',
        'street_address',
        'city',
        'province',
        'postal_code',
        'country',
        'supplier_category',
        'business_type',
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
            'status' => SupplierStatus::class,
        ];
    }

    /**
     * Get all inventory transactions supplied by this supplier.
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Determine whether the supplier is active.
     */
    public function isActive(): bool
    {
        return $this->status === SupplierStatus::ACTIVE;
    }

    /**
     * Determine whether the supplier can be selected for new transactions.
     */
    public function canBeUsed(): bool
    {
        return $this->status->isUsable();
    }

    /**
     * Get the preferred supplier display name.
     */
    public function getDisplayName(): string
    {
        return $this->company_name ?: $this->supplier_name;
    }
}
