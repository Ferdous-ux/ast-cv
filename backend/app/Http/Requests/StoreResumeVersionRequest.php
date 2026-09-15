<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'summary' => [
                'nullable',
                'string',
            ],

            'template' => [
                'sometimes',
                'string',
                'max:100',
            ],
        ];
    }
}