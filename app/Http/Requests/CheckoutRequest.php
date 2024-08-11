<?php

namespace App\Http\Requests;

class CheckoutRequest extends MainRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'address_type' => 'required|string|max:50',
            'order_type' => 'required|string|max:50|in:delivery,restaurant',
        ];
    }
}
