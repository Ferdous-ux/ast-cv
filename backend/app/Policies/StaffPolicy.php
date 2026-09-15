<?php

namespace App\Policies;

use App\Models\User;

class StaffPolicy
{
    /**
     * Determine whether the user can view staff members.
     */
    public function viewAny(User $user): bool
    {
        return $user->isOwner()
            || $user->hasPermission('staff.view');
    }

    /**
     * Determine whether the user can view a specific staff member.
     */
    public function view(User $user, User $staff): bool
    {
        if ($user->isOwner()) {
            return true;
        }

        return $user->hasPermission('staff.view');
    }

    /**
     * Determine whether the user can create a staff member.
     */
    public function create(User $user): bool
    {
        return $user->isOwner()
            || $user->hasPermission('staff.create');
    }

    /**
     * Determine whether the user can update a staff member.
     */
    public function update(User $user, User $staff): bool
    {
        /*
         * Only the Owner can manage another Owner.
         */
        if ($staff->isOwner()) {
            return $user->isOwner();
        }

        return $user->isOwner()
            || $user->hasPermission('staff.update');
    }

    /**
     * Determine whether the user can delete a staff member.
     */
    public function delete(User $user, User $staff): bool
    {
        /*
         * An Owner cannot be deleted through normal
         * staff management.
         */
        if ($staff->isOwner()) {
            return false;
        }

        return $user->isOwner()
            || $user->hasPermission('staff.delete');
    }

    /**
     * Determine whether the user can manage roles
     * assigned to a staff member.
     */
    public function manageRoles(User $user, User $staff): bool
    {
        /*
         * Only the Owner can modify another Owner's roles.
         */
        if ($staff->isOwner()) {
            return $user->isOwner();
        }

        /*
         * Owner always has full authority.
         */
        if ($user->isOwner()) {
            return true;
        }

        /*
         * Staff with staff.update may manage roles,
         * but the service layer must still prevent
         * assigning the Owner role.
         */
        return $user->hasPermission('staff.update');
    }

    /**
     * Determine whether the user can assign the Owner role.
     *
     * This is intentionally restricted to the system Owner.
     */
    public function assignOwnerRole(User $user): bool
    {
        return $user->isOwner();
    }
}