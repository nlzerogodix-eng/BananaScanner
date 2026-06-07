<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('welcome');
});

Route::get('register', [UserController::class, 'register'])->name('register');
Route::get('login', [UserController::class, 'login'])->name('login');

Route::post('registerValidate', [UserController::class, 'registerValidate'])->name('registerValidate');
Route::post('loginValidate', [UserController::class, 'loginValidate'])->name('loginValidate');

Route::group(['middleware' => 'auth'], function () {
    Route::get('dashboard', [ScanController::class, 'dashboard'])->name('dashboard');
    Route::get('home', function () {
        return view('home');
    })->name('home');
    
    Route::get('profile', [UserController::class, 'profile'])->name('profile.edit');
    Route::post('profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    Route::get('model', [ScanController::class, 'showModel'])->name('model');
    Route::view('support', 'support')->name('support');
    Route::view('about', 'about')->name('about');
    
    Route::post('logout', [UserController::class, 'logout'])->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::get('scan', [ScanController::class, 'showModel'])->name('model');
    Route::post('infer', [ScanController::class, 'processInference'])->name('infer');
});

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::put('users/{id}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('scans', [AdminController::class, 'scans'])->name('admin.scans');
    Route::get('scans/{id}/details', [ScanController::class, 'getScanDetails'])->name('admin.scans.details');
    Route::delete('scans/{id}', [AdminController::class, 'deleteScan'])->name('admin.scans.delete');
    Route::get('stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::get('profile/edit', [AdminController::class, 'editAdminProfile'])->name('admin.profile.edit');
    Route::post('profile/update', [AdminController::class, 'updateAdminProfile'])->name('admin.profile.update');
});