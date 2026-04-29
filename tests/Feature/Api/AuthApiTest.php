<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_and_returns_token(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/v1/register', [
            'name' => 'API User',
            'email' => 'apiuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['user' => ['id', 'email', 'role'], 'token'])
            ->assertJsonPath('user.email', 'apiuser@example.com')
            ->assertJsonPath('user.role', 'student');

        $user = User::where('email', 'apiuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_register_validates_role(): void
    {
        $this->postJson('/api/v1/register', [
            'name' => 'X',
            'email' => 'x@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'hacker',
        ])->assertUnprocessable()->assertJsonValidationErrors('role');
    }

    public function test_login_with_valid_credentials_returns_token(): void
    {
        $user = User::factory()->student()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('secret-pass'),
        ]);

        $this->postJson('/api/v1/login', [
            'email' => 'login@example.com',
            'password' => 'secret-pass',
        ])
            ->assertOk()
            ->assertJsonStructure(['user', 'token'])
            ->assertJsonPath('user.id', $user->id);
    }

    public function test_login_with_wrong_password_fails(): void
    {
        User::factory()->student()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('secret-pass'),
        ]);

        $this->postJson('/api/v1/login', [
            'email' => 'login@example.com',
            'password' => 'wrong',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_me_requires_auth(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_me_returns_current_user(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->student()->create();
        $token = $user->createToken('api');
        $tokenId = $token->accessToken->id;

        $this->withHeader('Authorization', "Bearer {$token->plainTextToken}")
            ->postJson('/api/v1/logout')
            ->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
    }
}
