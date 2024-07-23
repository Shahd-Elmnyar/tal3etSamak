<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetAppLang
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            $userLang = Auth::user()->lang; // Get the user's language preference

            if (in_array($userLang, config('app.available_locales'))) {
                App::setLocale($userLang); // Set the locale from user's preference
            } else {
                abort(400); // Abort if the language is not available
            }
        } else {
            // Fallback to header if user is not authenticated
            $userLang = $request->header('Accept-Language');

            if (!in_array($userLang, config('app.available_locales'))) {
                abort(400); // Abort if the language is not available
            }

            App::setLocale($userLang);
        }

        return $next($request);
    }
}
