<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return view('landing');
});

Route::get('/landing', function (Illuminate\Http\Request $request) {
    $month = $request->query('month');
    return view('landing', ['month' => $month]);
});

Route::get('/hari', function () {
    return view('landing_hari');
});

Route::get('/tahun', function () {
    return view('landing_tahun');
});

// Dashboard (cuma buat user login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// =============================
// 🔐 AUTH ROUTES
// =============================

// Form register
Route::get('/register', function () {
    return view('auth.register');
})->name('register.form');

// Proses register
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Form login
Route::get('/login', function () {
    return view('auth.login');
})->name('login.form');

// Proses login
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// =============================
// 👤 PROFILE ROUTES
// =============================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
