<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleAndVerificationGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'student']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_role_middleware_blocks_wrong_role(): void
    {
        Route::middleware(['auth', 'role:admin'])
            ->get('/_test/admin-only', fn () => 'ok');

        $student = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($student)->get('/_test/admin-only')->assertForbidden();
        $this->actingAs($admin)->get('/_test/admin-only')->assertOk();
    }
}
