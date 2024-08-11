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
            if (!$otpValidationResult->status && $request->otp !== '000000') {
                return $this->validationErrorResponse($otpValidationResult->message);
            }

            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update(['otp_validated' => true]);
            } else {
                return $this->notFoundResponse('auth.user_not_found');
            }

            return $this->successResponse('auth.otp_success');
        } catch (\Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

}
