<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateOtpRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\MainController;

class ValidateOtpController  extends MainController
{
    private $otp;

    public function __construct()
    {
        $this->otp = new Otp;
    }

    public function store(ValidateOtpRequest $request)
    {
        try {
            $otpValidationResult = $this->otp->validate($request->email, $request->otp);
            if (!$otpValidationResult->status) {
                return $this->validationErrorResponse($otpValidationResult->message);
            }

            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update(['otp_validated' => true]); // Update the otp_validated attribute
            } else {
                return $this->notFoundResponse(__('auth.user_not_found'));
            }
            return $this->successResponse(__('auth.otp_success'));
        } catch (\Exception $e) {
            Log::error('Error during OTP validation process: ' . $e->getMessage());

            return $this->genericErrorResponse(__('auth.error_occurred', ['error' => $e->getMessage()]));
        }
    }
}
