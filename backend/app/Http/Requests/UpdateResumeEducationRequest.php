<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeEducationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'resume_version_id' => [
                'sometimes',
                'integer',
                'exists:resume_versions,id',
            ],

            'institution' => [
                'sometimes',
                'string',
                'max:200',
            ],

            'degree' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
            ],

            'field_of_study' => [
                'sometimes',
                'nullable',
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