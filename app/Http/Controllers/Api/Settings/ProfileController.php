<?php

namespace App\Http\Controllers\Api\Settings;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\AppController;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Validation\ValidationException;


class ProfileController extends AppController
{
    public function index()
    {
        try {
            $this->user->load('addresses'); // Assuming you have an address relationship
            return $this->successResponse(
                'home.home_success',
                [
                    'user' => new UserResource($this->user),
                ]
            );
        } catch (\Exception $e) {
            Log::error('profile error: ' . $e->getMessage());
            return $this->genericErrorResponse(__('auth.error_occurred'), ['error' => $e->getMessage()]);
        }
    }

    public function deleteAccount()
    {
        try {
            $this->user->delete(); // Soft delete the user
            return $this->successResponse('home.delete_success');
        } catch (\Exception $e) {
            Log::error('delete account error: ' . $e->getMessage());
            return $this->genericErrorResponse(__('auth.error_occurred'), ['error' => $e->getMessage()]);
        }
    }


    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            // Check if there is any input to update
            $userData = $request->only(['name', 'email', 'phone', 'password', 'date_of_birth', 'gender']);
            $hasUpdates = false;

            foreach ($userData as $key => $value) {
                if (!is_null($value)) {
                    $hasUpdates = true;
                    break;
                }
            }

            // Handle profile image upload
            if ($request->hasFile('img')) {
                $hasUpdates = true;
                $image = $request->file('img');
                // Generate a unique file name
                $filename = time() . '.' . $image->getClientOriginalExtension();
                // Move the file to 'public/uploads'
                $image->move(public_path('uploads'), $filename);
                $userData['img'] = 'uploads/' . $filename; // Store the relative path
            }
            // dd($userData);

            if (!$hasUpdates && !$request->has('city_of_residence')) {
                return $this->successResponse('home.no_changes', [
                    'user' => new UserResource($this->user),
                ]);
            }

            if (isset($userData['date_of_birth'])) {
                $userData['date_of_birth'] = Carbon::createFromFormat('Y-m-d', $userData['date_of_birth']);
            }

            // Update the user
            $this->user->update($userData);

            // Update address if provided
            if ($request->has('city_of_residence')) {
                $address = $this->user->addresses()->first(); // Adjust based on your relationship
                if ($address) {
                    $address->update(['city_of_residence' => $request->input('city_of_residence')]);
                } else {
                    $this->user->addresses()->create(['city_of_residence' => $request->input('city_of_residence')]);
                }
            }

            $user = $this->user->load('addresses');

            return $this->successResponse('home.update_success', [
                'user' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            Log::error('update profile error: ' . $e->getMessage());
            return $this->genericErrorResponse(__('auth.error_occurred'), ['error' => $e->getMessage()]);
        }
    }
    public function changePassword(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'current_password' => 'required',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/\d/',
                    'regex:/[@$!%*#?&]/',
                ]
            ]);
            $currentPassword = $request->current_password;
            $newPassword = $request->password;
            Log::info('Current password hash: ' . $this->user->password);
            // Check if the current password matches
            if (!Hash::check($currentPassword, $this->user->password)) {
                return $this->validationErrorResponse(__('auth.password_incorrect'));
            }
            // Hash the new password
            $hashedNewPassword = Hash::make($newPassword);
            Log::info('New hashed password: ' . $hashedNewPassword);
            // Update the password
            $this->user->password = $hashedNewPassword;
            $this->user->save();
            Log::info('Password changed successfully for user ID: ' . $this->user->id);
            return $this->successResponse('home.password_change_success');
        } catch (ValidationException $e) {
            Log::error('Validation error: ', ['errors' => $e->errors()]);
            return $this->validationErrorResponse((object)['errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('change password error: ' . $e->getMessage());
            return $this->genericErrorResponse(__('auth.error_occurred'), ['error' => $e->getMessage()]);
        }
    }
    public function changeLang(Request $request){

        try {
            $request->validate([
                'lang' => 'required|in:en,ar',
            ]);
            $this->user->lang = $request->lang;
            $this->user->save();
            return $this->successResponse('home.language_change_success');
        } catch (ValidationException $e) {
            Log::error('Validation error: ', ['errors' => $e->errors()]);
            return $this->validationErrorResponse((object)['errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('change language error: ' . $e->getMessage());
            return $this->genericErrorResponse(__('auth.error_occurred'), ['error' => $e->getMessage()]);
        }
    }
}
