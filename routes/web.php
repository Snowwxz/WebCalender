<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



// 🔹 LANDING & PUBLIC ROUTES
// Halaman utama (kalender default)
Route::get('/', [LandingController::class, 'index'])->name('landing.index');

// biar bulan bisa di pencet dibagian tahun
Route::get('/landing', function (Illuminate\Http\Request $request) {
    $month = $request->query('month');
    return view('landing', ['month' => $month]);
});

// Optional (kalau masih pakai view terpisah)
Route::get('/hari', fn() => view('landing_hari'))->name('landing.hari');
Route::get('/tahun', fn() => view('landing_tahun'))->name('landing.tahun');
Route::get('/bulan', function (Request $request) {
    // JS mengirim 'bulan' sebagai 0-based (0 = Januari).
    $bulanIndex = $request->query('bulan', null);

    // Kalau ada, ubah jadi 1-based (1 = Januari); kalau tidak ada, biarkan null.
    $month = is_null($bulanIndex) ? null : ((int)$bulanIndex + 1);

    // Tahun jika ada, kalau tidak ambil tahun sekarang
    $year = $request->query('tahun', date('Y'));

    // Kirim ke view dengan nama variabel yang umum: month & year
    return view('landing', [
        'month' => $month,
        'year' => (int)$year,
    ]);
})->name('landing.bulan');



//LANDING PAGE API (PUBLIC CALENDAR)
// Ambil agenda berdasarkan bulan
Route::get('/api/agenda/{year}/{month}', [LandingController::class, 'getByMonth'])
    ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}']);

// Ambil agenda berdasarkan tanggal tertentu (untuk modal show)
Route::get('/api/agenda/date/{date}', [LandingController::class, 'getByDate'])
    ->where(['date' => '\d{4}-\d{2}-\d{2}']);

// Ambil semua agenda dalam satu tahun
Route::get('/api/agenda/year/{year}', [LandingController::class, 'getByYear'])
    ->where(['year' => '[0-9]{4}']);

// Fitur search agenda publik
Route::get('/api/agenda/search', [LandingController::class, 'search'])
    ->name('agenda.search');


//DASHBOARD (HANYA LOGIN)
Route::get('/dashboard', function () {
    return view('dashboard_bulan');
})->middleware(['auth', 'verified'])->name('dashboard');


// Register
Route::get('/register', fn() => view('auth.register'))->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Login
Route::get('/login', fn() => view('auth.login'))->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


// PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
