<?php

namespace App\Http\Requests;

class UpdateAddressRequest extends MainRequest
{

    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'content' => 'string|max:255',
            'type' => 'string|max:255|in:home,work,other',
            'building' => 'string|max:255',
            'floor' => 'string|max:255',
            'apartment' => 'string|max:255',
            'information' => 'string|max:255',
        ];
    }

}
