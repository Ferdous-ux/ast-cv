<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_owner_can_login_to_admin(): void
    {
        $owner = User::factory()->create([
            'email' => 'owner@astcv.local',
            'password' => Hash::make('password'),
        ]);

        $ownerRole = Role::create([
            'name' => 'Owner',
            'slug' => 'owner',
            'is_system' => true,
        ]);

        $owner->roles()->attach($ownerRole);

        $response = $this->post('/admin/login', [
            'email' => 'owner@astcv.local',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');

        $this->assertAuthenticated('web');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $owner = User::factory()->create([
            'email' => 'owner@astcv.local',
            'password' => Hash::make('password'),
        ]);

        $ownerRole = Role::create([
            'name' => 'Owner',
            'slug' => 'owner',
            'is_system' => true,
        ]);

        $owner->roles()->attach($ownerRole);

        $response = $this->post('/admin/login', [
            'email' => 'owner@astcv.local',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }

    public function test_user_without_admin_permission_is_forbidden(): void
    {
        $user = User::factory()->create([
            'email' => 'user@astcv.local',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user, 'web');

        $response = $this->get('/admin');

        $response->assertForbidden();
    }

    public function test_logout_ends_admin_session(): void
    {
        $owner = User::factory()->create([
            'email' => 'owner@astcv.local',
            'password' => Hash::make('password'),
        ]);

        $ownerRole = Role::create([
            'name' => 'Owner',
            'slug' => 'owner',
            'is_system' => true,
        ]);

        $owner->roles()->attach($ownerRole);

        $this->actingAs($owner, 'web');

        $this->assertAuthenticated('web');

        $response = $this->post('/admin/logout');

        $response->assertRedirect('/admin/login');

        $this->assertGuest('web');
    }
}