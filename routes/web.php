<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// 🔹 LANDING & PUBLIC ROUTES
Route::get('/', [LandingController::class, 'index'])->name('landing.index');

Route::get('/landing', function (Request $request) {
    $month = $request->query('month');
    return view('landing', ['month' => $month]);
});

Route::get('/hari', function (Request $request) {
    $tanggal = $request->query('tanggal');
    return view('landing_hari', compact('tanggal'));
});

Route::get('/tahun', fn() => view('landing_tahun'))->name('landing.tahun');

Route::get('/bulan', function (Request $request) {
    $bulanIndex = $request->query('bulan');
    $month = isset($bulanIndex) ? ((int)$bulanIndex + 1) : null;
    $year = (int)$request->query('tahun', date('Y'));

    return view('landing', compact('month', 'year'));
})->name('landing.bulan');

// 🔹 LANDING PAGE API
Route::prefix('/api/agenda')->group(function () {
    Route::get('/{year}/{month}', [LandingController::class, 'getByMonth'])
        ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}']);

    Route::get('/date/{date}', [LandingController::class, 'getByDate'])
        ->where(['date' => '\d{4}-\d{2}-\d{2}']);

    Route::get('/year/{year}', [LandingController::class, 'getByYear'])
        ->where(['year' => '[0-9]{4}']);

    Route::get('/search', [LandingController::class, 'search'])->name('agenda.search');
});

// 🔒 DASHBOARD (LOGIN DIBUTUHKAN)
Route::middleware(['auth', 'verified'])->group(function () {

    // ✅ Redirect dashboard sesuai role
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;

        return match ($role) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'admin' => redirect()->route('approve'),
            default => redirect()->route('dashboard.bulan'),
        };
    })->name('dashboard');

    // ✅ CRUD Agenda
    Route::prefix('dashboard')->group(function () {
        Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
        Route::get('/agenda/create', [AgendaController::class, 'create'])->name('agenda.create');
        Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
        Route::get('/agenda/{id_agenda}', [AgendaController::class, 'show'])->name('agenda.show');
        Route::get('/agenda/{id_agenda}/edit', [AgendaController::class, 'edit'])->name('agenda.edit');
        Route::put('/agenda/{id_agenda}', [AgendaController::class, 'update'])->name('agenda.update');
        Route::delete('/agenda/{id_agenda}', [AgendaController::class, 'destroy'])->name('agenda.destroy');
        Route::get('/notification', [AgendaController::class, 'notification'])->name('agenda.notification');
    });

    // ✅ Route role USER
    Route::middleware('role:user')->group(function () {
        Route::get('/dashboard/hari', fn() => view('dashboard_hari'))->name('dashboard.hari');
        Route::get('/dashboard/bulan', [AgendaController::class, 'index'])->name('dashboard.bulan');
        Route::get('/dashboard/tahun', fn() => view('dashboard_tahun'))->name('dashboard.tahun');
    });

    // ✅ Route role ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::get('/approve', [ApproveController::class, 'index'])->name('approve');
        Route::put('/approve/agenda/{id_agenda}', [AgendaController::class, 'update'])->name('approve.update');
        Route::put('/agenda/{id_agenda}/status', [AgendaController::class, 'updateStatus'])->name('agenda.updateStatus');
    });

    // ✅ Route role SUPERADMIN
    Route::middleware('role:superadmin')->prefix('superadmin')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('superadmin.dashboard');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::post('/units', [UnitController::class, 'store'])->name('units.store');
        Route::resource('units', UnitController::class);
    });

    // ✅ PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🔹 AUTH ROUTES
Route::controller(AuthController::class)->group(function () {
    Route::get('/register', fn() => view('auth.register'))->name('register.form');
    Route::post('/register', 'register')->name('register');
    Route::get('/login', fn() => view('auth.login'))->name('login.form');
    Route::post('/login', 'login')->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
