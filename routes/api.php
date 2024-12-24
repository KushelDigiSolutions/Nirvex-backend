<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\RegisterController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [RegisterController::class, 'login']);

    // Route::middleware('auth:api')->group(function () {
    //     Route::get('/dashboard', [UserController::class, 'dashboard']);
    //     Route::post('/logout', [UserController::class, 'logout']);
    // });
});


