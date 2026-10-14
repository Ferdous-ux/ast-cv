<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    /**
     * Seed roles and permissions for the AST-CV admin system.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Users
            [
                'name' => 'View Users',
                'slug' => 'users.view',
                'group' => 'users',
                'description' => 'View platform users.',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'users.create',
                'group' => 'users',
                'description' => 'Create new platform users.',
            ],
            [
                'name' => 'Update Users',
                'slug' => 'users.update',
                'group' => 'users',
                'description' => 'Update platform user information.',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'users.delete',
                'group' => 'users',
                'description' => 'Delete platform users.',
            ],

            // Resumes
            [
                'name' => 'View Resumes',
                'slug' => 'resumes.view',
                'group' => 'resumes',
                'description' => 'View user resumes.',
            ],
            [
                'name' => 'Update Resumes',
                'slug' => 'resumes.update',
                'group' => 'resumes',
                'description' => 'Update user resumes.',
            ],
            [
                'name' => 'Delete Resumes',
                'slug' => 'resumes.delete',
                'group' => 'resumes',
                'description' => 'Delete user resumes.',
            ],

            // Templates
            [
                'name' => 'View Templates',
                'slug' => 'templates.view',
                'group' => 'templates',
                'description' => 'View CV templates.',
            ],
            [
                'name' => 'Create Templates',
                'slug' => 'templates.create',
                'group' => 'templates',
                'description' => 'Create new CV templates.',
            ],
            [
                'name' => 'Update Templates',
                'slug' => 'templates.update',
                'group' => 'templates',
                'description' => 'Update CV templates.',
            ],
            [
                'name' => 'Delete Templates',
                'slug' => 'templates.delete',
                'group' => 'templates',
                'description' => 'Delete CV templates.',
            ],

            // Skills
            [
                'name' => 'Manage Skills',
                'slug' => 'skills.manage',
                'group' => 'skills',
                'description' => 'Manage system skills.',
            ],

            // Languages
            [
                'name' => 'Manage Languages',
                'slug' => 'languages.manage',
                'group' => 'languages',
                'description' => 'Manage system languages.',
            ],

            // ATS
            [
                'name' => 'View ATS',
                'slug' => 'ats.view',
                'group' => 'ats',
                'description' => 'View ATS analysis and configuration.',
            ],
            [
                'name' => 'Manage ATS',
                'slug' => 'ats.manage',
                'group' => 'ats',
                'description' => 'Manage ATS rules and configuration.',
            ],

            // Staff
            [
                'name' => 'View Staff',
                'slug' => 'staff.view',
                'group' => 'staff',
                'description' => 'View admin staff members.',
            ],
            [
                'name' => 'Create Staff',
                'slug' => 'staff.create',
                'group' => 'staff',
                'description' => 'Create new admin staff members.',
            ],
            [
                'name' => 'Update Staff',
                'slug' => 'staff.update',
                'group' => 'staff',
                'description' => 'Update admin staff members.',
            ],
            [
                'name' => 'Delete Staff',
                'slug' => 'staff.delete',
                'group' => 'staff',
                'description' => 'Delete admin staff members.',
            ],

            // Settings
            [
                'name' => 'View Settings',
                'slug' => 'settings.view',
                'group' => 'settings',
                'description' => 'View system settings.',
            ],
            [
                'name' => 'Update Settings',
                'slug' => 'settings.update',
                'group' => 'settings',
                'description' => 'Update system settings.',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Create / Update Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(
                [
                    'slug' => $permissionData['slug'],
                ],
                $permissionData
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = [
            [
                'name' => 'Owner',
                'slug' => 'owner',
                'description' => 'Full system access and ownership privileges.',
                'is_system' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'General administrative access.',
                'is_system' => true,
            ],
            [
                'name' => 'Content Manager',
                'slug' => 'content-manager',
                'description' => 'Manages CV content, templates, skills, and languages.',
                'is_system' => true,
            ],
            [
                'name' => 'Support',
                'slug' => 'support',
                'description' => 'Handles user support and account-related tasks.',
                'is_system' => true,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Create / Update Roles
        |--------------------------------------------------------------------------
        */

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                [
                    'slug' => $roleData['slug'],
                ],
                $roleData
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Load Roles
        |--------------------------------------------------------------------------
        */

        $owner = Role::where('slug', 'owner')->firstOrFail();
        $admin = Role::where('slug', 'admin')->firstOrFail();
        $contentManager = Role::where('slug', 'content-manager')->firstOrFail();
        $support = Role::where('slug', 'support')->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Load Permissions
        |--------------------------------------------------------------------------
        */

        $allPermissions = Permission::all();

        /*
        |--------------------------------------------------------------------------
        | Owner
        |--------------------------------------------------------------------------
        |
        | Owner receives every permission.
        |
        */

        $owner->permissions()->sync(
            $allPermissions->pluck('id')
        );

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $adminPermissions = Permission::whereIn('slug', [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'resumes.view',
            'resumes.update',
            'resumes.delete',

            'templates.view',
            'templates.create',
            'templates.update',
            'templates.delete',

            'skills.manage',
            'languages.manage',

            'ats.view',
            'ats.manage',

            'staff.view',

            'settings.view',
        ])->pluck('id');

        $admin->permissions()->sync($adminPermissions);

        /*
        |--------------------------------------------------------------------------
        | Content Manager
        |--------------------------------------------------------------------------
        */

        $contentManagerPermissions = Permission::whereIn('slug', [
            'resumes.view',

            'templates.view',
            'templates.create',
            'templates.update',
            'templates.delete',

            'skills.manage',
            'languages.manage',

            'ats.view',
        ])->pluck('id');

        $contentManager->permissions()->sync(
            $contentManagerPermissions
        );

        /*
        |--------------------------------------------------------------------------
        | Support
        |--------------------------------------------------------------------------
        */

        $supportPermissions = Permission::whereIn('slug', [
            'users.view',
            'users.update',

            'resumes.view',

            'settings.view',
        ])->pluck('id');

        $support->permissions()->sync(
            $supportPermissions
        );
    }
}