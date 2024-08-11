<?php

namespace App\Http\Requests;


class UpdatePasswordRequest extends MainRequest
{
    public function rules(): array
    {
        return [
            'email' =>['required' ,'email','exists:users'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/\d/',
                'regex:/[@$!%*#?&]/',
                'confirmed'
            ],
        ];
    }
}
