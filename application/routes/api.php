<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\User\Auth\LoginController as UserLoginController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Product\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('user')->group(function () {
    Route::post('login', [UserLoginController::class, 'login']);
    Route::post('otp-verify', [UserLoginController::class, 'check_otp']);
    Route::post('otp-resend', [UserLoginController::class, 'resend_otp']);
    Route::post('register', [UserLoginController::class, 'register']);
    Route::post('password-reset', [UserLoginController::class, 'changePassword']);

    // After Login and Email Verified
    Route::middleware(['auth:api'])->group(function () {
        /*Auth Apis*/
        Route::post('logout', [UserLoginController::class, 'logout']);
        //category
        Route::prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'categoryList']);
            Route::post('/', [CategoryController::class, 'storeCategory']);
            Route::put('/{id}', [CategoryController::class, 'updateCategory']);
        });
    });
});
