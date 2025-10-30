<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'id_unit' => ['required', 'integer'],
            // 'contact' => ['required', 'string', 'max:255'],
        ]);

        // dd($request);

        // Ambil username dari bagian depan email
        $email = $request->email;
        $baseUsername = strstr($email, '@', true);
        $username = $baseUsername;

        // Pastikan username unik
        $count = User::where('username', 'like', "{$baseUsername}%")->count();
        if ($count > 0) {
            $username = $baseUsername . ($count + 1);
        }


        // Simpan user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($request->password),
            'role' => 'user', // default sesuai enum
            'id_unit' => null,
            'contact' => null,
            // 'id_unit' => $request->id_unit,
            // 'contact' => $request->contact,
        ]);

        // Auth::login($user);

        return redirect()->route('login');
    }

    // LOGIN
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 🔹 Coba login sebagai superadmin/admin/user (dari tabel users)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (in_array($user->role, ['superadmin', 'admin', 'user'])) {
                return redirect()->route('dashboard.bulan');
            }

            return redirect()->route('landing.index');
        }

        // 🔹 Kalau gagal, coba login sebagai OPD (tabel units)
        if (Auth::guard('unit')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $unit = Auth::guard('unit')->user();

            // OPD langsung diarahkan ke dashboard_bulan juga
            return redirect()->route('dashboard.bulan');
        }

        // 🔹 Kalau dua-duanya gagal
        throw ValidationException::withMessages([
            'email' => 'Email atau password tidak cocok dengan data kami.',
        ]);
    }


    // LOGOUT
    public function logout(Request $request)
    {
        // 🔹 Jika yang login adalah OPD (guard: unit)
        if (Auth::guard('unit')->check()) {
            Auth::guard('unit')->logout();
        }
        // 🔹 Jika yang login adalah user biasa (guard: web)
        else {
            Auth::guard('web')->logout();
        }

        // Hapus session dan regenerasi token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan kembali ke halaman login atau beranda
        return redirect('/login');
    }
}
