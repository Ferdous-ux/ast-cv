<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        $staff = $this->route('staff');

        if (! $staff) {
            return false;
        }

        return $this->user()?->can('update', $staff) ?? false;
    }

    public function rules(): array
    {
        $staff = $this->route('staff');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:120',
            ],

            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($staff?->id),
            ],

            'status' => [
                'sometimes',
                'string',
                'in:active,inactive,suspended',
            ],
        ];
    }
}