<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skill_id' => [
                'required',
                'integer',
                'exists:skills,id',
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