<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    /**
     * ✅ Tampilkan daftar agenda di dashboard.
     */
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('m'));

        // Query dasar
        $query = Agenda::with(['user', 'approver'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc');

        // Jika user hanya ingin lihat agendanya sendiri
        if ($request->query('only_my')) {
            $query->where('id_user', Auth::id());
        }

        $agenda = $query->get();

        // Jika request dari AJAX → JSON, kalau tidak → view biasa
        if ($request->wantsJson() || $request->isJson()) {
            return response()->json($agenda);
        }

        // kalau dashboard_bulan kamu pakai view biasa
        return view('dashboard_bulan', compact('agenda', 'year', 'month'));
    }

    public function create()
    {
        return view('agenda_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agenda_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        try {
            // ❗ Pastikan user id valid
            $userId = Auth::id() ?: 1; // fallback user 1 (harus ada di users)

            Agenda::create([
                "agenda_name" => $validated['agenda_name'],
                "description" => $validated['description'] ?? null,
                "date" => $validated['date'],
                "location" => $validated['location'] ?? null,
                "status" => 'pending',
                "id_user" => $userId,
                "approved_by" => null,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Agenda berhasil ditambahkan ke database (status pending).');
        } catch (\Throwable $e) {
            Log::error('❌ Failed to save agenda: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan agenda: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Lihat detail agenda (optional).
     */
    public function show($id)
    {
        $agenda = Agenda::with(['user', 'approver'])->findOrFail($id);
        return response()->json($agenda);
    }

    /**
     * ✅ Update agenda.
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

        if (isset($validated['status']) && $validated['status'] === 'approved' && Auth::check()) {
            $validated['approved_by'] = Auth::id();
        }

        $agenda->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Agenda berhasil diperbarui.');
    }

    /**
     * ✅ Hapus agenda.
     */
    public function destroy(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);
        $agenda->delete();

        return redirect()
            ->back()
            ->with('success', 'Agenda berhasil dihapus.');
    }
}
