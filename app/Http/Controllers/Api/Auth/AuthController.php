<?php

namespace App\Http\Controllers\Api\Auth;


use Exception;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Api\MainController;

class AuthController extends MainController
{
    public function store(RegisterRequest $request): JsonResponse
    {
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            return $this->notFoundResponse('auth.user_role_not_found');
        }

        try {
            $user = User::create($request->validated());
            $user->load('addresses');
            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return $this->successResponse('auth.user_created', (object)['token' => $token, 'user_data' => $userData]);
        } catch (QueryException $e) {
            Log::error('Database error during register process: ' . $e->getMessage(), [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            return $this->genericErrorResponse('auth.database_error', ['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }




    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->with('addresses')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->validationErrorResponse('auth.invalid_credentials');
        }

        try {

            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return $this->successResponse('auth.user_login', (object)['token' => $token, 'user_data' => $userData]);
        } catch (Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }



    public function logout(Request $request)
    {
        try {
            if ($request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            } else {
                return $this->unauthorizedResponse('auth.invalid_token');
            }
            return $this->successResponse('auth.logged_out');
        } catch (Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
}
