<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Home\HomeController;
use App\Http\Controllers\Api\Address\AddressController;
use App\Http\Controllers\Api\Auth\ValidateOtpController;
use App\Http\Controllers\Api\Products\ProductController;
use App\Http\Controllers\Api\Addition\AdditionController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\UpdatePasswordController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('lang')->group(function () {
    Route::apiResource('auth', AuthController::class);
    Route::apiResource('validate-otp', ValidateOtpController::class);
    Route::apiResource('forget-password', ForgetPasswordController::class);
    Route::apiResource('update-password', UpdatePasswordController::class);
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('/home', HomeController::class)->only(['index']);
        Route::apiResource('/products', ProductController::class)->only(['index', 'show']);
        Route::post('search', [ProductController::class, 'search']);
        Route::post('filter', [ProductController::class, 'filter']);
        Route::apiResource('/menu', CategoryController::class)->only(['index']);
        Route::get('/menu/{main_category_id}/{sub_category_id?}', [CategoryController::class, 'show']);
        Route::apiResource('/address', AddressController::class)->only(['store']);
        Route::apiResource('additions/{product_id}', AdditionController::class)->only(['index']);
        Route::post('totalAdditions/{product_id}', [AdditionController::class, 'addAdditionToCart']);
        Route::post('addProduct/{product_id}', [ProductController::class, 'addProductToCart']);
        Route::post('/add-multiple-to-cart', [ProductController::class, 'addMultipleProductsToCart']);
        Route::get('/cart', [CartController::class, 'showCart']);
    });
});
