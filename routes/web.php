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
use App\Http\Controllers\Admin\ExamScoreController;
use App\Http\Controllers\Admin\ExamExaminerController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\PaymentController;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Models\City;
use App\Models\Dojo;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| API DROPDOWN & VALIDASI
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get('/cities/{province_id}', function ($province_id) {
        return City::where('province_id', $province_id)->orderBy('name')->get();
    });

    Route::get('/dojos/{city_id}', function ($city_id) {
        return Dojo::where('city_id', $city_id)->orderBy('name')->get();
    });

    Route::get('/check-whatsapp', function (Request $request) {
        $exists = User::where('whatsapp', $request->query('number'))->exists();
        return response()->json(['exists' => $exists]);
    });
});

/*
|--------------------------------------------------------------------------
| PAYMENT (IURAN & UJIAN) - DOKU
|--------------------------------------------------------------------------
| - auth wajib
| - webhook (notify) TANPA auth + WAJIB CSRF-except
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // create pembayaran iuran (membership_fee)
    Route::post('/payments/membership', [PaymentController::class, 'createMembership'])
        ->name('payments.membership.create');

    // create pembayaran ujian (exam_fee)
    Route::post('/payments/exam', [PaymentController::class, 'createExam'])
        ->name('payments.exam.create');

    // return URL dari DOKU (user kembali setelah bayar)
    Route::get('/payments/return', [PaymentController::class, 'return'])
        ->name('payments.return');
});

// webhook DOKU (server to server)
Route::post('/payments/doku/notify', [PaymentController::class, 'dokuNotify'])
    ->name('payments.doku.notify');

/*
|--------------------------------------------------------------------------
| DASHBOARD MEMBER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| AREA ADMIN & PENGURUS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |------------------------------------------------------------------
        | 1. AKSES SEMUA LEVEL (PB, Pengprov, Pengcab, Admin Dojo, Penguji)
        |------------------------------------------------------------------
        */
        Route::middleware(['role:pb,pengprov,pengcab,admin_dojo,penguji'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Manajemen Member
            Route::post('/members/review', [MemberController::class, 'review'])->name('members.review');
            Route::resource('members', MemberController::class);

            // Manajemen Ujian (View Only & Registration)
            Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
            Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');

            // (perbaikan) jangan pakai 'admin/exams...' karena sudah prefix admin
            Route::get('/exams/{exam}/scoring/{member}', [ExamController::class, 'showScoring'])
                ->name('exams.scoring.member.show');

            Route::post('/exams/{exam}/add-member', [ExamController::class, 'registerMember'])->name('exams.register-member');
            Route::post('/exams/{exam}/bulk-payment', [ExamController::class, 'bulkPayment'])->name('exams.bulk-payment');

            Route::delete('/exams/participants/{participant}', [ExamController::class, 'removeMember'])->name('exams.remove-member');
            Route::delete('/exams/{exam}/bulk-remove', [ExamController::class, 'bulkRemoveMember'])->name('exams.bulk-remove-member');

            // --- MODUL PENILAIAN (Scoring) ---
            Route::get('/exams/{exam}/scoring', [ExamScoreController::class, 'index'])->name('exams.scoring');
            Route::get('/exams/{exam}/scoring/data', [ExamScoreController::class, 'show'])->name('exams.scoring.show');
            Route::post('/exams/{exam}/scoring', [ExamScoreController::class, 'store'])->name('exams.scoring.store');
            Route::post('/exams/{exam}/scoring/finalize', [ExamScoreController::class, 'finalize'])->name('exams.scoring.finalize');
        });

        /*
        |------------------------------------------------------------------
        | 2. AKSES STRUKTURAL (PB, Pengprov, Pengcab)
        |------------------------------------------------------------------
        */
        Route::middleware(['role:pb,pengprov,pengcab'])->group(function () {

            Route::resource('dojos', DojoController::class);
            Route::resource('officials', OfficialController::class);

            // Manajemen Sesi Jadwal Ujian
            Route::resource('exams', ExamController::class)->except(['index', 'show']);
            Route::patch('/exams/{exam}/update-result', [ExamController::class, 'updateResult'])->name('exams.update-result');

            // Manajemen Penugasan Penguji
            Route::get('/exams/{exam}/examiners', [ExamExaminerController::class, 'edit'])->name('exams.examiners.edit');
            Route::put('/exams/{exam}/examiners', [ExamExaminerController::class, 'update'])->name('exams.examiners.update');
        });

        /*
        |------------------------------------------------------------------
        | 3. AKSES TINGGI (PB & Pengprov)
        |------------------------------------------------------------------
        */
        Route::middleware(['role:pb,pengprov'])->group(function () {

            Route::resource('users', UserController::class);
            Route::resource('provinces', ProvinceController::class);

            // Konfigurasi Master Biaya Ujian
            Route::prefix('exams-fees')->name('exams.fees.')->group(function () {
                Route::get('/', [ExamController::class, 'feeIndex'])->name('index');
                Route::post('/', [ExamController::class, 'feeStore'])->name('store');
                Route::delete('/{id}', [ExamController::class, 'feeDestroy'])->name('destroy');
            });

            // Konfigurasi Iuran (membership fee)
            Route::resource('fees', FeeConfigurationController::class);
        });
    });

/*
|--------------------------------------------------------------------------
| PROFILE UMUM
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';