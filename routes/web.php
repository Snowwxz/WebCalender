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
    $year = $request->query('year', date('Y'));
    return view('landing', ['month' => $month, 'year' => (int)$year]);
});

Route::get('/hari', function (Request $request) {
    $tanggal = $request->query('tanggal');
    return view('landing_hari', compact('tanggal'));
});

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

    // ✅ Dashboard utama: arahkan sesuai role user
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'superadmin', 'user'])) {
            return redirect()->route('dashboard.bulan');
        }
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
        Route::post('/agenda/{id_agenda}/reject', [AgendaController::class, 'reject'])->name('agenda.reject');
        Route::get('notification', [AgendaController::class, 'notification'])->name('agenda.notification');

        Route::get('/agenda/{id}/logs', [AgendaController::class, 'logs'])->name('agenda.logs');
        Route::get('/api/agenda/{id_agenda}/logs', [AgendaController::class, 'getAgendaLogs'])->name('api.agenda.logs');
    });

    // ✅ API Notifikasi untuk dropdown header
    Route::get('/api/notifications', [AgendaController::class, 'getNotifications'])->name('api.notifications');

    // ✅ API Agenda per bulan untuk dashboard (menampilkan publik + privasi)
    Route::get('/api/dashboard/agenda/{year}/{month}', [AgendaController::class, 'getByMonth'])
        ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}']);

    // ✅ API External Agenda
    Route::get('/api/external/agendas', [AgendaController::class, 'fetchExternalAgendas'])->name('api.external.agendas');
    Route::get('/api/external/agendas/date', [AgendaController::class, 'getExternalAgendasByDate'])->name('api.external.agendas.date');
    Route::get('/api/merged/agendas', [AgendaController::class, 'getMergedAgendas'])->name('api.merged.agendas');


    // ✅ Route khusus tiap role
    Route::middleware('role:superadmin,admin,user')->group(function () {
        Route::get('/dashboard/hari', fn() => view('dashboard_hari'))->name('dashboard.hari');
        Route::get('/dashboard/hari/data', [AgendaController::class, 'getAgendaHari'])->name('dashboard.hari.data');
        Route::get('/dashboard/bulan', [AgendaController::class, 'index'])->name('dashboard.bulan');
        Route::get('/dashboard/tahun', fn() => view('dashboard_tahun'))->name('dashboard.tahun');
    });

    Route::middleware('role:admin')->group(function () {});

    Route::middleware('role:superadmin')->group(function () {
        Route::get('/superadmin', [UserController::class, 'index'])->name('superadmin.dashboard');

        Route::delete('/superadmin/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/superadmin/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::post('/superadmin/users/store', [UserController::class, 'store'])->name('users.store');

        Route::post('/superadmin/units', [UnitController::class, 'store'])->name('units.store');

        Route::prefix('superadmin')->group(function () {
            Route::resource('units', UnitController::class);
        });
    });
});


// 🔹 AUTH ROUTES
Route::get('/register', fn() => view('auth.register'))->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/login', fn() => view('auth.login'))->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// 🔹 PROFILE ROUTES + APPROVE
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Password update route
    Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');

    Route::get('/approve', [ApproveController::class, 'index'])->name('approve');
    Route::put('/approve/agenda/{id_agenda}', [AgendaController::class, 'update'])->name('approve.update');
    Route::put('/agenda/{id_agenda}/status', [AgendaController::class, 'updateStatus'])->name('agenda.updateStatus');
});
