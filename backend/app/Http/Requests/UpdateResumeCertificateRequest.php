<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeCertificateRequest extends FormRequest
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

            'issuer' => [
                'sometimes',
                'string',
                'max:200',
            ],

            'credential_id' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
            ],

            'credential_url' => [
                'sometimes',
                'nullable',
                'url',
                'max:500',
            ],

            'issued_at' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'expires_at' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:issued_at',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }
}