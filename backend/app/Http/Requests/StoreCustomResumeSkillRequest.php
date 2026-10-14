<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomResumeSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => preg_replace(
                '/\s+/u',
                ' ',
                trim((string) $this->input('name'))
            ),

            'category' => filled($this->input('category'))
                ? preg_replace(
                    '/\s+/u',
                    ' ',
                    trim((string) $this->input('category'))
                )
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'category' => [
                'nullable',
                'string',
                'max:50',
            ],

            'level' => [
                'nullable',
                'string',
                'in:beginner,intermediate,advanced,expert,Beginner,Intermediate,Advanced,Expert',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }
}