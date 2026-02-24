<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DojoController;
use App\Http\Controllers\Admin\OfficialController;
use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\FeeConfigurationController;
use App\Http\Controllers\Admin\ExamController;
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

    // 1. AKSES SEMUA LEVEL ADMIN (Termasuk Admin Dojo)
    Route::middleware(['role:pb,pengprov,pengcab,admin_dojo, penguji'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Member
        Route::post('/members/review', [MemberController::class, 'review'])->name('members.review');
        Route::resource('members', MemberController::class);

        // Manajemen Ujian (Sisi Peserta & View)
        Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
        Route::post('/exams/{exam}/add-member', [ExamController::class, 'registerMember'])->name('exams.register-member');

        // Fitur Pembayaran Massal (Sesuai error di Blade sebelumnya)
        Route::post('/exams/{exam}/bulk-payment', [ExamController::class, 'bulkPayment'])->name('exams.bulk-payment');

        // Fitur Penghapusan Peserta (Single & Bulk)
        Route::delete('/exams/participants/{participant}', [ExamController::class, 'removeMember'])->name('exams.remove-member');
        Route::delete('/exams/{exam}/bulk-remove', [ExamController::class, 'bulkRemoveMember'])->name('exams.bulk-remove-member');

        Route::get('/exams/{exam}/examiners', [\App\Http\Controllers\Admin\ExamExaminerController::class, 'edit'])
            ->name('exams.examiners.edit');

        Route::put('/exams/{exam}/examiners', [\App\Http\Controllers\Admin\ExamExaminerController::class, 'update'])
            ->name('exams.examiners.update');

    });

    // 2. AKSES STRUKTURAL (PB, Pengprov, Pengcab)
    Route::middleware(['role:pb,pengprov,pengcab'])->group(function () {
        Route::resource('dojos', DojoController::class);
        Route::resource('officials', OfficialController::class);

        // Manajemen Sesi Jadwal Ujian (Resource: store, edit, update, destroy)
        Route::resource('exams', ExamController::class)->except(['index', 'show']);
        Route::patch('/exams/{exam}/update-result', [ExamController::class, 'updateResult'])->name('exams.update-result');
    });

    // 3. AKSES TINGGI (PB & Pengprov)
    Route::middleware(['role:pb,pengprov'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('provinces', ProvinceController::class);

        // Konfigurasi Master Biaya Ujian
        Route::prefix('exams-fees')->name('exams.fees.')->group(function () {
            Route::get('/', [ExamController::class, 'feeIndex'])->name('index');
            Route::post('/', [ExamController::class, 'feeStore'])->name('store');
            Route::delete('/{id}', [ExamController::class, 'feeDestroy'])->name('destroy');
        });

        Route::resource('fees', FeeConfigurationController::class);
    });
});

// --- PROFILE UMUM ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';