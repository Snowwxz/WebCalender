<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



// 🔹 LANDING & PUBLIC ROUTES
Route::get('/', [LandingController::class, 'index'])->name('landing.index');

Route::get('/landing', function (Request $request) {
    $month = $request->query('month');
    return view('landing', ['month' => $month]);
});

Route::get('/hari', fn() => view('landing_hari'))->name('landing.hari');
Route::get('/tahun', fn() => view('landing_tahun'))->name('landing.tahun');
Route::get('/bulan', function (Request $request) {
    $bulanIndex = $request->query('bulan', null);
    $month = is_null($bulanIndex) ? null : ((int)$bulanIndex + 1);
    $year = $request->query('tahun', date('Y'));

    return view('landing', [
        'month' => $month,
        'year' => (int)$year,
    ]);
})->name('landing.bulan');



// 🔹 LANDING PAGE API
Route::get('/api/agenda/{year}/{month}', [LandingController::class, 'getByMonth'])
    ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}']);
Route::get('/api/agenda/date/{date}', [LandingController::class, 'getByDate'])
    ->where(['date' => '\d{4}-\d{2}-\d{2}']);
Route::get('/api/agenda/year/{year}', [LandingController::class, 'getByYear'])
    ->where(['year' => '[0-9]{4}']);
Route::get('/api/agenda/search', [LandingController::class, 'search'])
    ->name('agenda.search');



// 🔒 DASHBOARD (LOGIN DIBUTUHKAN)
Route::middleware(['auth', 'verified'])->group(function () {

    // ✅ Default dashboard → redirect otomatis ke /dashboard/bulan
    Route::get('/dashboard', function () {
        return redirect('/dashboard/bulan');
    })->name('dashboard');

    // ✅ Halaman dashboard bulan
    Route::get('/dashboard/bulan', function () {
        return view('dashboard_bulan');
    })->name('dashboard.bulan');

    // ✅ Tambahkan halaman dashboard hari & tahun agar tidak not found
    Route::get('/dashboard/hari', fn() => view('dashboard_hari'))->name('dashboard.hari');
    Route::get('/dashboard/tahun', fn() => view('dashboard_tahun'))->name('dashboard.tahun');
});



// 🔹 AUTH ROUTES
Route::get('/register', fn() => view('auth.register'))->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', fn() => view('auth.login'))->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');



// 🔹 PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 🔔 APPROVE ROUTE
    Route::get('/approve', fn() => view('approve'))->name('approve');
});
