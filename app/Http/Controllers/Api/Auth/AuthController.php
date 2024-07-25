<?php

namespace App\Http\Controllers\Api\Auth;


use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\MainController;
use Illuminate\Support\Facades\App; // Add this line

class AuthController  extends MainController
{
    public function store(RegisterRequest $request): JsonResponse
    {
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            return $this->notFoundResponse((object)['user_role' => 'auth.user_role_not_found']);
        }

        try {
            $user = User::create($request->validatedData());
            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return $this->successResponse('auth.user_created', (object)['token' => $token, 'user_data' => $userData]);
        } catch (QueryException $e) {
            return $this->genericErrorResponse('auth.database_error', ['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->validationErrorResponse((object)['credential' => 'auth.invalid_credentials']);
        }

        try {
            // Generate and return token on successful login
            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return $this->successResponse('auth.user_login', (object)['token' => $token, 'user_data' => $userData]);
        } catch (\Exception $e) {
            // Handle any unexpected exceptions during token creation
            return $this->genericErrorResponse('auth.login_error', ['error' => $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        // Check if user is authenticated

        try {
            // Check if the current access token is valid
            if ($request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            } else {
                return $this->unauthorizedResponse('auth.invalid_token');
            }
            return $this->successResponse('auth.logged_out');
        } catch (\Exception $e) {
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }
}
