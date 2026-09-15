<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeAwardRequest extends FormRequest
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

            'title' => [
                'required',
                'string',
                'max:200',
            ],

            'issuer' => [
                'nullable',
                'string',
                'max:200',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'awarded_at' => [
                'nullable',
                'date',
            ],

            'url' => [
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