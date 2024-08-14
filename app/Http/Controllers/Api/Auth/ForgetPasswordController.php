<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\MainController;
use App\Http\Requests\ForgetPasswordRequest;
use App\Notifications\ResetPasswordVerificationNotification;


class ForgetPasswordController  extends MainController
{
    


























    // public function store(ForgetPasswordRequest $request)
    // {
    //     try {
    //         $user = User::where('email', $request->email)->first();
    //         if (!$user) {
    //             return $this->notFoundResponse( 'auth.user_not_found');
    //         }

    //         $notification = new ResetPasswordVerificationNotification();
    //         if (!$user->notify($notification)) {
    //             return $this->successResponse( 'auth.notification_success');
    //         } else {
    //             Log::error('Failed to send notification to user: ' . $user->email);
    //             return $this->genericErrorResponse( 'home.notification_failed');
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('General error : ' . $e->getMessage());
    //         return $this->genericErrorResponse();
    //     }
    // }
}
