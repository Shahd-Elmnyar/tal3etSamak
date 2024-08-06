<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAddressRequest extends FormRequest
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
            'name' => 'string|max:255',
            'content' => 'string|max:255',
            'type' => 'string|max:255|in:home,work,other',
            'building' => 'string|max:255',
            'floor' => 'string|max:255',
            'apartment' => 'string|max:255',
            'information' => 'string|max:255',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'status' => 'error',
            'message' => "validation_error",
            'errors' => $validator->errors(),
        ], 422);
        throw new HttpResponseException($response);
    }
}
