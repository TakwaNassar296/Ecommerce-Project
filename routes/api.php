<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;

Route::prefix('auth')->group(function(){

    Route::post('register' , [AuthController::class , 'register']);
    Route::post('login' , [AuthController::class , 'login']);
    Route::post('refresh' , [AuthController::class , 'refresh']);
    Route::post('forget-password' , [AuthController::class , 'forgetpassword']);
    Route::post('reset-password' , [AuthController::class , 'resetPassword']);

});

Route::middleware(['auth:sanctum'])->group(function(){

    Route::get('/user' , [AuthController::class , 'user']);
    Route::post('/logout' , [AuthController::class , 'logout']);


    //Cart Routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/update/{itemId}', [CartController::class, 'update']);
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);

    //Product Routes
    Route::get('/product' , [ProductController::class , 'index']);
    Route::get('/product/{slug}', [ProductController::class, 'show']);

    //Category Routes
    Route::get('/category' , [CategoryController::class , 'index']);
    Route::get('/category/{slug}', [CategoryController::class, 'show']);


    //Order Routes
    Route::get('/order' , [OrderController::class , 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancel']);

    // Checkout Routes
    Route::get('/pay', [CheckoutController::class, 'pay']);
    Route::post('/payment/callback', [CheckoutController::class, 'paymentCallback']);

});

    Route::get('/payment/success', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Payment completed successfully.'
        ]);
    })->name('payment.success');

    Route::get('/payment/failed', function () {
        return response()->json([
            'status' => 'fail',
            'message' => 'Payment failed.'
        ]);
    })->name('payment.failed');




