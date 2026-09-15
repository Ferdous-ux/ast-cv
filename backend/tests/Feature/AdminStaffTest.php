<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminStaffTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function createRole(
        string $name,
        string $slug,
        bool $isSystem = false
    ): Role {
        return Role::create([
            'name' => $name,
            'slug' => $slug,
            'description' => "{$name} role",
            'is_system' => $isSystem,
        ]);
    }

    private function createPermission(
        string $name,
        string $slug,
        string $group = 'staff'
    ): Permission {
        return Permission::create([
            'name' => $name,
            'slug' => $slug,
            'group' => $group,
            'description' => "{$name} permission",
        ]);
    }

    private function makeOwner(): User
    {
        $user = User::factory()->create();

        $ownerRole = $this->createRole(
            'Owner',
            'owner',
            true
        );

        $user->roles()->attach($ownerRole);

        return $user;
    }

    private function makeUserWithPermission(
        string $permissionSlug
    ): User {
        $user = User::factory()->create();

        $role = $this->createRole(
            'Test Admin',
            'test-admin'
        );

        $permission = $this->createPermission(
            'Test Permission',
            $permissionSlug
        );

        $role->permissions()->attach($permission);

        $user->roles()->attach($role);

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_staff_api(): void
    {
        $response = $this->getJson('/api/admin/staff');

        $response->assertUnauthorized();
    }

    /*
    |--------------------------------------------------------------------------
    | Staff Listing
    |--------------------------------------------------------------------------
    */

    public function test_owner_can_list_staff(): void
    {
        $owner = $this->makeOwner();

        User::factory()->count(3)->create();

        Sanctum::actingAs($owner);

        $response = $this->getJson('/api/admin/staff');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'data',
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ]);

        $response->assertJsonCount(
            4,
            'data.data'
        );
    }

    public function test_user_without_staff_view_permission_cannot_list_staff(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/staff');

        $response->assertForbidden();
    }

    public function test_user_with_staff_view_permission_can_list_staff(): void
    {
        $user = $this->makeUserWithPermission(
            'staff.view'
        );

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/staff');

        $response->assertOk();
    }

    /*
    |--------------------------------------------------------------------------
    | Staff Details
    |--------------------------------------------------------------------------
    */

    public function test_owner_can_view_staff_details(): void
    {
        $owner = $this->makeOwner();

        $staff = User::factory()->create();

        Sanctum::actingAs($owner);

        $response = $this->getJson(
            "/api/admin/staff/{$staff->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $staff->id
            );
    }

    public function test_user_without_staff_view_permission_cannot_view_staff_details(): void
    {
        $user = User::factory()->create();

        $staff = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/admin/staff/{$staff->id}"
        );

        $response->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | Create Staff
    |--------------------------------------------------------------------------
    */

    public function test_owner_can_create_staff(): void
    {
        $owner = $this->makeOwner();

        Sanctum::actingAs($owner);

        $response = $this->postJson(
            '/api/admin/staff',
            [
                'name' => 'New Staff',
                'email' => 'newstaff@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'status' => 'active',
            ]
        );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'data.name',
                'New Staff'
            )
            ->assertJsonPath(
                'data.email',
                'newstaff@example.com'
            );

        $this->assertDatabaseHas('users', [
            'email' => 'newstaff@example.com',
            'name' => 'New Staff',
            'status' => 'active',
        ]);
    }

    public function test_user_without_create_permission_cannot_create_staff(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/admin/staff',
            [
                'name' => 'Unauthorized Staff',
                'email' => 'unauthorized@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ]
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => 'unauthorized@example.com',
        ]);
    }

    public function test_staff_create_requires_valid_data(): void
    {
        $owner = $this->makeOwner();

        Sanctum::actingAs($owner);

        $response = $this->postJson(
            '/api/admin/staff',
            []
        );

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'name',
            'email',
            'password',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Staff
    |--------------------------------------------------------------------------
    */

    public function test_owner_can_update_staff(): void
    {
        $owner = $this->makeOwner();

        $staff = User::factory()->create([
            'name' => 'Old Name',
        ]);

        Sanctum::actingAs($owner);

        $response = $this->putJson(
            "/api/admin/staff/{$staff->id}",
            [
                'name' => 'Updated Name',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.name',
                'Updated Name'
            );

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_user_without_update_permission_cannot_update_staff(): void
    {
        $user = User::factory()->create();

        $staff = User::factory()->create([
            'name' => 'Original Name',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson(
            "/api/admin/staff/{$staff->id}",
            [
                'name' => 'Hacked Name',
            ]
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'name' => 'Original Name',
        ]);
    }

    public function test_staff_with_update_permission_can_update_staff(): void
    {
        $manager = $this->makeUserWithPermission(
            'staff.update'
        );

        $staff = User::factory()->create([
            'name' => 'Original Name',
        ]);

        Sanctum::actingAs($manager);

        $response = $this->putJson(
            "/api/admin/staff/{$staff->id}",
            [
                'name' => 'Updated By Manager',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.name',
                'Updated By Manager'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Owner Protection
    |--------------------------------------------------------------------------
    */

    public function test_non_owner_cannot_update_owner(): void
    {
        $owner = $this->makeOwner();

        $manager = $this->makeUserWithPermission(
            'staff.update'
        );

        Sanctum::actingAs($manager);

        $response = $this->putJson(
            "/api/admin/staff/{$owner->id}",
            [
                'name' => 'Attempted Owner Change',
            ]
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'id' => $owner->id,
            'name' => 'Attempted Owner Change',
        ]);
    }

    public function test_owner_cannot_be_deleted(): void
    {
        $owner = $this->makeOwner();

        Sanctum::actingAs($owner);

        $response = $this->deleteJson(
            "/api/admin/staff/{$owner->id}"
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $owner->id,
        ]);
    }

    public function test_owner_can_delete_regular_staff(): void
    {
        $owner = $this->makeOwner();

        $staff = User::factory()->create();

        Sanctum::actingAs($owner);

        $response = $this->deleteJson(
            "/api/admin/staff/{$staff->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Staff member deleted successfully.',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $staff->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Management
    |--------------------------------------------------------------------------
    */

    public function test_owner_can_assign_role_to_staff(): void
    {
        $owner = $this->makeOwner();

        $staff = User::factory()->create();

        $adminRole = $this->createRole(
            'Admin',
            'admin'
        );

        Sanctum::actingAs($owner);

        $response = $this->putJson(
            "/api/admin/staff/{$staff->id}/roles",
            [
                'roles' => [
                    'admin',
                ],
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $staff->id
            );

        $this->assertTrue(
            $staff->fresh()->hasRole('admin')
        );

        $this->assertDatabaseHas('role_user', [
            'user_id' => $staff->id,
            'role_id' => $adminRole->id,
        ]);
    }

    public function test_owner_can_assign_multiple_roles(): void
    {
        $owner = $this->makeOwner();

        $staff = User::factory()->create();

        $this->createRole(
            'Admin',
            'admin'
        );

        $this->createRole(
            'Content Manager',
            'content-manager'
        );

        Sanctum::actingAs($owner);

        $response = $this->putJson(
            "/api/admin/staff/{$staff->id}/roles",
            [
                'roles' => [
                    'admin',
                    'content-manager',
                ],
            ]
        );

        $response->assertOk();

        $staff->refresh();

        $this->assertTrue(
            $staff->hasRole('admin')
        );

        $this->assertTrue(
            $staff->hasRole('content-manager')
        );
    }

    public function test_non_owner_cannot_assign_owner_role(): void
    {
        $manager = $this->makeUserWithPermission(
            'staff.update'
        );

        $staff = User::factory()->create();

        $this->createRole(
            'Owner',
            'owner',
            true
        );

        Sanctum::actingAs($manager);

        $response = $this->putJson(
            "/api/admin/staff/{$staff->id}/roles",
            [
                'roles' => [
                    'owner',
                ],
            ]
        );

        $response->assertForbidden();

        $this->assertFalse(
            $staff->fresh()->hasRole('owner')
        );
    }

    public function test_owner_can_assign_owner_role(): void
    {
        $owner = $this->makeOwner();

        $staff = User::factory()->create();

        Sanctum::actingAs($owner);

        $response = $this->putJson(
            "/api/admin/staff/{$staff->id}/roles",
            [
                'roles' => [
                    'owner',
                ],
            ]
        );

        $response->assertOk();

        $this->assertTrue(
            $staff->fresh()->hasRole('owner')
        );
    }

    public function test_staff_role_assignment_requires_valid_role(): void
    {
        $owner = $this->makeOwner();

        $staff = User::factory()->create();

        Sanctum::actingAs($owner);

        $response = $this->putJson(
            "/api/admin/staff/{$staff->id}/roles",
            [
                'roles' => [
                    'does-not-exist',
                ],
            ]
        );

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'roles.0',
        ]);
    }
}