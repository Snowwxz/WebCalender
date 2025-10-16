<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AgendaController extends Controller
{
    /**
     * ✅ Tampilkan daftar agenda di dashboard.
     */
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('m'));

        $query = Agenda::with(['user', 'approver'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc');

        if ($request->query('only_my')) {
            $query->where('id_user', Auth::id());
        }

        $agenda = $query->get();

        if ($request->wantsJson() || $request->isJson()) {
            return response()->json($agenda);
        }

        return view('dashboard_bulan', compact('agenda', 'year', 'month'));
    }

    /**
     * ✅ Form pengajuan agenda.
     */
    public function create()
    {
        return view('agenda_create');
    }

    /**
     * ✅ Simpan agenda baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agenda_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'submitted_by' => 'nullable|string|max:255',
            'person_in_charge' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'location' => 'nullable|string|max:255',
            'involved_institution' => 'nullable|string|max:500',
        ]);

        try {
            $userId = Auth::id() ?: 1;

            $agenda = new Agenda();
            $agenda->agenda_name = $validated['agenda_name'];
            $agenda->description = $validated['description'] ?? null;
            $agenda->submitted_by = $validated['submitted_by'] ?? null;
            $agenda->person_in_charge = $validated['person_in_charge'] ?? null;
            $agenda->date = $validated['date'];
            $agenda->start_time = $validated['start_time'] ?? null;
            $agenda->end_time = $validated['end_time'] ?? null;
            $agenda->location = $validated['location'] ?? null;
            $agenda->involved_institution = $validated['involved_institution'] ?? null;
            $agenda->status = 'pending';
            $agenda->id_user = $userId;
            $agenda->approved_by = null;
            $agenda->save();

            return redirect()
                ->back()
                ->with('success', '✅ Agenda berhasil ditambahkan (status: pending).');

        } catch (\Throwable $e) {
            Log::error('❌ Gagal menyimpan agenda: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan agenda: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Lihat detail agenda.
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
            'submitted_by' => 'nullable|string|max:255',
            'person_in_charge' => 'nullable|string|max:255',
            'date' => 'sometimes|required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'location' => 'nullable|string|max:255',
            'involved_institution' => 'nullable|string|max:500',
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
    public function destroy($id)
    {
        $agenda = Agenda::findOrFail($id);
        $agenda->delete();

        return redirect()
            ->back()
            ->with('success', 'Agenda berhasil dihapus.');
    }
}
