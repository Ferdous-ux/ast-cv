<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeProjectRequest extends FormRequest
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

            'name' => [
                'sometimes',
                'string',
                'max:200',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'project_url' => [
                'sometimes',
                'nullable',
                'url',
                'max:500',
            ],

            'repository_url' => [
                'sometimes',
                'nullable',
                'url',
                'max:500',
            ],

            'start_date' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'end_date' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }
}