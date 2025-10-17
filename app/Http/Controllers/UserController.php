<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Jika superadmin, tampilkan halaman daftar user
        if ($user->role === 'superadmin') {
            $users = User::all();
            return view('SuperAdmin', compact('users'));
        }

        // Jika admin
        if ($user->role === 'admin') {
            return view('approve');
        }

        // Jika user OPD
        if ($user->role === 'user') {
            return view('dashboard_bulan');
        }

        // Default fallback
        abort(403, 'Role tidak dikenali.');
    }
}
