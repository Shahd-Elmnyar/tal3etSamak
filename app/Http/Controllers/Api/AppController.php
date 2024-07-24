<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\MainController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AppController extends MainController
{
    use AuthorizesRequests, ValidatesRequests;
    public $user;

    public function __construct()
    {
        // Apply middleware to check authorization on every request
        $this->middleware(function ($request, $next) {
            $this->user = $this->checkAuthorization($request);
            return $next($request);
        });
    }
}
