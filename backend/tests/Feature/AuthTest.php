<?php


namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Account created successfully.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user',
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user',
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'login@example.com',
        ]);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->getJson('/api/auth/me');

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                    ],
                ],
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                ],
            ]);
    }

    public function test_guest_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }


public function test_user_can_logout_and_token_is_revoked(): void
{
    $user = User::factory()->create();

    $accessToken = $user->createToken('test-token');

    $token = $accessToken->plainTextToken;
    $tokenId = $accessToken->accessToken->id;

    // Token should work before logout.
    $this
        ->withToken($token)
        ->getJson('/api/auth/me')
        ->assertStatus(200);

    // Token must exist before logout.
    $this->assertDatabaseHas('personal_access_tokens', [
        'id' => $tokenId,
    ]);

    // Logout using the token.
    $response = $this
        ->withToken($token)
        ->postJson('/api/auth/logout');

    $response
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);

    // Logout must delete the current Sanctum token.
    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenId,
    ]);
}




}

