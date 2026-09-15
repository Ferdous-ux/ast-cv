<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeExperienceRequest extends FormRequest
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

            'company_name' => [
                'sometimes',
                'string',
                'max:200',
            ],

            'job_title' => [
                'sometimes',
                'string',
                'max:150',
            ],

            'location' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
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

            'is_current' => [
                'sometimes',
                'boolean',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }
}