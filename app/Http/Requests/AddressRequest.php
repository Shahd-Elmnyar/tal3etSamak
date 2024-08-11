<?php

namespace App\Http\Requests;

class AddressRequest extends MainRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:255',
            'type' => 'required|string|max:255|in:home,work,other',
            'building' => 'string|max:255',
            'floor' => 'string|max:255',
            'apartment' => 'string|max:255',
            'information' => 'string|max:255',
        ];
    }

}
