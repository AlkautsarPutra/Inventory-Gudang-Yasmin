<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Supplier;
use App\Enums\SupplierStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    /**
     * Get paginated suppliers ordered by company name.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Supplier::query()
            ->orderBy('company_name')
            ->paginate($perPage);
    }

    /**
     * Find a supplier by its primary key.
     */
    public function find(int $id): Supplier
    {
        return Supplier::query()->findOrFail($id);
    }

    /**
     * Find a supplier by supplier code.
     */
    public function findByCode(string $supplierCode): Supplier
    {
        return Supplier::query()
            ->where('supplier_code', $supplierCode)
            ->firstOrFail();
    }

    /**
     * Create a new supplier.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Supplier
    {
        return DB::transaction(
            static fn (): Supplier => Supplier::query()->create($data)
        );
    }

    /**
     * Update an existing supplier.
     *
     * @param array<string, mixed> $data
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        return DB::transaction(
            static function () use ($supplier, $data): Supplier {
                $supplier->update($data);

                return $supplier->refresh();
            }
        );
    }

    /**
     * Delete a supplier.
     */
    public function delete(Supplier $supplier): void
    {
        DB::transaction(
            static function () use ($supplier): void {
                $supplier->update(['status' => SupplierStatus::INACTIVE]);
            }
        );
    }
}
