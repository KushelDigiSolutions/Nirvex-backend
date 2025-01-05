<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\RegisterController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/validateOtp', [LoginController::class, 'validateOtp']);
    Route::post('/resend-otp', [LoginController::class, 'resendOtp']);

    Route::middleware('auth:api')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        
    });
});




