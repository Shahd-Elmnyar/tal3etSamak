<?php

namespace App\Http\Controllers\Api\Settings;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\helpResource;
use App\Http\Controllers\Api\AppController;
use Illuminate\Validation\ValidationException;

class HelpController extends AppController
{
    public function index()
    {
        try {
            $helpPages = Page::where('page_type', 'help')->get();

            if ($helpPages->isEmpty()) {
                return $this->notFoundResponse('home.help_pages_not_found');
            }

            return $this->successResponse(
                'Help pages retrieved successfully.',
                [
                    'help' => HelpResource::collection($helpPages),
                ]
            );
        } catch (\Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    public function search(Request $request)
    {
        try {
            $validated = $request->validate([
                'search' => 'required|string|max:255',
            ]);

            $filters = ['search' => $validated['search']];

            $helpPages = Page::where('page_type', 'help')
                ->filter($filters)
                ->get();

            if ($helpPages->isEmpty()) {
                return $this->notFoundResponse('home.no_help_page_match');
            }

            return $this->successResponse(
                null,
                [
                    'help' => HelpResource::collection($helpPages),
                ]
            );

        } catch (ValidationException $e) {

            Log::error('Validation errors: ', ['errors' => $e->errors()]);
            return $this->validationErrorResponse(['errors' => $e->errors()]);

        } catch (\Exception $e) {

            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
            
        }
    }

}
