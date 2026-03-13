<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DojoController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamExaminerController;
use App\Http\Controllers\Admin\ExamScoreController;
use App\Http\Controllers\Admin\FeeConfigurationController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\OfficialController;
use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Models\City;
use App\Models\Dojo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// =====================================================
// API DROPDOWN & VALIDASI
// =====================================================
Route::prefix('api')->group(function () {
    Route::get('/cities/{province_id}', function ($province_id) {
        return City::where('province_id', $province_id)->orderBy('name')->get();
    })->name('api.cities');

    Route::get('/dojos/{city_id}', function ($city_id) {
        return Dojo::where('city_id', $city_id)->orderBy('name')->get();
    })->name('api.dojos');

    Route::get('/check-whatsapp', function (Request $request) {
        $exists = User::where('whatsapp', $request->query('number'))->exists();

        return response()->json([
            'exists' => $exists,
        ]);
    })->name('api.check-whatsapp');
});

// =====================================================
// DASHBOARD GATEWAY (SEMUA ROLE)
// =====================================================
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();

    $extraRoles = is_array($user->roles)
        ? $user->roles
        : (json_decode($user->roles ?? '[]', true) ?: []);

    $ownedRoles = collect(array_merge([$user->role], $extraRoles))
        ->filter()
        ->map(fn ($role) => strtolower(trim((string) $role)))
        ->unique()
        ->values()
        ->all();

    $adminRoles = [
        'pb',
        'pengprov',
        'pengcab',
        'admin_dojo',
        'penguji',
        'admin_pengprov',
        'admin_pengcab',
        'admin',
        'superadmin',
    ];

    if (count(array_intersect($adminRoles, $ownedRoles)) > 0) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('member.dashboard');
})->name('dashboard');

// =====================================================
// DASHBOARD MEMBER
// =====================================================
Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])
        ->name('member.dashboard');
});

// =====================================================
// AREA ADMIN & PENGURUS
// =====================================================
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // =============================================
        // 1. AKSES SEMUA LEVEL ADMIN/PENGURUS
        // =============================================
        Route::middleware(['role:pb,pengprov,pengcab,admin_dojo,penguji,admin_pengprov,admin_pengcab,admin,superadmin'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // -----------------------------------------
            // MEMBERS
            // review harus dideklarasikan sebelum resource
            // -----------------------------------------
            Route::post('/members/review', [MemberController::class, 'review'])
                ->name('members.review');

            Route::get('/members/review', function () {
                return redirect()
                    ->route('admin.members.create')
                    ->withErrors([
                        'review' => 'Silakan isi form pendaftaran terlebih dahulu.',
                    ]);
            })->name('members.review.redirect');

            Route::resource('members', MemberController::class)->except(['show']);

            // -----------------------------------------
            // EXAMS - VIEW / GENERAL ACCESS
            // -----------------------------------------
            Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
            Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');

            Route::get('/exams/{exam}/scoring/{member}', [ExamController::class, 'showScoring'])
                ->name('exams.scoring.member');

            Route::post('/exams/{exam}/add-member', [ExamController::class, 'registerMember'])
                ->name('exams.register-member');

            Route::post('/exams/{exam}/bulk-payment', [ExamController::class, 'bulkPayment'])
                ->name('exams.bulk-payment');

            Route::delete('/exams/participants/{participant}', [ExamController::class, 'removeMember'])
                ->name('exams.remove-member');

            Route::delete('/exams/{exam}/bulk-remove', [ExamController::class, 'bulkRemoveMember'])
                ->name('exams.bulk-remove-member');

            // -----------------------------------------
            // SCORING
            // -----------------------------------------
            Route::get('/exams/{exam}/scoring', [ExamScoreController::class, 'index'])
                ->name('exams.scoring');

            Route::get('/exams/{exam}/scoring/data', [ExamScoreController::class, 'show'])
                ->name('exams.scoring.show');

            Route::post('/exams/{exam}/scoring', [ExamScoreController::class, 'store'])
                ->name('exams.scoring.store');

            Route::post('/exams/{exam}/scoring/finalize', [ExamScoreController::class, 'finalize'])
                ->name('exams.scoring.finalize');

            // -----------------------------------------
            // PAYMENTS - ADMIN AREA
            // -----------------------------------------
            Route::post('/payments/iuran/bulk', [PaymentController::class, 'createIuranBulk'])
                ->name('payments.iuran.bulk');

            Route::post('/payments/iuran', [PaymentController::class, 'createIuran'])
                ->name('payments.iuran.create');

            Route::post('/payments/ujian', [PaymentController::class, 'createUjian'])
                ->name('payments.ujian.create');
        });

        // =============================================
        // 2. AKSES STRUKTURAL
        // =============================================
        Route::middleware(['role:pb,pengprov,pengcab,admin_pengprov,admin_pengcab,admin,superadmin'])->group(function () {
            Route::resource('dojos', DojoController::class);
            Route::resource('officials', OfficialController::class);

            Route::resource('exams', ExamController::class)->except(['index', 'show']);

            Route::patch('/exams/{exam}/update-result', [ExamController::class, 'updateResult'])
                ->name('exams.update-result');

            Route::get('/exams/{exam}/examiners', [ExamExaminerController::class, 'edit'])
                ->name('exams.examiners.edit');

            Route::put('/exams/{exam}/examiners', [ExamExaminerController::class, 'update'])
                ->name('exams.examiners.update');
        });

        // =============================================
        // 3. AKSES TINGGI
        // =============================================
        Route::middleware(['role:pb,pengprov,admin_pengprov,admin,superadmin'])->group(function () {
            Route::resource('users', UserController::class);
            Route::resource('provinces', ProvinceController::class);
            Route::resource('fees', FeeConfigurationController::class);

            Route::prefix('exams-fees')->name('exams.fees.')->group(function () {
                Route::get('/', [ExamController::class, 'feeIndex'])->name('index');
                Route::post('/', [ExamController::class, 'feeStore'])->name('store');
                Route::delete('/{id}', [ExamController::class, 'feeDestroy'])->name('destroy');
            });
        });
    });

// =====================================================
// PROFILE UMUM
// =====================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =====================================================
// PAYMENTS (DOKU) - PUBLIC ENDPOINTS
// =====================================================
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/doku/return', [PaymentController::class, 'dokuReturn'])->name('doku.return');
    Route::post('/doku/notify', [PaymentController::class, 'dokuNotify'])->name('doku.notify');
});

require __DIR__ . '/auth.php';