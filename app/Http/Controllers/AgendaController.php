<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    /**
     * Menampilkan semua agenda (untuk dashboard admin/user)
     */
    public function index()
    {
        // Bisa tambahkan filter kalau user biasa hanya lihat agenda miliknya
        $agendas = Agenda::with(['user', 'approver'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($agendas);
    }

    /**
     * Menyimpan agenda baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agenda_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        $validated['id_user'] = Auth::id() ?? $request->id_user; // fallback kalau belum auth
        $validated['status'] = 'pending';

        $agenda = Agenda::create($validated);

        return response()->json([
            'message' => 'Agenda berhasil dibuat.',
            'data' => $agenda
        ], 201);
    }

    /**
     * Menampilkan satu agenda (detail)
     */
    public function show($id)
    {
        $agenda = Agenda::with(['user', 'approver'])->findOrFail($id);

        return response()->json($agenda);
    }

    /**
     * Memperbarui data agenda
     */
    public function update(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

        $validated = $request->validate([
            'agenda_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'sometimes|required|date',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,approved,rejected',
        ]);

        // Kalau admin update (misal approve), simpan approved_by
        if ($request->status === 'approved' && Auth::check()) {
            $validated['approved_by'] = Auth::id();
        }

        $agenda->update($validated);

        return response()->json([
            'message' => 'Agenda berhasil diperbarui.',
            'data' => $agenda
        ]);
    }

    /**
     * Menghapus agenda
     */
    public function destroy($id)
    {
        $agenda = Agenda::findOrFail($id);
        $agenda->delete();

        return response()->json([
            'message' => 'Agenda berhasil dihapus.'
        ]);
    }
}
