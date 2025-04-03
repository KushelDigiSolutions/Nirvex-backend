<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\AddressController;

Route::get('/', function () {
    return view('login');
});

Auth::routes();

 Route::get('/admin/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.dashboard');

// Route::get('/admin/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('admin.categories');

Route::middleware(['role:admin'])->prefix('admin')->group(function(){
    Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('subcategories', SubcategoryController::class);
        Route::delete('/products/image/remove', [ProductController::class, 'deleteImage'])->name('product.image.delete');
        Route::resource('orders', OrderController::class);
        Route::post('/update-order-status', [OrderController::class,'updateStatus'])->name('update.order.status');
        Route::get('/get-sellers', [OrderController::class, 'getSellers'])->name('get-sellers');
        Route::put('/apply-seller-order', [OrderController::class, 'applySellerOrder'])->name('apply-seller-order');
        Route::resource('pricings', PriceController::class);
        // Route::resource('settings', SettingController::class);
        Route::resource('services', ServiceController::class);
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::resource('sellers', SellerController::class);

        Route::get('customers', [UserController::class, 'getCustomer'])->name('customers.index');
        Route::get('customers/{id}', [UserController::class, 'getCustomerById'])->name('customers.getById');
        Route::get('clients', [UserController::class, 'getClient'])->name('clients.index');
        Route::get('/search-product', [ProductController::class, 'search']);
        Route::post('/orders/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::match(['get', 'post'], '/profile/update', [ProfileController::class, 'updateProfile']);  
        Route::get('sellers', [SellerController::class, 'index'])->name('sellers.index');  
        Route::post('addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::delete('addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::get('addresses/{id}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
        Route::put('addresses/{id}', [AddressController::class, 'update'])->name('addresses.update');
    
    

    Route::post('sellers/update-seller-active', [SellerController::class, 'updateSellerActive'])->name('sellers.updateSellerActive');
    Route::post('sellers/get-seller-active', [SellerController::class, 'getSellerActive'])->name('sellers.getSellerActive');
    Route::post('sellers/update-cs-seller', [SellerController::class, 'updateCustomerSo'])->name('sellers.updateCustomerSo');
    Route::post('sellers/update-cs-details', [SellerController::class, 'updateSellerDetails'])->name('sellers.updateSellerDetails');
    Route::post('sellers/update-cs-shop-image', [SellerController::class, 'updateCustomerShopImage'])->name('sellers.updateCustomerShopImage');

    Route::post('sellers/update-cs-gst-image', [SellerController::class, 'updateCustomerGstImage'])->name('sellers.updateCustomerGstImage');
    Route::post('sellers/update-cs-fssi-image', [SellerController::class, 'updateCustomerFssiImage'])->name('sellers.updateCustomerFssiImage');
    Route::post('sellers/update-cs-adhar-front-image', [SellerController::class, 'updateCustomerAdharFrontImage'])->name('sellers.updateCustomerAdharFrontImage');
    Route::post('sellers/update-cs-adhar-back-image', [SellerController::class, 'updateCustomerAdharBackImage'])->name('sellers.updateCustomerAdharBackImage');
    Route::post('sellers/update-cs-pan-image', [SellerController::class, 'updateCustomerPanImage'])->name('sellers.updateCustomerPanImage');
    Route::post('sellers/update-cs-bnk-image', [SellerController::class, 'updateCustomerBnkImage'])->name('sellers.updateCustomerBnkImage');

});

