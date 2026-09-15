<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Policies\StaffPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
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
    | Role Tests
    |--------------------------------------------------------------------------
    */

    public function test_owner_has_owner_role(): void
    {
        $owner = $this->makeOwner();

        $this->assertTrue(
            $owner->hasRole('owner')
        );

        $this->assertTrue(
            $owner->isOwner()
        );
    }

    public function test_regular_user_is_not_owner(): void
    {
        $user = User::factory()->create();

        $this->assertFalse(
            $user->hasRole('owner')
        );

        $this->assertFalse(
            $user->isOwner()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Tests
    |--------------------------------------------------------------------------
    */

    public function test_user_can_have_permission_through_role(): void
    {
        $user = $this->makeUserWithPermission(
            'staff.create'
        );

        $this->assertTrue(
            $user->hasPermission('staff.create')
        );
    }

    public function test_user_without_permission_is_denied(): void
    {
        $user = User::factory()->create();

        $this->assertFalse(
            $user->hasPermission('staff.create')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Staff Policy Tests
    |--------------------------------------------------------------------------
    */

    public function test_owner_can_view_staff(): void
    {
        $owner = $this->makeOwner();
        $staff = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->viewAny($owner)
        );

        $this->assertTrue(
            $policy->view($owner, $staff)
        );
    }

    public function test_user_without_staff_permission_cannot_view_staff(): void
    {
        $user = User::factory()->create();
        $staff = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertFalse(
            $policy->viewAny($user)
        );

        $this->assertFalse(
            $policy->view($user, $staff)
        );
    }

    public function test_user_with_staff_view_permission_can_view_staff(): void
    {
        $user = $this->makeUserWithPermission(
            'staff.view'
        );

        $staff = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->viewAny($user)
        );

        $this->assertTrue(
            $policy->view($user, $staff)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Staff Creation
    |--------------------------------------------------------------------------
    */

    public function test_owner_can_create_staff(): void
    {
        $owner = $this->makeOwner();

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->create($owner)
        );
    }

    public function test_user_without_create_permission_cannot_create_staff(): void
    {
        $user = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertFalse(
            $policy->create($user)
        );
    }

    public function test_user_with_create_permission_can_create_staff(): void
    {
        $user = $this->makeUserWithPermission(
            'staff.create'
        );

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->create($user)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Staff Update
    |--------------------------------------------------------------------------
    */

    public function test_owner_can_update_staff(): void
    {
        $owner = $this->makeOwner();
        $staff = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->update($owner, $staff)
        );
    }

    public function test_user_without_update_permission_cannot_update_staff(): void
    {
        $user = User::factory()->create();
        $staff = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertFalse(
            $policy->update($user, $staff)
        );
    }

    public function test_user_with_update_permission_can_update_staff(): void
    {
        $user = $this->makeUserWithPermission(
            'staff.update'
        );

        $staff = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->update($user, $staff)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Owner Protection
    |--------------------------------------------------------------------------
    */

    public function test_only_owner_can_update_owner(): void
    {
        $owner = $this->makeOwner();
        $anotherUser = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->update($owner, $owner)
        );

        $this->assertFalse(
            $policy->update($anotherUser, $owner)
        );
    }

    public function test_owner_cannot_be_deleted(): void
    {
        $owner = $this->makeOwner();

        $policy = new StaffPolicy();

        $this->assertFalse(
            $policy->delete($owner, $owner)
        );
    }

    public function test_owner_can_delete_regular_staff(): void
    {
        $owner = $this->makeOwner();
        $staff = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->delete($owner, $staff)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role Management
    |--------------------------------------------------------------------------
    */

    public function test_owner_can_manage_staff_roles(): void
    {
        $owner = $this->makeOwner();
        $staff = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->manageRoles($owner, $staff)
        );
    }

    public function test_staff_with_update_permission_can_manage_roles(): void
    {
        $staffManager = $this->makeUserWithPermission(
            'staff.update'
        );

        $staff = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->manageRoles($staffManager, $staff)
        );
    }

    public function test_regular_user_cannot_manage_roles(): void
    {
        $user = User::factory()->create();
        $staff = User::factory()->create();

        $policy = new StaffPolicy();

        $this->assertFalse(
            $policy->manageRoles($user, $staff)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Owner Role Protection
    |--------------------------------------------------------------------------
    */

    public function test_only_owner_can_assign_owner_role(): void
    {
        $owner = $this->makeOwner();

        $admin = $this->makeUserWithPermission(
            'staff.update'
        );

        $policy = new StaffPolicy();

        $this->assertTrue(
            $policy->assignOwnerRole($owner)
        );

        $this->assertFalse(
            $policy->assignOwnerRole($admin)
        );
    }
}