<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResumePublicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'publisher' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'url' => [
                'sometimes',
                'nullable',
                'url',
                'max:2048',
            ],

            'published_at' => [
                'sometimes',
                'nullable',
                'date',
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