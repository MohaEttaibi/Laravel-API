<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

Route::get('/', function () {
    return redirect()->route("login");
    // $role = Role::create(['name' => 'admin']); #user
    // $permission = Permission::creat(['name' => 'admin']) #user
});

Route::controller(BrandController::class)->group(function() {
    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/add-brand', 'add_brand')->name('add.brand');
        Route::get('/view-brand', 'view_brand')->name('view.brand');
        Route::any('/store-brand', 'store_brand')->name('store.brand');
        Route::get('/edit-brand/{id}', 'edit_brand')->name('edit.brand');
        Route::any('/update-brand', 'update_brand')->name('update.brand');
        Route::any('/delete-brand/{id}', 'delete_brand')->name('delete.brand');
    });
});

Route::controller(ProductController::class)->group(function() {
    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/add-product', 'add_product')->name('add.product');
        Route::get('/view-product', 'view_product')->name('view.product');
        Route::any('/store-product', 'store_product')->name('store.product');
        Route::get('/edit-product/{id}', 'edit_product')->name('edit.product');
        Route::any('/update-product', 'update_product')->name('update.product');
        Route::get('/delete-product/{id}', 'delete_product')->name('delete.product');
    });
});

Route::controller(DashboardController::class)->group(function() {
    Route::any('/admin-login', 'admin_login')->name('admin.login');
    Route::any('/admin-forgot-password', 'admin_forgot_password')->name('admin.forgot.password');
    Route::get('/admin-reset-password/{id}', 'admin_reset_password')->name('admin.reset.password');
    Route::any('/admin-update-password', 'admin_update_password')->name('admin.update.password');

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function(){
        Route::get('/dashboard', 'dashboard')->name('dashboard');
    });
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified', 'role:admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';