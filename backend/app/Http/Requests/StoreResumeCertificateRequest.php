<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeCertificateRequest extends FormRequest
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

            'name' => [
                'required',
                'string',
                'max:200',
            ],

            'issuer' => [
                'required',
                'string',
                'max:200',
            ],

            'credential_id' => [
                'nullable',
                'string',
                'max:150',
            ],

            'credential_url' => [
                'nullable',
                'url',
                'max:500',
            ],

            'issued_at' => [
                'nullable',
                'date',
            ],

            'expires_at' => [
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