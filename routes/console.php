<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:auth', function () {
    $this->line("config email: " . var_export(config('inventory.initial_admin.email'), true));
    $this->line("config password: " . var_export(config('inventory.initial_admin.password'), true));
    try {
        $seeder = new \Database\Seeders\UserSeeder();
        $seeder->run();
        $this->info("Seeder executed successfully");
    } catch (\Throwable $e) {
        $this->error("Seeder error: " . $e->getMessage());
    }
    $users = \App\Models\User::all();
    $this->info("Total users: " . $users->count());
    foreach ($users as $user) {
        $this->line("User ID: {$user->id} | Name: {$user->name} | Email: '{$user->email}'");
        $check1 = \Illuminate\Support\Facades\Hash::check('Password123!', $user->password) ? 'YES' : 'NO';
        $check2 = \Illuminate\Support\Facades\Hash::check('password', $user->password) ? 'YES' : 'NO';
        $attempt1 = \Illuminate\Support\Facades\Auth::attempt(['email' => $user->email, 'password' => 'Password123!']) ? 'SUCCESS' : 'FAILED';
        $attempt2 = \Illuminate\Support\Facades\Auth::attempt(['email' => $user->email, 'password' => 'password']) ? 'SUCCESS' : 'FAILED';
        $this->line("  Hash check 'Password123!': $check1 | Auth::attempt: $attempt1");
        $this->line("  Hash check 'password': $check2 | Auth::attempt: $attempt2");
    }
});
