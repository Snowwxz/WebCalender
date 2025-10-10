<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;

// ====================
// 🔹 Landing Pages
// ====================

// Halaman utama (kalender default: bulan)
Route::get('/', [LandingController::class, 'index'])->name('landing.index');

// Optional: kalau kamu memang punya view terpisah buat tampilan hari & tahun
Route::get('/hari', function () {
    return view('landing_hari');
})->name('landing.hari');

Route::get('/tahun', function () {
    return view('landing_tahun');
})->name('landing.tahun');

// ====================
// 🔹 API sementara (buat frontend ambil data kalender)
// ====================
Route::get('/agendas/{year}/{month}', [LandingController::class, 'getByMonth']);
Route::get('/agendas/date/{date}', [LandingController::class, 'getByDate']);
Route::get('/agendas/year/{year}', [LandingController::class, 'getByYear']);
Route::get('/agendas/search', [LandingController::class, 'search']);

// ====================
// 🔹 Dashboard & Auth
// ====================

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile (dari Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
