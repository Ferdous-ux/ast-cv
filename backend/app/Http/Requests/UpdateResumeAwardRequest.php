<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeAwardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resume_version_id' => [
                'sometimes',
                'integer',
                'exists:resume_versions,id',
            ],

            'title' => [
                'sometimes',
                'string',
                'max:200',
            ],

            'issuer' => [
                'sometimes',
                'nullable',
                'string',
                'max:200',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'awarded_at' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'url' => [
                'sometimes',
                'nullable',
                'url',
                'max:500',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }
}