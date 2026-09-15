<?php


namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile(): void
    {
        $this
            ->getJson('/api/profile')
            ->assertStatus(401);
    }

    public function test_authenticated_user_can_create_profile(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/profile', [
                'first_name' => 'Ferdous',
                'last_name' => 'Ahmed',
                'headline' => 'Software Developer',
                'phone' => '+967700000000',
                'country' => 'Yemen',
                'city' => 'Sana’a',
                'website_url' => 'https://example.com',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'github_url' => 'https://github.com/example',
                'portfolio_url' => 'https://portfolio.example.com',
            ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Profile created successfully.',
            ])
            ->assertJsonPath('data.profile.first_name', 'Ferdous')
            ->assertJsonPath('data.profile.last_name', 'Ahmed');

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'first_name' => 'Ferdous',
            'last_name' => 'Ahmed',
        ]);
    }

    public function test_authenticated_user_can_view_own_profile(): void
    {
        $user = User::factory()->create();

        $user->profile()->create([
            'first_name' => 'Ferdous',
            'last_name' => 'Ahmed',
            'headline' => 'Software Developer',
        ]);

        $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/profile')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.profile.first_name', 'Ferdous')
            ->assertJsonPath('data.profile.last_name', 'Ahmed');
    }

    public function test_authenticated_user_can_update_own_profile(): void
    {
        $user = User::factory()->create();

        $user->profile()->create([
            'first_name' => 'Ferdous',
            'last_name' => 'Ahmed',
            'headline' => 'Software Developer',
        ]);

        $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/profile', [
                'first_name' => 'Ferdous',
                'last_name' => 'Nasser',
                'headline' => 'Senior Software Developer',
                'city' => 'Sana’a',
            ])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully.',
            ])
            ->assertJsonPath('data.profile.last_name', 'Nasser')
            ->assertJsonPath(
                'data.profile.headline',
                'Senior Software Developer'
            );

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'last_name' => 'Nasser',
            'headline' => 'Senior Software Developer',
            'city' => 'Sana’a',
        ]);
    }

    public function test_user_cannot_create_second_profile(): void
    {
        $user = User::factory()->create();

        $user->profile()->create([
            'first_name' => 'Ferdous',
        ]);

        $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/profile', [
                'first_name' => 'Another Name',
            ])
            ->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'Profile already exists.',
            ]);

        $this->assertDatabaseCount('profiles', 1);
    }

    public function test_profile_creation_requires_first_name(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/profile', [
                'last_name' => 'Ahmed',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
            ]);
    }

    public function test_profile_rejects_invalid_urls(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/profile', [
                'first_name' => 'Ferdous',
                'website_url' => 'not-a-valid-url',
                'linkedin_url' => 'invalid-url',
                'github_url' => 'invalid-url',
                'portfolio_url' => 'invalid-url',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'website_url',
                'linkedin_url',
                'github_url',
                'portfolio_url',
            ]);
    }
}
