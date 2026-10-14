<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeSkillsBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skills' => [
                'required',
                'array',
                'min:1',
                'max:50',
            ],

            'skills.*.skill_id' => [
                'required',
                'integer',
                'distinct',
                'exists:skills,id',
            ],

            'skills.*.level' => [
                'nullable',
                'string',
                'in:beginner,intermediate,advanced,expert,Beginner,Intermediate,Advanced,Expert',
            ],

            'skills.*.sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }
}