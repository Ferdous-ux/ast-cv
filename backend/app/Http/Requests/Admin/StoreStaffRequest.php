<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class StoreStaffRequest extends FormRequest
{


    /**
     * Authorization
     */
    public function authorize(): bool
    {

        return $this->user()?->isOwner()
            ||
            $this->user()?->hasPermission('staff.create');

    }







    /**
     * Validation Rules
     */
    public function rules(): array
    {

        return [



            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */


            'name' => [

                'required',

                'string',

                'max:120',

            ],





            'email' => [

                'required',

                'email',

                'max:255',

                'unique:users,email',

            ],





            'password' => [

                'required',

                'confirmed',

                Password::defaults(),

            ],






            'status' => [

                'sometimes',

                'string',

                'in:active,inactive,suspended',

            ],







            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */


            'roles' => [

                'nullable',

                'array',

            ],






            'roles.*' => [

                'exists:roles,id',

            ],



        ];

    }



}