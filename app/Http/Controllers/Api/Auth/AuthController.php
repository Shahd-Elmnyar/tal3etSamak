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

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        // Log the incoming request data for debugging


        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            return response()->json([
                'status' => 'error',
                'errors' => (object)['user_role'=>'user role not found'] ,
            ], 500);
        }

        try {
            // dd($request->email);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role_id' => $userRole->id,
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'data' => $userData,
            ]);
        } catch (QueryException $e) {
            return response()->json(['msg' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // public function logout(Request $request)
    // {
    //     // Check if user is authenticated
    //     if (!$request->user()) {
    //         return response()->json([
    //             'code'=>'ERROR',
    //             'data' => 'USER_NOT_AUTH'
    //         ], 401);
    //     }
    //     try {
    //         // Check if the current access token is valid
    //         if ($request->user()->currentAccessToken()) {
    //             $request->user()->currentAccessToken()->delete();
    //         } else {
    //             return response()->json([
    //                 'code' =>'ERROR',
    //                 'data'=>'INVALID_TOKEN',
    //                 ], 401);
    //         }
    //         return response()->json([
    //             "code" => 'SUCCESS',
    //             'data'=>(object)[],
    //             ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['code' => 'ERROR: ' . $e->getMessage()], 500);
    //     }
    // }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation_error' ,
                'errors'=>(object)['credential'=> 'The provided credentials are incorrect. Please try again.'],
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
                'message' => 'An error occurred while trying to log you in. Please try again later.',
            ], 500);
        }
    }

}
