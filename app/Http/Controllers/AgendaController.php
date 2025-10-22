<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
<<<<<<< HEAD
use Carbon\Carbon;
=======
use App\Models\Unit;
>>>>>>> fdc4bcef316fd3bbb6c5143aec79b55e5a009ca4
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
            // Jika user ingin melihat agenda mereka sendiri, tampilkan semua status
            $query->where('id_user', Auth::user()->id_user);
        } else {
            // Jika melihat semua agenda, hanya tampilkan yang sudah di-approve
            $query->where('status', 'approved');
        }

        $agenda = $query->get();

        if ($request->wantsJson() || $request->isJson()) {
            return response()->json($agenda);
        }

        return view('dashboard_bulan', compact('agenda', 'year', 'month'));
    }

<<<<<<< HEAD
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
=======
    /**
     * ✅ Form pengajuan agenda.
     */
>>>>>>> fdc4bcef316fd3bbb6c5143aec79b55e5a009ca4
    public function create()
    {
        $units = Unit::orderBy('unit_name', 'asc')->get();

        return view('agenda_create', compact('units'));
    }

    /**
     * ✅ Simpan agenda baru.
     */
    public function store(Request $request)
    {
<<<<<<< HEAD
        // Validasi input
        $validated = $request->validate([
            'agenda_name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'person_in_charge' => 'required|string|max:255',
            'involved_institution' => 'required|string|max:255',
        ]);

        try {
            $userId = Auth::id() ?: 1;

            $agenda = new Agenda;
            $agenda->agenda_name = $validated['agenda_name'];
            $agenda->description = $validated['description'] ?? null;
            $agenda->submitted_by = $validated['submitted_by'] ?? null;
            $agenda->start_time = $validated['start_time'];
            $agenda->end_time = $validated['end_time'];
            $agenda->date = $validated['date'];
            $agenda->location = $validated['location'] ?? null;
            $agenda->person_in_charge = $validated['person_in_charge'] ?? null;
            $agenda->involved_institution = $validated['involved_institution'] ?? null;
            $agenda->status = 'pending';
            $agenda->id_user = $userId;
            $agenda->approved_by = 0;
            $agenda->save();

            return redirect()
                ->back()
                ->with('success', '✅ Agenda berhasil ditambahkan ke database (status pending).');

        } catch (\Throwable $e) {
            Log::error('❌ Gagal menyimpan agenda: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan agenda ke database: '.$e->getMessage());
        }
    }

    // lihat detail agenda (show)
    public function show($id)
    {
        $agenda = Agenda::with(['user', 'approver'])->findOrFail($id);

        return response()->json($agenda);
    }

    // tampilkan form pengeditan
    public function edit($id)
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
=======
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


    /**
     * ✅ Lihat detail agenda (JSON).
     */
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
>>>>>>> fdc4bcef316fd3bbb6c5143aec79b55e5a009ca4
        ]);

        $agenda->agenda_name = $validated['agenda_name'];
        $agenda->description = $validated['description'] ?? null;
        $agenda->date = $validated['tanggal'];
        $agenda->start_time = $validated['start_time'] ?? null;
        $agenda->end_time = $validated['end_time'] ?? null;
        $agenda->location = $validated['lokasi'] ?? null;
        $agenda->person_in_charge = $validated['penanggung_jawab'] ?? null;
        $agenda->involved_institution = $validated['instansi_ikut'] ?? null;
<<<<<<< HEAD
        $agenda->time = $validated['waktu_pelaksanaan'] ?? null;
        $agenda->status = $validated['status'] ?? $agenda->status;

        if (($validated['status'] ?? '') === 'approved' && Auth::check()) {
            $agenda->approved_by = Auth::id();
=======
        $agenda->is_public = $validated['is_public'];

        // hanya admin yang boleh ubah status
        if (Auth::user()->role === 'admin') {
            if (isset($validated['status'])) {
                $agenda->status = $validated['status'];
                $agenda->approved_by = ($validated['status'] === 'approved') ? Auth::id() : null;
            }
>>>>>>> fdc4bcef316fd3bbb6c5143aec79b55e5a009ca4
        }

        $agenda->save();

        return redirect()
            ->route('agenda.edit', $agenda->id_agenda)
            ->with('success', '✅ Agenda berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id_agenda)
    {
        $agenda = Agenda::findOrFail($id_agenda);

        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        if (Auth::user()->role === 'admin') {
            $agenda->status = $request->status;
            $agenda->approved_by = ($request->status === 'approved') ? Auth::id() : null;
            $agenda->save();

            return redirect()->back()->with('success', 'Status agenda berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah status agenda.');
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
