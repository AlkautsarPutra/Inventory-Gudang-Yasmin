<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-users', static fn (User $user): bool => $user->role->canManageUsers());
        Gate::define('manage-master-data', static fn (User $user): bool => $user->role->canManageMasterData());
        Gate::define('record-inventory', static fn (User $user): bool => $user->role->canRecordInventory());
        Gate::define('reverse-inventory', static fn (User $user): bool => $user->role->canReverseInventory());
        Gate::define('override-negative-stock', static fn (User $user): bool => $user->role->canOverrideNegativeStock());
        Gate::define('access-forecasting', static fn (User $user): bool => $user->role->canAccessForecasting());
    }
}
