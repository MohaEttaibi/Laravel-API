<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AuthCotroller;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(DashboardController::class)->group(function() {
    Route::get('home', 'home');
    Route::get('products-by-brand/{brand}', 'products_by_brand');
    Route::get('products-view/{id}', 'products_view');
    Route::get('filters/{filter}', 'filters');
    Route::get('filters/{brand}/{filter}', 'filters_by_brand');
    Route::get('add-favorite/{product_id}', 'add_favorite')->middleware('auth:sanctum');
    Route::get('fetch-favorite', 'fetch_favorite')->middleware('auth:sanctum');
    Route::get('favorite-remove/{id}', 'remove_favorite')->middleware('auth:sanctum');
    Route::any('add-cart', 'add_cart')->middleware('auth:sanctum');
    Route::get('cart', 'cart')->middleware('auth:sanctum');
    Route::get('cart-remove/{id}', 'remove_cart')->middleware('auth:sanctum');
    Route::get('cart-remove-all', 'remove_cart_all')->middleware('auth:sanctum');
    Route::get('create-orders', 'create_orders')->middleware('auth:sanctum');
    Route::any('/phone-verify', 'phone_verify')->middleware('auth:sanctum');
    Route::any('/otp-verify', 'otp_verify');
    Route::get('authorized/{id}', 'authorized');
    Route::get('declined/{id}', 'declined');
    Route::get('cancelled/{id}', 'cancelled');
    Route::get('orders-fetch', 'orders_fetch');
    Route::any('live-chat', 'live_chat');
});

Route::controller(AuthCotroller::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('/logout', 'logout');
});