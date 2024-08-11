<?php

namespace App\Http\Requests;


class ValidateOtpRequest extends MainRequest
{

    public function rules(): array
    {
        return [
            'email' =>['required' ,'email','exists:users'],
            'otp' =>['required' ,'max:6'],
        ];
    }
    
}
