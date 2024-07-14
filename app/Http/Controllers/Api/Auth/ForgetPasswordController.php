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
                    'message' => 'We could not find a user with that email address.',
                ], 404);
            }

            $user->notify(new ResetPasswordVerificationNotification());
            return response()->json([
                'status' => 'success',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error during forget password process: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. Please try again later.',
            ], 500);
        }
    }
}
