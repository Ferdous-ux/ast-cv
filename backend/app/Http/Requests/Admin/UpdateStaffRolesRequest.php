<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $staff = $this->route('staff');

        if (! $staff) {
            return false;
        }

        /*
         * User must have permission to manage roles.
         */
        if (! $this->user()?->can('manageRoles', $staff)) {
            return false;
        }

        /*
         * Only the Owner can assign the Owner role.
         *
         * This is an authorization rule, so the response
         * must be HTTP 403 instead of HTTP 422.
         */
        if (
            in_array(
                'owner',
                $this->input('roles', []),
                true
            )
            && ! $this->user()?->isOwner()
        ) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'roles' => [
                'required',
                'array',
                'min:1',
            ],

            'roles.*' => [
                'required',
                'string',
                'exists:roles,slug',
            ],
        ];
    }
}