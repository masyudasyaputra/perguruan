<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DojoController;
use App\Http\Controllers\Member\MemberDashboardController; // Pastikan ini di-import
use Illuminate\Support\Facades\Route;
use App\Models\City;
use App\Models\Dojo;

Route::get('/', function () {
    return view('welcome');
});

// --- API DROPDOWN DINAMIS (Dikelompokkan agar rapi) ---
Route::prefix('api')->group(function () {
    Route::get('/cities/{province_id}', function ($province_id) {
        return City::where('province_id', $province_id)->get();
    });

    Route::get('/dojos/{city_id}', function ($city_id) {
        return Dojo::where('city_id', $city_id)->get();
    });
});

// --- DASHBOARD BERDASARKAN ROLE ---

// Dashboard untuk Pengurus (PB, Pengprov, Pengcab, Dojo)
Route::middleware(['auth', 'role:pb,pengprov,pengcab,admin_dojo'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

// Dashboard untuk Member
Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
});

// --- MANAJEMEN USER (Hanya PB dan Pengprov) ---
Route::middleware(['auth', 'role:pb,pengprov'])->group(function () {
    Route::resource('admin/users', UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    Route::resource('admin/dojos', DojoController::class)->names([
        'index' => 'admin.dojos.index',
        'create' => 'admin.dojos.create',
        'store' => 'admin.dojos.store',
        'edit' => 'admin.dojos.edit',
        'update' => 'admin.dojos.update',
        'destroy' => 'admin.dojos.destroy',
    ]);
});

// --- PROFILE UMUM ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';