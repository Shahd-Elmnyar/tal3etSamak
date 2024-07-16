<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ValidateOtpController;
use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\UpdatePasswordController;

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

Route::middleware('lang')->prefix('{locale}')->group(function () {
    // Use resource controllers
    Route::apiResource('auth', AuthController::class);
    Route::apiResource('validate-otp', ValidateOtpController::class);
    Route::apiResource('forget-password', ForgetPasswordController::class);
    Route::apiResource('validate-otp', ValidateOtpController::class);
    Route::apiResource('update-password', UpdatePasswordController::class);

    // Custom routes for login and logout within the AuthController
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});
