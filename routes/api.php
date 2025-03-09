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

// Route::middleware(['check.token.expiration'])->group(function () {
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::get('/register/getuser', [RegisterController::class, 'getUser']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/validateOtp', [LoginController::class, 'validateOtp']);
    Route::post('/resend-otp', [LoginController::class, 'resendOtp']);
    Route::post('/update-pincode', [LoginController::class, 'updatePincode']);

    Route::middleware('auth:api')->group(function () {
        Route::apiResource('/categories', CategoryController::class);
        Route::apiResource('subcategories', SubcategoryController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('services', ServiceController::class);
        Route::apiResource('orders', OrderApiController::class);
        Route::get('services', [ServiceController::class, 'getServices']);
        Route::get('service-detail/{serviceId}', [EcommerceApiController::class, 'getServiceDetails']);
                    
        Route::get('/sliders', [EcommerceApiController::class, 'listSliders']);
        Route::get('/listProductsBySubCategory', [EcommerceApiController::class, 'listProductsBySubCategory']);
        Route::get('/product-detail/{productId}', [EcommerceApiController::class, 'getProductDetail']);
        Route::post('/add-rating', [EcommerceApiController::class, 'addRating']);
        Route::put('/update-rating/{ratingId}', [EcommerceApiController::class, 'updateRating']); // Update review/rating
        Route::delete('/delete-rating/{ratingId}', [EcommerceApiController::class, 'deleteRating']); // Delete review/rating
        Route::get('/variant-reviews/{variantId}', [EcommerceApiController::class, 'getVariantReviews']); 
        Route::post('/addresses', [EcommerceApiController::class, 'createAddress']);
        Route::get('/addresses', [EcommerceApiController::class, 'getAddresses']);
        Route::put('/address/{id}', [EcommerceApiController::class, 'updateAddress']);
        Route::delete('/address/{id}', [EcommerceApiController::class, 'deleteAddress']);
        Route::get('/setAddress/{id}', [EcommerceApiController::class, 'setAddress']);
        Route::get('/search', [EcommerceApiController::class, 'search']);        
        Route::post('/cart/add', [EcommerceApiController::class, 'addToCart']);
        Route::post('/cart/remove', [EcommerceApiController::class, 'removeFromCart']);
        Route::delete('/cart/delete-item', [EcommerceApiController::class, 'deleteCartItem']);
        Route::delete('/cart/clear', [EcommerceApiController::class, 'clearCart']);
        Route::get('/cart', [EcommerceApiController::class, 'getCartItems']);
        Route::get('/checkout', [EcommerceApiController::class, 'checkout']);
        Route::post('/orderCreate', [EcommerceApiController::class, 'createOrder']);
        Route::post('/cart/add-to-cart', [EcommerceApiController::class, 'addToCart']);
        Route::delete('/cart/remove/{itemId}', [EcommerceApiController::class, 'deleteFromCart']);
        Route::delete('/cart/clear', [EcommerceApiController::class, 'clearCart']);
        Route::post('/cart/checkout', [EcommerceApiController::class, 'calculateCheckout']);
        Route::get('/all-coupons', [EcommerceApiController::class, 'listAvailableCoupons']);
        Route::post('/apply-new-coupon', [EcommerceApiController::class, 'applyCoupon']);
        Route::post('/remove-coupon', [EcommerceApiController ::class, 'removeCoupon']);
        Route::post('/create-order', [EcommerceApiController ::class, 'createOrder']);
        Route::post('/update-profile', [EcommerceApiController ::class, 'updateProfile']);
        Route::put('/update-profile-photo', [EcommerceApiController ::class, 'updateProfilePhoto']);
        Route::get('/my-orders', [EcommerceApiController ::class, 'orderHistory']);
        Route::post('/mobile-update', [EcommerceApiController ::class, 'mobileUpdate']);
        Route::post('/email-update', [EcommerceApiController ::class, 'emailUpdate']);
        Route::get('/order-detail/{orderId}', [EcommerceApiController ::class, 'orderDetail']);
        Route::get('/order-txn/{orderId}', [EcommerceApiController ::class, 'orderTransaction']);
        Route::post('/verify-mobile', [EcommerceApiController ::class, 'verifyMobile']);
        Route::post('/verify-email', [EcommerceApiController ::class, 'verifyEmail']);


        //vendor level api

        Route::get('/search-vendor-order/{orderId}', [EcommerceApiController ::class, 'searchVendorOrder']);
        Route::get('/vendor-dashboard/', [EcommerceApiController ::class, 'vendorDashboard']);
        Route::get('/vendor-orders/', [EcommerceApiController ::class, 'vendorOrders']);
        Route::get('/update-order-status/', [EcommerceApiController ::class, 'updateVendorOrderStatus']);
        Route::get('/get-vendor-stock/', [EcommerceApiController ::class, 'getVendorStocks']);
        Route::get('/update-vendor-stocks/', [EcommerceApiController ::class, 'updateVendorEarnings']);
        Route::get('/get-vendor-earnings/', [EcommerceApiController ::class, 'getVendorEarnings']);
    });
});

// }); // 




