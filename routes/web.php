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
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('subcategories', SubcategoryController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        Route::delete('/products/image/remove', [ProductController::class, 'deleteImage'])->name('product.image.delete');
        Route::resource('orders', OrderController::class);
        Route::resource('pricings', PriceController::class);
        Route::resource('settings', SettingController::class);
        // Route::get('/admin/dashboard', HomeController::class)->name('admin.dashboard');
        
});

// Route::group(['middleware' => ['auth']], function() {
//     Route::resource('roles', RoleController::class);
//     Route::resource('users', UserController::class);
//     Route::resource('products', ProductController::class);
// });