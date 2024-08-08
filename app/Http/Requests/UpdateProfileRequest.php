<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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

