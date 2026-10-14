<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'headline' => ['sometimes', 'nullable', 'string', 'max:160'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'country' => ['sometimes', 'nullable', 'string', 'max:100'],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'avatar_path' => ['sometimes', 'nullable', 'string', 'max:500'],
            'website_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'linkedin_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'github_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'portfolio_url' => ['sometimes', 'nullable', 'url', 'max:500'],
        ];
    }
}

