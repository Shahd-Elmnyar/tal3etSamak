<?php

namespace App\Http\Controllers\Api\Auth;

use Log;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App; // Add this line

class AuthController extends Controller
{
    // app/Http/Controllers/Api/Auth/AuthController.php

    public function store(RegisterRequest $request): JsonResponse
    {
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            return response()->json([
                'status' => 'error',
                'errors' => (object)['user_role' => __('auth.user_role_not_found')],
            ], 500);
        }

        try {
            $user = User::create($request->validatedData());
            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'data' => $userData,
            ]);
        } catch (QueryException $e) {
            return response()->json(['msg' => __('auth.database_error', ['error' => $e->getMessage()])], 500);
        } catch (\Exception $e) {
            return response()->json(['msg' => __('auth.general_error', ['error' => $e->getMessage()])], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            dd(Hash::make($request->password) , $user->password);
            return response()->json([
                'status' => 'error',
                'message' => 'validation_error',
                'errors' => (object)['credential' => __('auth.invalid_credentials')],
            ], 401);
        }

        try {
            // Generate and return token on successful login
            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'data' => $userData,
            ]);
        } catch (\Exception $e) {
            // Handle any unexpected exceptions during token creation
            return response()->json([
                'status' => 'error',
                'message' => __('auth.login_error'),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'code' => 'ERROR',
                'data' => __('auth.user_not_auth'),
            ], 401);
        }
        try {
            // Check if the current access token is valid
            if ($request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            } else {
                return response()->json([
                    'code' => 'ERROR',
                    'data' => __('auth.invalid_token'),
                ], 401);
            }
            return response()->json([
                "code" => 'SUCCESS',
                'data' => (object)[],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['code' => 'ERROR: ' . $e->getMessage()], 500);
        }
    }
}
