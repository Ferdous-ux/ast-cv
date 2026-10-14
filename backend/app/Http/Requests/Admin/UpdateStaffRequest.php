<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateStaffRequest extends FormRequest
{


    public function authorize(): bool
    {
        $staff = $this->route('staff');


        if (! $staff) {

            return false;

        }


        return $this->user()?->can('update', $staff)
            ?? false;

    }







    public function rules(): array
    {

        $staff = $this->route('staff');



        return [



            'name' => [

                'sometimes',

                'string',

                'max:120',

            ],






            'email' => [

                'sometimes',

                'email',

                'max:255',


                Rule::unique(
                    'users',
                    'email'
                )
                ->ignore(
                    $staff?->id
                ),

            ],







            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */


            'password' => [

                'nullable',

                'confirmed',

                Password::defaults(),

            ],







            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */


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

                'required',

                'array',

                'min:1',

            ],




            'roles' => [
    'nullable',
    'array',
],



        ];

    }

}