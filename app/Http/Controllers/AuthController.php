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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user(); // 🔹 Ambil data user yang login

            // 🔹 Cek role dan arahkan ke halaman sesuai peran
            if ($user->role === 'super_admin') {
                return redirect('/super_admin');
            } elseif ($user->role === 'admin') {
                return redirect('/approve');
            } else {
                return redirect('/dashboard/bulan');
            }
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }



    // LOGOUT
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
