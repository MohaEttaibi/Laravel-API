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
});

Route::controller(AuthCotroller::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});