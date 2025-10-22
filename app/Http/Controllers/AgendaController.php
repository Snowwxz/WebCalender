<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    // 🔥 new method: list() — ambil agenda via /date/{year?}-{month?}-{day?}
    public function list($params = null)
    {
        try {
            // Pisahkan parameter jadi [year, month, day]
            $parts = $params ? explode('-', $params) : [];

            $year = $parts[0] ?? null;
            $month = $parts[1] ?? null;
            $day = $parts[2] ?? null;

            // Kalau semua kosong → return data HARI INI
            if (! $year && ! $month && ! $day) {
                $today = now()->toDateString();

                $agenda = Agenda::with(['user', 'approver'])
                    ->whereDate('date', $today)
                    ->orderBy('date', 'desc')
                    ->get();

                return response()->json([
                    'requested_date' => $today,
                    'total' => $agenda->count(),
                    'data' => $agenda,
                    'note' => 'Menampilkan agenda untuk hari ini',
                ]);
            }

            // Kalau cuma tahun kosong tapi gak semua kosong → fallback tahun terakhir
            if (! $year && ($month || $day)) {
                $year = Agenda::selectRaw('YEAR(MAX(date)) as latest_year')->value('latest_year') ?? now()->year;
            }

            // Bentuk tanggal valid
            $dateString = sprintf(
                '%04d-%02d-%02d',
                $year ?? now()->year,
                $month ?? 1,
                $day ?? 1
            );

            $date = Carbon::parse($dateString);

            // Query fleksibel
            $query = Agenda::with(['user', 'approver'])
                ->orderBy('date', 'desc');

            if ($year) {
                $query->whereYear('date', $year);
            }
            if ($month) {
                $query->whereMonth('date', $month);
            }
            if ($day) {
                $query->whereDay('date', $day);
            }

            $agenda = $query->get();

            return response()->json([
                'requested_date' => $date->toDateString(),
                'total' => $agenda->count(),
                'data' => $agenda,
            ]);

        } catch (\Throwable $e) {
            Log::error('Gagal ambil list agenda: '.$e->getMessage());

            return response()->json(['error' => 'Invalid date format or server error'], 400);
        }
    }

    // ini buat munculin form create
    public function create()
    {
        return view('agenda_create', ['agenda' => null]);
    }

    // ini buat simpan agenda
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
            'is_public' => 'required|in:0,1',
            'id_unit' => 'required|exists:units,id_unit',
        ]);

        try {
            $agenda = new Agenda();
            $agenda->agenda_name = $validated['agenda_name'];
            $agenda->description = $validated['description'];
            $agenda->person_in_charge = $validated['person_in_charge'];
            $agenda->date = $validated['date'];
            $agenda->start_time = $validated['start_time'];
            $agenda->end_time = $validated['end_time'];
            $agenda->location = $validated['location'];
            $agenda->involved_institution = $validated['involved_institution'];
            $agenda->is_public = $validated['is_public'];
            $agenda->id_unit = $validated['id_unit'];
            $agenda->id_user = Auth::user()->id_user;
            $agenda->status = 'pending';

            $agenda->save();

            return redirect()->back()->with('success', 'Agenda berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan agenda: ' . $e->getMessage());
        }
    }


    // lihat detail agenda (show)
    public function show($id)
    {
        try {
            $agenda = Agenda::with(['user', 'approver'])->findOrFail(id: $id);
            return response()->json([
                'success' => true,
                'data' => $agenda,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Agenda tidak ditemukan.',
            ], 404);
        }
    }

    /**
     * ✅ Form edit agenda.
     */
    public function edit($id_agenda)
    {
        $agenda = Agenda::findOrFail($id);

        return view('agenda_edit', compact('agenda'));
    }

    // khusus simpan update-an form agenda yo
    public function update(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

        $validated = $request->validate([
            'agenda_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tanggal' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'instansi_ikut' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'waktu_pelaksanaan' => 'nullable|string|max:10',
        ]);

        $agenda->agenda_name = $validated['agenda_name'];
        $agenda->description = $validated['description'] ?? null;
        $agenda->date = $validated['tanggal'];
        $agenda->location = $validated['lokasi'] ?? null;
        $agenda->person_in_charge = $validated['penanggung_jawab'] ?? null;
        $agenda->involved_institution = $validated['instansi_ikut'] ?? null;
        $agenda->time = $validated['waktu_pelaksanaan'] ?? null;
        $agenda->status = $validated['status'] ?? $agenda->status;

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
