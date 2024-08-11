<?php

namespace App\Http\Requests;

class AdditionRequest extends MainRequest
{
    public function rules(): array
    {
        return [
            'size_id' => 'required|exists:sizes,id',
            'additions' => 'required|array|min:1',
            'additions.*.addition_id' => 'required|exists:additions,id',
            'additions.*.quantity' => 'required|integer|min:1',
        ];
    }
}
