<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    // tampilkan daftar agenda
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

    // ini buat munculin form create
    public function create()
    {
        return view('agenda_create');
    }

    // ini buat simpan agenda
    public function store(Request $request)
    {
       // Validasi input
    $validated = $request->validate([
        'agenda_name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'date' => 'required|date',
        'location' => 'nullable|string|max:255',
        'person_in_charge' => 'nullable|string|max:255',
        'involved_institution' => 'nullable|string|max:255',
    ]);

    try {
        // Ambil user id
        $userId = Auth::id() ?: 1;

        // Simpan ke database
        $agenda = new Agenda();
        $agenda->agenda_name = $validated['agenda_name'];
        $agenda->description = $validated['description'] ?? null;
        $agenda->date = $validated['tanggal'];
        $agenda->location = $validated['lokasi'] ?? null;
        $agenda->person_in_charge = $validated['penanggung_jawab'] ?? null;
        $agenda->involved_institution = $validated['instansi_ikut'] ?? null;
        $agenda->status = 'pending';
        $agenda->id_user = $userId;
        $agenda->approved_by = null;
        $agenda->save();

        return redirect()
            ->back()
            ->with('success', '✅ Agenda berhasil ditambahkan ke database (status pending).');

    } catch (\Throwable $e) {
        Log::error('❌ Gagal menyimpan agenda: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        return back()
            ->withInput()
            ->with('error', 'Gagal menambahkan agenda ke database: ' . $e->getMessage());
    }
    }

    // lihat detail agenda (show)
    public function show($id)
    {
        $agenda = Agenda::with(['user', 'approver'])->findOrFail($id);
        return response()->json($agenda);
    }

    //tampilkan form pengeditan
    public function edit($id)
    {
        // Ambil data agenda sesuai ID
        $agenda = Agenda::findOrFail($id);

        // Kirim ke view edit
        return view('agenda_edit', compact('agenda'));
    }

    //khusus simpan update-an form agenda yo
   public function update(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

        // Validasi input sama seperti store
        $validated = $request->validate([
            'agenda_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tanggal' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'instansi_ikut' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'waktu_pelaksanaan' => 'nullable|string|max:10', // tambahan kalau pakai field time
        ]);

        // Map ke field database
        $agenda->agenda_name = $validated['agenda_name'];
        $agenda->description = $validated['description'] ?? null;
        $agenda->date = $validated['tanggal'];
        $agenda->location = $validated['lokasi'] ?? null;
        $agenda->person_in_charge = $validated['penanggung_jawab'] ?? null;
        $agenda->involved_institution = $validated['instansi_ikut'] ?? null;
        $agenda->time = $validated['waktu_pelaksanaan'] ?? null; // jika ada kolom waktu
        $agenda->status = $validated['status'] ?? $agenda->status;

        // Jika status diubah ke approved, set approved_by
        if (($validated['status'] ?? '') === 'approved' && Auth::check()) {
            $agenda->approved_by = Auth::id();
        }

        $agenda->save();

        return redirect()
            ->back()
            ->with('success', 'Agenda berhasil diperbarui.');
    }

    // ini buat hapus agenda ges
    public function destroy(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);
        $agenda->delete();

        return redirect()
            ->back()
            ->with('success', 'Agenda berhasil dihapus.');
    }
}
