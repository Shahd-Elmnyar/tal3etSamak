<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\MainController;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\PasswordReset;

class UpdatePasswordController  extends MainController
{
    public function store(UpdatePasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            $PasswordReset= PasswordReset::where('email', $request->email)->first();
            if (!$user || !$user->otp_validated || !$PasswordReset) {
                return $this->notFoundResponse('auth.invalid_email_or_otp');
            }
            $user->password = Hash::make($request->password);
            $user->otp_validated = false;
            $user->save();
            // $user->update(['password' => Hash::make($request->password), 'otp_validated' => false]);
            PasswordReset::where('email', $request->email)->delete();
            $user->tokens()->delete();

            return $this->successResponse('auth.password_updated');
        } catch (\Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

}
