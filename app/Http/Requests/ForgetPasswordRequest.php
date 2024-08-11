<?php

namespace App\Http\Requests;



class ForgetPasswordRequest extends MainRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users',
        ];
    }
}
