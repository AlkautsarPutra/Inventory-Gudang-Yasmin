<?php

declare(strict_types=1);

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware('can:manage-master-data')->group(function (): void {
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
    });

    Route::get('inventory-transactions', [InventoryTransactionController::class, 'index'])
        ->name('inventory-transactions.index');

    Route::middleware('can:access-forecasting')->prefix('forecasts')->name('forecasts.')->group(function (): void {
        Route::get('/', [ForecastController::class, 'index'])->name('index');
        Route::get('/{product}', [ForecastController::class, 'show'])->name('show');
        Route::post('/calculate', [ForecastController::class, 'calculate'])->name('calculate');
        Route::post('/calculate-all', [ForecastController::class, 'calculateAll'])->name('calculate-all');
    });

    Route::middleware('can:record-inventory')->group(function (): void {
        Route::get('inventory-transactions/create', [InventoryTransactionController::class, 'create'])
            ->name('inventory-transactions.create');
        Route::post('inventory-transactions', [InventoryTransactionController::class, 'store'])
            ->name('inventory-transactions.store');

        Route::post('alerts/{stockAlert}/acknowledge', [\App\Http\Controllers\StockAlertController::class, 'acknowledge'])->name('alerts.acknowledge');
        Route::post('alerts/check', [\App\Http\Controllers\StockAlertController::class, 'checkNow'])->name('alerts.check');
    });

    Route::get('alerts', [\App\Http\Controllers\StockAlertController::class, 'index'])->name('alerts.index');

    Route::get('inventory-transactions/{inventoryTransaction}', [InventoryTransactionController::class, 'show'])
        ->name('inventory-transactions.show');

    Route::post('inventory-transactions/{inventoryTransaction}/reverse', [InventoryTransactionController::class, 'reverse'])
        ->middleware('can:reverse-inventory')
        ->name('inventory-transactions.reverse');

    Route::middleware('can:manage-users')->group(function (): void {
        Route::resource('users', UserController::class)->only([
            'index', 'create', 'store', 'edit', 'update',
        ]);
    });
});
