<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\RegisterController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\SubcategoryController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\ServiceController;
use App\Http\Controllers\api\EcommerceApiController;
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
        Route::get('services', [ServiceController::class, 'getServices']);
       

                
        Route::post('/addresses', [EcommerceApiController::class, 'createAddress']);
        Route::get('/addresses', [EcommerceApiController::class, 'getAddresses']);
        Route::put('/address/{id}', [EcommerceApiController::class, 'updateAddress']);
        Route::delete('/address/{id}', [EcommerceApiController::class, 'deleteAddress']);
        Route::get('/setAddress/{id}', [EcommerceApiController::class, 'setAddress']);
        Route::get('/products/search', [EcommerceApiController::class, 'search']);        
        Route::post('/cart/add', [EcommerceApiController::class, 'addToCart']);
        Route::post('/cart/remove', [EcommerceApiController::class, 'removeFromCart']);
        Route::delete('/cart/delete-item', [EcommerceApiController::class, 'deleteCartItem']);
        Route::delete('/cart/clear', [EcommerceApiController::class, 'clearCart']);
        Route::get('/cart', [EcommerceApiController::class, 'getCartItems']);
        Route::get('/checkout', [EcommerceApiController::class, 'checkout']);
        Route::post('/orderCreate', [EcommerceApiController::class, 'orderCreate']);
    });
});




