<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\MainController;
use App\Http\Requests\UpdatePasswordRequest;

class UpdatePasswordController  extends MainController
{
    public function store(UpdatePasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !$user->otp_validated) {
                return $this->notFoundResponse('auth.invalid_email_or_otp');
            }

            $user->update(['password' => Hash::make($request->password), 'otp_validated' => false]);

            $user->tokens()->delete();

            return $this->successResponse('auth.password_updated');
        } catch (\Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

}
