<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

Route::get('/', function () {
    return redirect()->route("login");
    // $role = Role::create(['name' => 'admin']); #user
    // $permission = Permission::creat(['name' => 'admin']) #user
});

Route::controller(DashboardController::class)->group(function() {
    Route::any('/admin-login', 'admin_login')->name('admin.login');
    Route::any('/admin-forgot-password', 'admin_forgot_password')->name('admin.forgot.password');
    Route::get('/admin-reset-password/{id}', 'admin_reset_password')->name('admin.reset.password');
    Route::any('/admin-update-password', 'admin_update_password')->name('admin.update.password');
});

Route::get('/dashboard', function () {
    return view('dashboard.master');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
