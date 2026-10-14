<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeEducationRequest extends FormRequest
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
                'required',
                'integer',
                'exists:resume_versions,id',
            ],

            'institution' => [
                'required',
                'string',
                'max:200',
            ],

            'degree' => [
                'nullable',
                'string',
                'max:150',
            ],

            'field_of_study' => [
                'nullable',
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