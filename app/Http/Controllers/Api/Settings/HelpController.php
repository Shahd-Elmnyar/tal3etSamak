<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Api\AppController;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\helpResource;

class HelpController extends AppController
{
    public function index(){
        $help =Page::where('page_type', 'help')->get();
        $HelpResources = $help->isNotEmpty() ? helpResource::collection($help) : [];

        return $this->successResponse(
            'home.home_success',
            [
                'help' => $HelpResources,
            ]
        );
    }
    public function search(Request $request)
    {
        // Validate the search input
        $validated = $request->validate([
            'search' => 'required|string|max:255',
        ]);

        // Prepare filters
        $filters = ['search' => $validated['search']];

        // Use the custom filter scope method to filter pages
        $helpPages = Page::where('page_type', 'help')->filter($filters)->get();

        // Check if any results were found
        if ($helpPages->isEmpty()) {
            return $this->notFoundResponse('home.request_not_found');
        }

        // If results found, create the resource collection
        $HelpResources = helpResource::collection($helpPages);

        return $this->successResponse(
            'home.home_success',
            [
                'help' => $HelpResources,
            ]
        );
    }
}
