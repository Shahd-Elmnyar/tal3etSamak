<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\Api\MainController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
    public function getProducts()
    {
        return Product::with(['images', 'sizes', 'additions'])->paginate(6);
    }


    

}
