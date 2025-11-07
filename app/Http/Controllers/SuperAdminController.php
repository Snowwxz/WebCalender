<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Unit;

class SuperAdminController extends Controller
{
    public function index()
    {
        // Ambil semua user dan relasi unit-nya
        $users = User::with('unit')->get();

        // Ambil semua data unit untuk dropdown/tambah user
        $units = Unit::all();

        // Kirim ke view
        return view('SuperAdmin', compact('users', 'units'));
    }
}
