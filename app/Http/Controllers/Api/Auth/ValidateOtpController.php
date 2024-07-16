<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateOtpRequest;
use Illuminate\Support\Facades\Validator;

class ValidateOtpController extends Controller
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
                return response()->json([
                    'status' => 'error',
                    'message' => $otpValidationResult->message,
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update(['otp_validated' => true]); // Update the otp_validated attribute
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => __('auth.user_not_found'),
                ], 404);
            }
            return response()->json([
                'status' => 'success',
                'message' =>__('auth.otp_success'),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error during OTP validation process: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('auth.error_occurred'),
            ], 500);
        }
    }
}
