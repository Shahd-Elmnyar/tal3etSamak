<?php

namespace App\Http\Requests;


class RegisterRequest extends MainRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'phone' => ['required', 'string', 'min:10', 'max:15', 'unique:users,phone,NULL,id,deleted_at,NULL'],
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
