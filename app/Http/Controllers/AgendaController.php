<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AgendaController extends Controller
{
    /**
     * ✅ Tampilkan daftar agenda di dashboard.
     */
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('m'));

        $query = Agenda::with(['user', 'approver', 'unit'])
            ->orderBy('date', 'desc');

        if ($request->query('only_my')) {
            // Jika user ingin melihat agenda mereka sendiri, tampilkan semua status
            $query->where('id_user', Auth::user()->id_user);
        } else {
            // Untuk user biasa, tampilkan agenda mereka sendiri (semua status) + agenda approved dari user lain
            $query->where(function ($q) {
                $q->where('id_user', Auth::user()->id_user) // Agenda user sendiri (semua status)
                    ->orWhere('status', 'approved'); // Agenda approved dari user lain
            });
        }

        $agenda = $query->get();
        $units = Unit::orderBy('unit_name', 'asc')->get();

        if ($request->wantsJson() || $request->isJson()) {
            return response()->json($agenda);
        }

        return view('dashboard_bulan', compact('agenda', 'year', 'month', 'units'));
    }

    /**
     * ✅ Form pengajuan agenda.
     */
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

            // ⚙ Jika request datang via AJAX / fetch
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'agenda' => $agenda,
                    'message' => 'Agenda berhasil ditambahkan (pending approval).',
                ]);
            }

            // 📄 Kalau request biasa (form HTML)
            return redirect()->back()->with('success', 'Agenda berhasil ditambahkan!');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan agenda: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menyimpan agenda: ' . $e->getMessage());
        }
    }



    /**
     * ✅ Lihat detail agenda (JSON).
     */
    public function show($id)
    {
        $agenda = Agenda::with(['user', 'approver', 'unit'])->findOrFail($id);

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

    /**
     * ✅ Ambil daftar agenda berdasarkan tanggal (untuk klik kalender).
     * Digunakan di dashboard user (AJAX).
     */
    public function getAgendaByDate(Request $request)
    {
        $date = $request->input('date');

        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal tidak diberikan.'
            ], 400);
        }

        $agenda = Agenda::with(['unit', 'user', 'approver'])
            ->whereDate('date', $date)
            ->where(function ($q) {
                $q->where('id_user', Auth::user()->id_user)
                    ->orWhere('status', 'approved'); // hanya tampilkan approved dari user lain
            })
            ->orderBy('start_time', 'asc')
            ->get();

        // Kalau kosong, tetap kasih response clean biar frontend gak error
        if ($agenda->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada agenda di tanggal ini.',
                'data' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil diambil.',
            'data' => $agenda
        ]);
    }

    public function list($params = null)
    {
        try {
            $parts = $params ? explode('-', $params) : [];

            // 1. Validasi dan Konversi Parameter ke Integer
            // Pastikan nilai yang diambil ada dan numerik
            $year = (isset($parts[0]) && is_numeric($parts[0])) ? (int) $parts[0] : null;
            $month = (isset($parts[1]) && is_numeric($parts[1])) ? (int) $parts[1] : null;
            $day = (isset($parts[2]) && is_numeric($parts[2])) ? (int) $parts[2] : null;

            // Kasus 1: Tidak ada parameter yang diberikan (URL: /api/agenda/list/)
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

            // Kasus 2: Ada parameter, tentukan nilai fallback
            $currentYear = now()->year;

            // Jika tahun kosong, gunakan tahun terakhir dari data atau tahun saat ini.
            if (! $year) {
                $year = Agenda::selectRaw('YEAR(MAX(date)) as latest_year')->value('latest_year') ?? $currentYear;
            }

            // Terapkan nilai fallback untuk bulan dan hari jika kosong
            $targetYear = $year;
            $targetMonth = $month ?? 1;
            $targetDay = $day ?? 1;

            // 2. Validasi Tanggal Kalender (Penting untuk mencegah "Invalid date format")
            if (!checkdate($targetMonth, $targetDay, $targetYear)) {
                $errorMessage = "Invalid date components provided: Year=$targetYear, Month=$targetMonth, Day=$targetDay. The date does not exist (e.g., Feb 30th).";
                Log::warning('Agenda List Error: ' . $errorMessage);

                return response()->json(['error' => 'Invalid date format: Date components are out of calendar range.'], 400);
            }

            // Bentuk tanggal valid untuk respons
            $dateString = sprintf(
                '%04d-%02d-%02d',
                $targetYear,
                $targetMonth,
                $targetDay
            );
            $date = Carbon::parse($dateString);

            // 3. Query Fleksibel
            $query = Agenda::with(['user', 'approver'])
                ->orderBy('date', 'desc');

            // Selalu filter berdasarkan tahun (karena sudah ada fallback-nya)
            $query->whereYear('date', $targetYear);

            // Filter hanya berdasarkan bulan dan hari jika parameter tersebut diberikan
            if ($month) {
                $query->whereMonth('date', $targetMonth);
            }
            if ($day) {
                $query->whereDay('date', $targetDay);
            }

            $agenda = $query->get();

            return response()->json([
                'requested_date' => $date->toDateString(),
                'total' => $agenda->count(),
                'data' => $agenda,
            ]);

        } catch (\Throwable $e) {
            // Blok ini akan menangkap semua error server (masalah DB, Model, Relationship)

            // Log pesan error penuh (penting untuk debugging)
            Log::error('Gagal ambil list agenda: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            // MENGEMBALIKAN ERROR EKSPLISIT:
            return response()->json([
                'error' => 'Server Error Occurred.',
                'exception_message' => $e->getMessage(), // <-- PESAN ERROR ASLI DARI LARAVEL/PHP
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                // Di lingkungan dev, Anda juga bisa menyertakan trace:
                // 'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
