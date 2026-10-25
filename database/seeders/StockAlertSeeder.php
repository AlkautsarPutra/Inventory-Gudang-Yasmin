<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\StockAlert;
use App\Models\User;
use App\Services\StockAlertService;
use Illuminate\Database\Seeder;

class StockAlertSeeder extends Seeder
{
    /**
     * Seed stock alerts based on current inventory balances.
     */
    public function run(): void
    {
        $alertService = app(StockAlertService::class);
        $alertService->checkAllProducts();

        $admin = User::query()->where('role', 'admin')->first();

        // Ensure we have both active and acknowledged alerts for demo
        $alerts = StockAlert::query()->where('is_acknowledged', false)->get();
        if ($alerts->count() > 2 && $admin !== null) {
            foreach ($alerts->take(2) as $alert) {
                $alertService->acknowledge($alert, $admin);
            }
        }
    }
}
