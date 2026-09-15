<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resume_version_id' => [
                'required',
                'integer',
                'exists:resume_versions,id',
            ],

            'company_name' => [
                'required',
                'string',
                'max:200',
            ],

            'job_title' => [
                'required',
                'string',
                'max:150',
            ],

            'location' => [
                'nullable',
                'string',
                'max:150',
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'is_current' => [
                'boolean',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'sort_order' => [
                'integer',
                'min:0',
            ],
        ];
    }
}