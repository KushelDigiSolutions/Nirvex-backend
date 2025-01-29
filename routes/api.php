<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\RegisterController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\SubcategoryController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\ServiceController;
use App\Http\Controllers\api\OrderApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::get('/register/getuser', [RegisterController::class, 'getUser']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/validateOtp', [LoginController::class, 'validateOtp']);
    Route::post('/resend-otp', [LoginController::class, 'resendOtp']);
    Route::post('/update-pincode', [LoginController::class, 'updatePincode']);

    Route::middleware('auth:api')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('subcategories', SubcategoryController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('services', ServiceController::class);
        Route::apiResource('orders', OrderApiController::class);
        
    });
});




