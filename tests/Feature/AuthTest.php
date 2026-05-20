<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ── Registration ─────────────────────────────────────────────────────────

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'user',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_registration_requires_min_8_password(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test',
            'email'                 => 'short@example.com',
            'password'              => 'abc123',  // only 6 chars
            'password_confirmation' => 'abc123',
            'role'                  => 'user',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post('/logout')
             ->assertRedirect('/login');

        $this->assertGuest();
    }

    // ── API Logout (Sanctum token revocation) ─────────────────────────────────

    public function test_api_logout_revokes_token(): void
    {
        $user      = User::factory()->create();
        $tokenResult = $user->createToken('test');
        $plainToken  = $tokenResult->plainTextToken;
        $tokenId     = $tokenResult->accessToken->id;

        $response = $this->withToken($plainToken)
            ->postJson('/api/logout');

        $response->assertOk()
                 ->assertJson(['success' => true]);

        // Verify the personal access token record was deleted from DB
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
    }

    // ── Forgot password ───────────────────────────────────────────────────────

    public function test_forgot_password_page_is_accessible(): void
    {
        $this->get('/forgot-password')->assertOk();
    }

    public function test_forgot_password_sends_reset_link_for_existing_email(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        // Should redirect back with 'status' flash (regardless of mail driver)
        $response->assertRedirect();
    }

    // ── Reset password ────────────────────────────────────────────────────────

    public function test_reset_password_page_is_accessible(): void
    {
        $this->get('/reset-password/fake-token?email=test@example.com')
             ->assertOk();
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create();

        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/login');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
