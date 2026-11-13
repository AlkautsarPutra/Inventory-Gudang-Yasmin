<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Seed the suppliers table from the verified supplier dataset.
     */
    public function run(): void
    {
        $filePath = database_path('data/suppliers.json');
        if (! file_exists($filePath)) {
            return;
        }

        /** @var array<int, array<string, mixed>>|null $suppliers */
        $suppliers = json_decode((string) file_get_contents($filePath), true);
        if (! is_array($suppliers)) {
            return;
        }

        foreach ($suppliers as $supplierData) {
            Supplier::query()->updateOrCreate(
                ['supplier_code' => $supplierData['supplier_code']],
                $supplierData
            );
        }
    }
}
