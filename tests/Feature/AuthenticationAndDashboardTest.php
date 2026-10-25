<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_sign_in_and_reach_the_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.test',
            'password' => Hash::make('a-secure-test-password'),
            'role' => UserRole::VIEWER,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'a-secure-test-password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_a_user_can_sign_in_with_case_insensitive_and_whitespace_email(): void
    {
        $user = User::factory()->create([
            'email' => 'loginuser@example.test',
            'password' => Hash::make('password123456'),
            'role' => UserRole::VIEWER,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => '  LoginUser@EXAMPLE.test  ',
            'password' => 'password123456',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_only_admins_can_record_and_reverse_inventory(): void
    {
        $viewer = User::factory()->create(['role' => UserRole::VIEWER]);
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->assertFalse($viewer->can('record-inventory'));
        $this->assertFalse($viewer->can('reverse-inventory'));
        $this->assertFalse($viewer->can('override-negative-stock'));
        $this->assertFalse($viewer->can('access-forecasting'));
        $this->assertTrue($admin->can('record-inventory'));
        $this->assertTrue($admin->can('reverse-inventory'));
        $this->assertTrue($admin->can('override-negative-stock'));
        $this->assertTrue($admin->can('access-forecasting'));
    }
}
