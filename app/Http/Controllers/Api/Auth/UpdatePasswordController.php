<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdatePasswordRequest;

class UpdatePasswordController extends Controller
{
    public function store(UpdatePasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user || !$user->otp_validated) {
                return response()->json([
                    'status' => 'error',
                    'data' => 'The email or OTP provided is invalid. Please check and try again.'
                ], 401);
            }

            $user->update(['password' => Hash::make($request->password), 'otp_validated' => false]);

            return response()->json([
                'status' => 'success',
                'message' => 'your password has been updated successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error during password update process: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'data' => 'An error occurred while updating the password. Please try again later.',
            ], 500);
        }
    }
}
