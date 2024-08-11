<?php

namespace App\Http\Requests;


class UpdateProfileRequest extends MainRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $this->user()->id,
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $this->user()->id,
            'img' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'date_of_birth' => 'nullable|date|before:today|date_format:Y-m-d',
            'gender' => 'nullable|string|in:male,female',
            'city_of_residence' => 'nullable|string|max:255',
        ];
    }
}

