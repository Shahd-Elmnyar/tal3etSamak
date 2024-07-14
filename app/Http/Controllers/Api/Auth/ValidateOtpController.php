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
                $msg = $otpValidationResult->message == "OTP is not valid" || $otpValidationResult->message == "OTP does not exist" ? 'INVALID_OTP' : $otpValidationResult->message;
                return response()->json([
                    'status' => 'error',
                    'message' => $msg
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update(['otp_validated' => true]); // Update the otp_validated attribute
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                ], 404);
            }
            return response()->json([
                'status' => 'success',
                'message' =>'OTP validated successfully. Your account is now verified.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error during OTP validation process: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while validating the OTP. Please try again later.',
            ], 500);
        }
    }
}
