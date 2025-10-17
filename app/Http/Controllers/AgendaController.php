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
            'person_in_charge' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'location' => 'nullable|string|max:255',
            'involved_institution' => 'nullable|string|max:500',
            'is_public' => 'required|boolean',
        ]);

        try {
            $userId = Auth::id() ?: 1;

            $agenda = new Agenda();
            $agenda->agenda_name = $validated['agenda_name'];
            $agenda->description = $validated['description'] ?? null;
            $agenda->person_in_charge = $validated['person_in_charge'] ?? null;
            $agenda->date = $validated['date'];
            $agenda->start_time = $validated['start_time'] ?? null;
            $agenda->end_time = $validated['end_time'] ?? null;
            $agenda->location = $validated['location'] ?? null;
            $agenda->involved_institution = $validated['involved_institution'] ?? null;
            $agenda->status = 'pending';
            $agenda->is_public = $validated['is_public'];
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
     * ✅ Lihat detail agenda (JSON).
     */
    public function show($id)
    {
        $agenda = Agenda::with(['user', 'approver'])->findOrFail($id);
        return response()->json($agenda);
    }

    /**
     * ✅ Form edit agenda.
     */
    public function edit($id_agenda)
    {
        $agenda = Agenda::findOrFail($id_agenda);
        return view('agenda_edit', compact('agenda'));
    }

    /**
     * ✅ Update agenda (baik dari admin maupun user).
     */
    public function update(Request $request, $id_agenda)
    {
        $agenda = Agenda::findOrFail($id_agenda);

        $validated = $request->validate([
            'agenda_name'        => 'required|string|max:255',
            'description'        => 'nullable|string',
            'tanggal'            => 'required|date',
            'start_time'         => 'nullable|date_format:H:i',
            'end_time'           => 'nullable|date_format:H:i|after_or_equal:start_time',
            'lokasi'             => 'nullable|string|max:255',
            'penanggung_jawab'   => 'nullable|string|max:255',
            'instansi_ikut'      => 'nullable|string|max:255',
            'status'             => 'nullable|string|in:pending,approved,rejected',
            'is_public' => 'required|boolean',
        ]);

        $agenda->agenda_name = $validated['agenda_name'];
        $agenda->description = $validated['description'] ?? null;
        $agenda->date = $validated['tanggal'];
        $agenda->start_time = $validated['start_time'] ?? null;
        $agenda->end_time = $validated['end_time'] ?? null;
        $agenda->location = $validated['lokasi'] ?? null;
        $agenda->person_in_charge = $validated['penanggung_jawab'] ?? null;
        $agenda->involved_institution = $validated['instansi_ikut'] ?? null;
        $agenda->is_public = $validated['is_public'];

        // hanya admin yang boleh ubah status
        if (Auth::user()->role === 'admin') {
            if (isset($validated['status'])) {
                $agenda->status = $validated['status'];
                $agenda->approved_by = ($validated['status'] === 'approved') ? Auth::id() : null;
            }
        }

        $agenda->save();

        return redirect()
            ->route('agenda.edit', $agenda->id_agenda)
            ->with('success', '✅ Agenda berhasil diperbarui.');
    }

    /**
     * ✅ Hapus agenda.
     */
    public function destroy($id_agenda)
    {
        $agenda = Agenda::findOrFail($id_agenda);

        // Validasi: hanya creator & status pending yang bisa hapus
        if ($agenda->status !== 'pending') {
            return redirect()->back()->with('error', 'Agenda tidak dapat dihapus karena sudah di-approve atau ditolak.');
        }

        // Sesuaikan dengan kolom kamu: id_user
        if ($agenda->id_user !== Auth::user()->id_user) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus agenda ini.');
        }

        $agenda->delete();

        return redirect()->back()->with('success', 'Agenda berhasil dihapus.');
    }

    /**
     * ✅ Notifikasi agenda milik user yang login.
     */
    public function notification()
    {
        $userId = Auth::id();

        $agenda = Agenda::where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        if (request()->wantsJson()) {
            return response()->json($agenda);
        }

        return view('notification', compact('agenda'));
    }

}
