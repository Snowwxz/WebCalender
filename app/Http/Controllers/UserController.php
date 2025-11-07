<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Jika superadmin, tampilkan halaman daftar user
        if ($user->role === 'superadmin') {
            $users = User::whereNotNull('id_user')->get();
            $units = \App\Models\Unit::all();
            return view('SuperAdmin', compact('users', 'units'));
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

    /**
     * Hapus user (hanya untuk superadmin)
     */
    public function destroy($id_user)
    {
        $user = User::where('id_user', $id_user)->first();
        if ($user) {
            $user->delete();
        }

        return redirect()->back()->with('success', 'User berhasil dihapus');
    }


    /**
     * Update data user (hanya untuk superadmin)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:user,admin',
            'id_unit' => 'required|exists:units,id_unit',
        ]);

        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->id_unit = $request->id_unit;

        // Jika password diisi, baru ubah
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->role = $request->role;
        $user->save();

        // Kirim pesan berbeda berdasarkan role
        if ($request->role === 'admin') {
            return redirect()->back()->with('success', 'Admin berhasil diperbarui.');
        } else {
            return redirect()->back()->with('success', 'User berhasil diperbarui.');
        }
    }

    /**
     * Simpan user baru (hanya untuk superadmin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:user,admin',
            'id_unit' => 'required|exists:units,id_unit'
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'id_unit' => $request->id_unit,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan!');
    }
}
