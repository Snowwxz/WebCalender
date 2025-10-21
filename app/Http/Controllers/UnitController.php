<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Unit;

class UnitController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        Unit::create([
            'unit_name' => $request->unit_name,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'OPD berhasil ditambahkan.');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'superadmin') {
            $users = User::all();
            $units = Unit::all();
            return view('SuperAdmin', compact('users', 'units'));
        }

        if ($user->role === 'admin') {
            return view('approve');
        }

        if ($user->role === 'user') {
            return view('dashboard_bulan');
        }

        abort(403, 'Role tidak dikenali.');
    }

    public function update(Request $request, $id_unit)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $unit = Unit::findOrFail($id_unit);
        $unit->update([
            'unit_name' => $request->unit_name,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'OPD berhasil diperbarui.');
    }

    public function destroy($id_unit)
    {
        $unit = Unit::findOrFail($id_unit);
        $unit->delete();

        return redirect()->back()->with('success', 'OPD berhasil dihapus.');
    }
}
