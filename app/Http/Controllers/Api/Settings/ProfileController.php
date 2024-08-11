<?php

namespace App\Http\Controllers\Api\Settings;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\AppController;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Validation\ValidationException;

class ProfileController extends AppController
{
    public function index()
    {
        try {
            $this->getUserData();
            return $this->successResponse(
                null,
                [
                    'user' => new UserResource($this->user),
                ]
            );
        } catch (\Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    public function deleteAccount()
    {
        try {
            $this->user->delete();
            return $this->successResponse('home.delete_success');
        } catch (\Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }


    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $userData = $this->extractUserData($request);

            if (!$this->hasUpdates($userData, $request)) {
                return $this->successResponse('No changes were made.', [
                    'user' => new UserResource($this->user),
                ]);
            }

            $this->handleProfileImage($request, $userData);
            $this->formatDateOfBirth($userData);

            $this->user->update($userData);

            if ($request->has('city_of_residence')) {
                $this->updateUserAddress($request->input('city_of_residence'));
            }

            return $this->successResponse('Profile updated successfully.', [
                'user' => new UserResource($this->getUserData()),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    private function extractUserData(Request $request): array
    {
        return $request->only(['name', 'email', 'phone', 'password', 'date_of_birth', 'gender']);
    }

    private function hasUpdates(array $userData, Request $request): bool
    {
        return collect($userData)->filter()->isNotEmpty() || $request->hasFile('img') || $request->has('city_of_residence');
    }

    private function handleProfileImage(Request $request, array &$userData): void
    {
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $filename);
            $userData['img'] = 'uploads/' . $filename;
        }
    }

    private function formatDateOfBirth(array &$userData): void
    {
        if (isset($userData['date_of_birth'])) {
            $userData['date_of_birth'] = Carbon::createFromFormat('Y-m-d', $userData['date_of_birth']);
        }
    }

    private function updateUserAddress(string $cityOfResidence): void
    {
        $address = $this->user->addresses()->first();

        if ($address) {
            $address->update(['city_of_residence' => $cityOfResidence]);
        } else {
            $this->user->addresses()->firstOrCreate(['city_of_residence' => $cityOfResidence]);
        }
    }

    public function changePassword(Request $request)
    {
        try {

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

            if (!Hash::check($currentPassword, $this->user->password)) {
                return $this->validationErrorResponse(__('auth.password_incorrect'));
            }

            $hashedNewPassword = Hash::make($newPassword);
            Log::info('New hashed password: ' . $hashedNewPassword);

            $this->user->password = $hashedNewPassword;
            $this->user->save();
            Log::info('Password changed successfully for user ID: ' . $this->user->id);
            return $this->successResponse('home.password_change_success');
        } catch (ValidationException $e) {
            Log::error('Validation error: ', ['errors' => $e->errors()]);
            return $this->validationErrorResponse((object)['errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
    public function changeLang(Request $request): JsonResponse
    {
        try {
            $this->validateLanguageChange($request);
            $this->updateUserLanguage($request->lang);

            return $this->successResponse('home.language_change_success');
        } catch (ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return $this->validationErrorResponse((object)['errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    private function validateLanguageChange(Request $request): void
    {
        $request->validate([
            'lang' => 'required|in:en,ar',
        ]);
    }

    private function updateUserLanguage(string $lang): void
    {
        $this->user->lang = $lang;
        $this->user->save();
    }

    protected function getUserData()
    {
        return $this->user->load('addresses');
    }

}
