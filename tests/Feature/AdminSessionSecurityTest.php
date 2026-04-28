<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSessionSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_sets_admin_tab_bootstrap_flag(): void
    {
        $user = User::query()->create([
            'nip' => '123456789012345678',
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => 'rahasia123',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'nip' => $user->nip,
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('admin.index'));
        $this->assertAuthenticatedAs($user);
        $response->assertSessionHas('admin_tab_bootstrap_pending', true);
    }

    public function test_tab_close_route_logs_user_out(): void
    {
        $user = User::query()->create([
            'nip' => '123456789012345678',
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => 'rahasia123',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('logout.tab-close'));

        $response->assertNoContent();
        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_too_many_attempts(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', [
                'nip' => '123456789012345678',
                'password' => 'salah-password',
            ]);
        }

        $response = $this->post('/login', [
            'nip' => '123456789012345678',
            'password' => 'salah-password',
        ]);

        $response->assertStatus(429);
    }
}
