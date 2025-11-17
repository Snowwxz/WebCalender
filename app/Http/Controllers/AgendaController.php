<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Unit;
use App\Models\User;
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

        $user = Auth::user(); // ambil user login
        $userId = $user->id_user;

        // 🔹 Base query
        $query = Agenda::with(['user', 'approver', 'unit'])
            ->where('status', 'approved')
            ->orderBy('date', 'desc');

        // 🔹 Kalau bukan superadmin, batasi hanya publik atau milik sendiri atau diundang
        if ($user->role !== 'superadmin') {
            $userUnit = $user->unit;
            $userUnitName = $userUnit ? $userUnit->unit_name : null;

            $query->where(function ($q) use ($userId, $userUnitName) {
                $q->where('is_public', 1)
                    ->orWhere('id_user', $userId);

                // Tambahkan kondisi untuk agenda privat yang mengundang instansi user
                if ($userUnitName) {
                    $q->orWhere(function ($subQ) use ($userUnitName) {
                        $subQ->where('is_public', 0)
                            ->where('involved_institution', 'like', '%' . $userUnitName . '%');
                    });
                }
            });
        }

        $agenda = $query->get();
        $units = Unit::orderBy('unit_name', 'asc')->get();

        // 🔹 Response JSON (misal untuk AJAX)
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
        $user = Auth::user();
        $unitName = null;

        if ($user && $user->id_unit) {
            $unit = Unit::find($user->id_unit);
            $unitName = $unit ? $unit->unit_name : null;
        }

        $units = Unit::all();
        // kirim nama instansi user login
        return view('agenda_create', compact('unitName', 'units'));
    }


    /**
     * ✅ Simpan agenda baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agenda_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'location' => 'nullable|string|max:255',
            'involved_institution' => 'nullable|string|max:500',
            'is_public' => 'required|in:0,1',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $agenda = new Agenda();
            $agenda->agenda_name = $validated['agenda_name'];
            $agenda->description = $validated['description'];
            $agenda->date = $validated['date'];
            $agenda->start_time = $validated['start_time'];
            $agenda->end_time = $validated['end_time'];
            $agenda->location = $validated['location'];
            $agenda->involved_institution = $validated['involved_institution'];
            $agenda->is_public = $validated['is_public'];
            $agenda->id_unit = Auth::user()->id_unit;
            $agenda->id_user = Auth::id();
            $agenda->status = 'pending';
            $agenda->notes = $validated['notes'] ?? null;
            $agenda->save();

            // ⚙ Jika request datang via AJAX / fetch
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'agenda' => $agenda,
                    'message' => 'Agenda berhasil ditambahkan (pending approval).',
                ]);
            }

            // 📄 Kalau request biasa (form HTML) → kembali ke Dashboard Bulan
            return redirect()->route('dashboard.bulan')->with('success', 'Agenda berhasil ditambahkan!');
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
        $units = Unit::orderBy('unit_name', 'asc')->get();

        $user = Auth::user();
        $unitName = null;
        if ($user && $user->id_unit) {
            $unit = Unit::find($user->id_unit);
            $unitName = $unit ? $unit->unit_name : null;
        }

        return view('agenda_edit', compact('agenda', 'units', 'unitName'));
    }

    /**
     * ✅ Update agenda (baik dari admin maupun user).
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'agenda_name' => 'required|string|max:255',
            'description' => 'required|string',
            'id_unit' => 'required|exists:units,id_unit',
            'is_public' => 'required|boolean',
            'location' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'involved_institution' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $agenda = Agenda::findOrFail($id);
        $agenda->update($validated);

        // 🔥 Perbedaan redirect berdasarkan role pengguna
        if (Auth::user()->role === 'admin') {
            return redirect()->route('approve')
                ->with('success', 'Agenda berhasil diperbarui');
        }

        // Untuk user biasa
        return redirect()->route('agenda.notification')
            ->with('success', 'Agenda berhasil diperbarui');
    }


    public function updateStatus(Request $request, $id_agenda)
    {
        $agenda = Agenda::findOrFail($id_agenda);

        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengubah status agenda.'
            ], 403);
        }

        $agenda->status = $request->status;
        $agenda->approved_by = ($request->status === 'approved') ? Auth::id() : null;
        $agenda->save();

        return response()->json([
            'success' => true,
            'message' => 'Status agenda berhasil diperbarui!',
            'agenda' => $agenda
        ]);
    }

    public function reject(Request $request, $id_agenda)
    {
        $agenda = Agenda::findOrFail($id_agenda);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki izin menolak agenda.'], 403);
        }

        $agenda->status = 'rejected';
        $agenda->reason = $request->reason; // simpan alasan admin
        $agenda->approved_by = Auth::id();
        $agenda->save();

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil ditolak.',
            'agenda' => $agenda
        ]);
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
    public function notification(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();

        // Filters
        $status = $request->query('status', 'all');
        $search = $request->query('q');

        // Base query for the list
        $query = Agenda::with(['unit'])
            ->where('id_user', $userId)
            ->orderBy('created_at', 'desc');

        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $like = "%" . $search . "%";
                $q->where('agenda_name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('location', 'like', $like)
                    ->orWhere('involved_institution', 'like', $like)
                    ->orWhereHas('unit', function ($uq) use ($like) {
                        $uq->where('unit_name', 'like', $like);
                    });
            });
        }

        // Use paginator for the view's pagination helpers
        $agenda = $query->paginate(10)->withQueryString();

        // Counts for tabs (ignoring search for overall counts)
        $baseCount = Agenda::where('id_user', $userId);
        $counts = [
            'all' => (clone $baseCount)->count(),
            'pending' => (clone $baseCount)->where('status', 'pending')->count(),
            'approved' => (clone $baseCount)->where('status', 'approved')->count(),
            'rejected' => (clone $baseCount)->where('status', 'rejected')->count(),
        ];

        // Tandai notifikasi user sudah dibuka agar badge hilang
        // Simpan ke database agar tetap tersimpan setelah logout/login
        if ($user && $user instanceof \App\Models\User) {
            $user->last_seen_notification_at = now();
            $user->save();
        }


        if ($request->wantsJson()) {
            return response()->json($agenda);
        }

        return view('notification', compact('agenda', 'counts', 'status', 'search'));
    }

    /**
     * ✅ API untuk mendapatkan notifikasi terbaru (untuk dropdown header)
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id_user;

        // Untuk user biasa: ambil agenda yang statusnya approved/rejected setelah last_seen
        // Untuk admin: ambil agenda pending yang dibuat setelah last_seen
        if ($user->role === 'admin') {
            $lastSeen = $user->last_seen_approve_at;
            $notifications = Agenda::with(['unit', 'user'])
                ->where('status', 'pending')
                ->when($lastSeen, function ($q) use ($lastSeen) {
                    $q->where('created_at', '>', $lastSeen);
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } else {
            $lastSeenU = $user->last_seen_notification_at;
            $notifications = Agenda::with(['unit'])
                ->where('id_user', $userId)
                ->whereIn('status', ['approved', 'rejected'])
                ->when($lastSeenU, function ($q) use ($lastSeenU) {
                    $q->where('updated_at', '>', $lastSeenU);
                }, function ($q) {
                    $q->where('updated_at', '>=', now()->startOfDay());
                })
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Hitung total notifikasi baru
        $count = 0;
        if ($user->role === 'admin') {
            $lastSeen = $user->last_seen_approve_at;
            $count = Agenda::where('status', 'pending')
                ->when($lastSeen, function ($q) use ($lastSeen) {
                    $q->where('created_at', '>', $lastSeen);
                })
                ->count();
        } else {
            $lastSeenU = $user->last_seen_notification_at;
            $count = Agenda::where('id_user', $userId)
                ->whereIn('status', ['approved', 'rejected'])
                ->when($lastSeenU, function ($q) use ($lastSeenU) {
                    $q->where('updated_at', '>', $lastSeenU);
                }, function ($q) {
                    $q->where('updated_at', '>=', now()->startOfDay());
                })
                ->count();
        }

        return response()->json([
            'notifications' => $notifications,
            'count' => $count
        ]);
    }

    /**
     * ✅ Ambil daftar agenda berdasarkan tanggal (untuk klik kalender).
     * Digunakan di dashboard user (AJAX).
     */
    public function getAgendaByDate(Request $request)
    {
        $date = $request->input('date');
        $user = Auth::user();
        $userId = $user->id_user;

        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal tidak diberikan.'
            ], 400);
        }

        $userUnit = $user->unit;
        $userUnitName = $userUnit ? $userUnit->unit_name : null;

        $agenda = Agenda::with(['unit', 'user', 'approver'])
            ->whereDate('date', $date)
            ->where(function ($q) use ($userId, $userUnitName) {
                $q->where('is_public', 1) // publik
                    ->orWhere('id_user', $userId); // private tapi milik sendiri

                // Tambahkan kondisi untuk agenda privat yang mengundang instansi user
                if ($userUnitName) {
                    $q->orWhere(function ($subQ) use ($userUnitName) {
                        $subQ->where('is_public', 0)
                            ->where('involved_institution', 'like', '%' . $userUnitName . '%');
                    });
                }
            })
            ->where('status', 'approved')
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




    public function getAgendaHari(Request $request)
    {
        $date = $request->query('tanggal', date('Y-m-d'));
        $user = Auth::user();
        $userId = $user->id_user;

        $userUnit = $user->unit;
        $userUnitName = $userUnit ? $userUnit->unit_name : null;

        $agenda = Agenda::whereDate('date', $date)
            ->where('status', 'approved')
            ->where(function ($q) use ($userId, $userUnitName) {
                $q->where('is_public', 1)
                    ->orWhere('id_user', $userId);

                // Tambahkan kondisi untuk agenda privat yang mengundang instansi user
                if ($userUnitName) {
                    $q->orWhere(function ($subQ) use ($userUnitName) {
                        $subQ->where('is_public', 0)
                            ->where('involved_institution', 'like', '%' . $userUnitName . '%');
                    });
                }
            })
            ->orderBy('start_time', 'asc')
            ->get(['id_agenda as id', 'agenda_name as title', 'start_time', 'end_time', 'location', 'is_public']);

        // Tambahkan warna otomatis buat bedain publik/privat
        $agenda->transform(function ($item) {
            $item->color = $item->is_public ? '#3a7bd5' : '#f39c12';
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $agenda,
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
