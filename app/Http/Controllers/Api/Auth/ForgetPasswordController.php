<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Exceptions\Handler;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordRequest;
use App\Notifications\ResetPasswordVerificationNotification;


class ForgetPasswordController extends Controller
{
    public function store(ForgetPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('auth.user_not_found'),
                ], 404);
            }

            // Send the notification
            $notification = new ResetPasswordVerificationNotification();
            if (!$user->notify($notification)) {
                return response()->json([
                    'status' => 'success',
                ], 200);
            } else {
                Log::error('Failed to send notification to user: ' . $user->email);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send notification',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error during forget password process: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('auth.error_occurred'),
            ], 500);
        }
    }
}
