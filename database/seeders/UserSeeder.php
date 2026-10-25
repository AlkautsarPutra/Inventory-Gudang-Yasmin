<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table.
     */
    public function run(): void
    {
        $email = config('inventory.initial_admin.email');
        $password = config('inventory.initial_admin.password');

        if (! is_string($email) || ! is_string($password) || $email === '' || $password === '') {
            $email = 'admin@yasmingoldencare.com';
            $password = 'Password123!';
        }

        /*
        |--------------------------------------------------------------------------
        | Default Administrator
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => $email,
            ],
            [
                'name' => config('inventory.initial_admin.name', 'System Administrator'),
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'email_verified_at' => now(),
                'role' => UserRole::ADMIN,
            ]
        );

        if ($email !== 'admin@mail.com') {
            User::updateOrCreate(
                [
                    'email' => 'admin@mail.com',
                ],
                [
                    'name' => 'Administrator',
                    'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                    'email_verified_at' => now(),
                    'role' => UserRole::ADMIN,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sample Operational Users
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'pengelola@yasmingoldencare.com',
            ],
            [
                'name' => 'Pengelola Gudang',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
                'role' => UserRole::ADMIN,
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'viewer@yasmingoldencare.com',
            ],
            [
                'name' => 'Staff Viewer',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
                'role' => UserRole::VIEWER,
            ]
        );
    }
}
