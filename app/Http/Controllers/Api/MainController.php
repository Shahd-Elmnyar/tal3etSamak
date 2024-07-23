<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class MainController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function notFoundResponse($message)
    {
        return response()->json([
            'code' => 'error',
            'errors' =>$message,
        ], 404);
    }
    public function genericErrorResponse($message)
    {
        return response()->json([
            'code' => 'error',
            'errors' => $message ,
        ], 500);
    }
    public function checkAuthorization($request){
        $user = $request->user();

        if (!$user) {
            return $this->unauthorizedResponse(__('auth.user_not_found'));
        }
        return $user;
    }
    public function unauthorizedResponse($message)
    {
        return response()->json([
            'code' => 'error',
            'message' => $message,
        ], 401);
    }
    public function validationErrorResponse($message)
    {
        return response()->json([
            'code' => 'error',
            'message' => 'validation_error',
            'errors' => $message,
        ], 403);
    }
    public function successResponse($message, $data = null)
    {
        $responseData = [
            'code' => 'success',
            'message' => $message,
        ];

        if ($data !== null) {
            $responseData['data'] = $data;
        }

        return response()->json($responseData, 200);
    }
    public function emptyResponse($data,$message){
        if ($data->isEmpty()) {
            return $this->notFoundResponse(__($message));
        }
    }


}
