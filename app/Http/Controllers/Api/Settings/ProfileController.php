<?php

namespace App\Http\Controllers\Api\Settings;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Api\AppController;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;


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

            // Update the user profile
            if (isset($userData['password'])) {
                $userData['password'] = bcrypt($userData['password']);
            }
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

}
