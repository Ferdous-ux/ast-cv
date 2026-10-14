<?php


namespace App\Policies;

use App\Models\Resume;
use App\Models\User;

class ResumePolicy
{
    /**
     * Determine whether the user can view any resumes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the resume.
     */
    public function view(User $user, Resume $resume): bool
    {
        return $resume->user_id === $user->id;
    }

    /**
     * Determine whether the user can create resumes.
     */
    public function create(User $user): bool
    {
        return $user->status === 'active';
    }

    /**
     * Determine whether the user can update the resume.
     */
    public function update(User $user, Resume $resume): bool
    {
        return $resume->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the resume.
     */
    public function delete(User $user, Resume $resume): bool
    {
        return $resume->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the resume.
     */
    public function restore(User $user, Resume $resume): bool
    {
        return $resume->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the resume.
     */
    public function forceDelete(User $user, Resume $resume): bool
    {
        return $resume->user_id === $user->id;
    }
}

