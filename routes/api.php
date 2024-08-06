<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Cart\CartController;
use App\Http\Controllers\Api\Home\HomeController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Address\AddressController;
use App\Http\Controllers\Api\Auth\ValidateOtpController;
use App\Http\Controllers\Api\Products\ProductController;
use App\Http\Controllers\Api\Settings\ProfileController;
use App\Http\Controllers\Api\Addition\AdditionController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Checkout\CheckoutController;
use App\Http\Controllers\Api\Favorite\FavoriteController;
use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\UpdatePasswordController;
use App\Http\Controllers\Api\Payment\StripePaymentController;

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

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::apiResource('/home', HomeController::class)->only(['index']);
        Route::apiResource('/products', ProductController::class)->only(['index', 'show']);
        Route::post('search', [ProductController::class, 'search']);
        Route::post('filter', [ProductController::class, 'filter']);
        Route::apiResource('/menu', CategoryController::class)->only(['index']);
        Route::get('/menu/{main_category_id}/{sub_category_id?}', [CategoryController::class, 'show']);
        Route::apiResource('/address', AddressController::class)->only(['index','store']);
        Route::post('/address/{address_id}', [AddressController::class, 'update']);
        Route::delete('/address/{address_id}', [AddressController::class, 'destroy']);

        Route::apiResource('additions/{product_id}', AdditionController::class)->only(['index']);
        Route::post('totalAdditions/{product_id}', [AdditionController::class, 'addAdditionToCart']);
        Route::post('addProduct/{product_id}', [ProductController::class, 'addProductToCart']);
        Route::post('/add-multiple-to-cart', [ProductController::class, 'addMultipleProductsToCart']);
        Route::get('/cart', [CartController::class, 'showCart']);
        Route::post('/checkout', [CheckoutController::class, 'checkout']);
        Route::post('/checkout/{orderId}/payment-method', [CheckoutController::class, 'updatePaymentMethod']);
        Route::post('stripe',[StripePaymentController::class,'stripePost']);
        Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
        Route::apiResource('favorites', FavoriteController::class)->only(['index', 'store', 'destroy']);
        Route::delete('favorites', [FavoriteController::class, 'destroyAll']);
        Route::apiResource('profile',ProfileController::class)->only('index' );
        Route::delete('/profile', [ProfileController::class, 'deleteAccount']);
        Route::post('/profile', [ProfileController::class, 'update']);
    });
});
