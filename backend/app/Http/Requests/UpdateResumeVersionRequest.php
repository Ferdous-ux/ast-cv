<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'summary' => [
                'sometimes',
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