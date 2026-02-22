<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DojoController;
use App\Http\Controllers\Admin\OfficialController;
use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Member\MemberDashboardController;
use Illuminate\Support\Facades\Route;
use App\Models\City;
use App\Models\Dojo;
use App\Models\User;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// --- API DROPDOWN & VALIDASI ---
Route::prefix('api')->group(function () {
    Route::get('/cities/{province_id}', function ($province_id) {
        return City::where('province_id', $province_id)->get();
    });
    Route::get('/dojos/{city_id}', function ($city_id) {
        return Dojo::where('city_id', $city_id)->get();
    });
    
    // API untuk cek WhatsApp duplikat di Database
    Route::get('/check-whatsapp', function (Request $request) {
        $exists = User::where('whatsapp', $request->query('number'))->exists();
        return response()->json(['exists' => $exists]);
    });
});

// --- DASHBOARD MEMBER ---
Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
});

// --- AREA ADMIN & PENGURUS ---
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // 1. AKSES SEMUA LEVEL ADMIN (Dashboard Utama)
    Route::middleware(['role:pb,pengprov,pengcab,admin_dojo'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // --- MANAJEMEN MEMBER & CHECKOUT ---
        // Route Review/Checkout diletakkan sebelum resource agar tidak bentrok
        Route::post('/members/review', [MemberController::class, 'review'])->name('members.review');
        Route::resource('members', MemberController::class);
    });

    // 2. AKSES STRUKTURAL (Kelola Dojo & Pengurus)
    Route::middleware(['role:pb,pengprov,pengcab'])->group(function () {
        Route::resource('dojos', DojoController::class);
        Route::resource('officials', OfficialController::class);
    });

    // 3. AKSES MANAJEMEN USER & WILAYAH (PB & Pengprov)
    Route::middleware(['role:pb,pengprov'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('provinces', ProvinceController::class);
    });
});

// --- PROFILE UMUM ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';