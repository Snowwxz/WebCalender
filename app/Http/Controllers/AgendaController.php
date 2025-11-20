<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Unit;
use App\Models\User;
use App\Models\AgendaLog;
use App\Services\ExternalAgendaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AgendaController extends Controller
{
    /**
     * ✅ Tampilkan daftar agenda di dashboard (termasuk agenda eksternal).
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

        $localAgendas = $query->get()->toArray();

        // Get external agendas for the current month
        $externalService = new ExternalAgendaService();
        $externalAgendas = $externalService->getAgendasByMonth($year, $month);

        $mergedAgendas = $localAgendas;

        // Add external agendas if available
        if ($externalAgendas) {
            $transformedExternal = array_map(function ($agenda) use ($externalService) {
                $transformed = $externalService->transformToLocalFormat($agenda);
                // Mark as external and add id_agenda for compatibility
                $transformed['id_agenda'] = 'ext_' . ($agenda['id'] ?? uniqid());
                $transformed['is_external'] = true;
                $transformed['source'] = 'external';
                // Ensure date is in correct format (Y-m-d string)
                if (isset($transformed['date'])) {
                    try {
                        // If it's already a string in Y-m-d format, use it
                        if (is_string($transformed['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $transformed['date'])) {
                            // Already in correct format
                        } else {
                            // Try to parse and format
                            $transformed['date'] = Carbon::parse($transformed['date'])->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        // If parsing fails, try strtotime as fallback
                        $parsed = strtotime($transformed['date']);
                        if ($parsed !== false) {
                            $transformed['date'] = date('Y-m-d', $parsed);
                        }
                    }
                }
                // Keep unit from service if available, otherwise set to null
                // Unit is already set in transformToLocalFormat if found
                if (!isset($transformed['unit'])) {
                    $transformed['unit'] = null;
                }
                // Add empty relations for compatibility with local agendas
                $transformed['user'] = null;
                $transformed['approver'] = null;
                // Convert Carbon dates to strings for JSON encoding
                if (isset($transformed['created_at']) && is_object($transformed['created_at'])) {
                    $transformed['created_at'] = $transformed['created_at']->toDateTimeString();
                }
                if (isset($transformed['updated_at']) && is_object($transformed['updated_at'])) {
                    $transformed['updated_at'] = $transformed['updated_at']->toDateTimeString();
                }
                return $transformed;
            }, $externalAgendas);

            $mergedAgendas = array_merge($localAgendas, $transformedExternal);
        }

        // Convert back to collection for view compatibility
        // Ensure all dates are properly formatted
        $mergedAgendas = array_map(function ($item) {
            if (isset($item['date']) && is_object($item['date'])) {
                $item['date'] = $item['date']->format('Y-m-d');
            }
            return $item;
        }, $mergedAgendas);
        
        $agenda = collect($mergedAgendas);
        $units = Unit::orderBy('unit_name', 'asc')->get();

        // 🔹 Response JSON (misal untuk AJAX)
        if ($request->wantsJson() || $request->isJson()) {
            return response()->json($mergedAgendas);
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

        // format agar cocok input HTML
        $agenda->date = \Carbon\Carbon::parse($agenda->date)->format('Y-m-d');
        $agenda->start_time = \Carbon\Carbon::parse($agenda->start_time)->format('H:i');
        $agenda->end_time = \Carbon\Carbon::parse($agenda->end_time)->format('H:i');

        return view('agenda_edit', compact('agenda', 'units', 'unitName'));
    }


    /**
     * ✅ Update agenda (baik dari admin maupun user).
     */
    public function update(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

        // capture old data
        $oldData = $agenda->toArray();

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

        // Reload agenda untuk mendapatkan data terbaru
        $agenda->refresh();

        // *** WRITE LOG ENTRY untuk semua user (admin dan user biasa) ***
        AgendaLog::create([
            'agenda_id' => $agenda->id_agenda,
            'user_id'   => Auth::id(),
            'action'    => 'updated',
            'description' => Auth::user()->role === 'admin' ? 'Agenda updated by admin' : 'Agenda updated by user',
            'old_data' => $oldData,
            'new_data' => $agenda->toArray(),
        ]);

        // 🔥 Perbedaan redirect berdasarkan role pengguna
        if (Auth::user()->role === 'admin') {
            return redirect()->route('approve')
                ->with('success', 'Agenda berhasil diperbarui');
        }

        return redirect()
            ->route('agenda.notification')
            ->with('success', 'Agenda berhasil diperbarui.');
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

        $oldData = $agenda->toArray();

        $agenda->status = $request->status;
        $agenda->approved_by = ($request->status === 'approved') ? Auth::id() : null;
        $agenda->save();

        // log it
        AgendaLog::create([
            'agenda_id' => $agenda->id_agenda,
            'user_id'   => Auth::id(),
            'action'    => 'status_changed',
            'description' => "Status changed to {$request->status}",
            'old_data' => $oldData,
            'new_data' => $agenda->toArray(),
        ]);

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
        $agendaId = $request->query('agenda_id');
        $selectedAgenda = null;

        // Jika ada agenda_id, ambil agenda tersebut (bisa agenda publik atau milik user)
        if ($agendaId) {
            $selectedAgenda = Agenda::with(['unit', 'user'])
                ->where('id_agenda', $agendaId)
                ->where(function ($q) use ($userId) {
                    // Bisa agenda milik user atau agenda publik yang approved
                    $q->where('id_user', $userId)
                        ->orWhere(function ($pubQ) {
                            $pubQ->where('is_public', 1)
                                ->where('status', 'approved');
                        });
                })
                ->first();

            // Jika agenda ditemukan dan bukan milik user, set status ke 'all' untuk menampilkan semua
            if ($selectedAgenda && $selectedAgenda->id_user != $userId) {
                $status = 'all';
            } elseif ($selectedAgenda) {
                // Jika milik user, set status sesuai status agenda
                $status = $selectedAgenda->status;
            }
        }

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

        return view('notification', compact('agenda', 'counts', 'status', 'search', 'agendaId', 'selectedAgenda'));
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
     * ✅ API untuk mendapatkan agenda per bulan (untuk dashboard - menampilkan publik + privasi + eksternal)
     */
    public function getByMonth($year, $month)
    {
        if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
            return response()->json(['error' => 'Parameter tahun atau bulan tidak valid.'], 400);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = $user->id_user;
        $userUnit = $user->unit;
        $userUnitName = $userUnit ? $userUnit->unit_name : null;

        // Get local agendas
        $localAgendas = Agenda::query()
            ->with(['unit'])
            ->selectRaw("id_agenda, agenda_name, DATE(date) as date, location, description, start_time, end_time, involved_institution, is_public, status, id_unit, notes")
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('status', 'approved')
            ->where(function ($q) use ($userId, $userUnitName) {
                // Agenda publik
                $q->where('is_public', 1);
                
                // Agenda privasi milik user
                $q->orWhere(function ($subQ) use ($userId) {
                    $subQ->where('is_public', 0)
                        ->where('id_user', $userId);
                });

                // Agenda privasi yang mengundang instansi user
                if ($userUnitName) {
                    $q->orWhere(function ($subQ) use ($userUnitName) {
                        $subQ->where('is_public', 0)
                            ->where('involved_institution', 'like', '%' . $userUnitName . '%');
                    });
                }
            })
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();

        // Get external agendas
        $externalService = new ExternalAgendaService();
        $externalAgendas = $externalService->getAgendasByMonth($year, $month);

        $mergedAgendas = $localAgendas;

        // Add external agendas if available
        if ($externalAgendas) {
            $transformedExternal = array_map(function ($agenda) use ($externalService) {
                $transformed = $externalService->transformToLocalFormat($agenda);
                // Mark as external and add id_agenda for compatibility
                $transformed['id_agenda'] = 'ext_' . ($agenda['id'] ?? uniqid());
                $transformed['is_external'] = true;
                $transformed['source'] = 'external';
                // Ensure date is in correct format
                if (isset($transformed['date'])) {
                    $transformed['date'] = date('Y-m-d', strtotime($transformed['date']));
                }
                return $transformed;
            }, $externalAgendas);

            $mergedAgendas = array_merge($localAgendas, $transformedExternal);
        }

        // Sort by date and time
        usort($mergedAgendas, function ($a, $b) {
            $dateA = $a['date'] ?? '';
            $dateB = $b['date'] ?? '';
            
            if ($dateA !== $dateB) {
                return strcmp($dateA, $dateB);
            }
            
            $timeA = $a['start_time'] ?? $a['end_time'] ?? '';
            $timeB = $b['start_time'] ?? $b['end_time'] ?? '';
            
            return strcmp($timeA, $timeB);
        });

        return response()->json($mergedAgendas);
    }

    /**
     * ✅ Ambil daftar agenda berdasarkan tanggal (untuk klik kalender).
     * Digunakan di dashboard user (AJAX) - termasuk agenda eksternal.
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

        // Get local agendas
        $localAgendas = Agenda::with(['unit', 'user', 'approver'])
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
            ->get()
            ->toArray();

        // Get external agendas
        $externalService = new ExternalAgendaService();
        $externalAgendas = $externalService->getAgendasByDate($date);

        $mergedAgendas = $localAgendas;

        // Add external agendas if available
        if ($externalAgendas) {
            $transformedExternal = array_map(function ($agenda) use ($externalService) {
                $transformed = $externalService->transformToLocalFormat($agenda);
                // Mark as external and add id_agenda for compatibility
                $transformed['id_agenda'] = 'ext_' . ($agenda['id'] ?? uniqid());
                $transformed['is_external'] = true;
                $transformed['source'] = 'external';
                // Ensure date is in correct format
                if (isset($transformed['date'])) {
                    $transformed['date'] = date('Y-m-d', strtotime($transformed['date']));
                }
                return $transformed;
            }, $externalAgendas);

            $mergedAgendas = array_merge($localAgendas, $transformedExternal);
        }

        // Sort by time
        usort($mergedAgendas, function ($a, $b) {
            $timeA = $a['start_time'] ?? $a['end_time'] ?? '';
            $timeB = $b['start_time'] ?? $b['end_time'] ?? '';
            return strcmp($timeA, $timeB);
        });

        // Kalau kosong, tetap kasih response clean biar frontend gak error
        if (empty($mergedAgendas)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada agenda di tanggal ini.',
                'data' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil diambil.',
            'data' => $mergedAgendas
        ]);
    }




    public function getAgendaHari(Request $request)
    {
        $date = $request->query('tanggal', date('Y-m-d'));
        $user = Auth::user();
        $userId = $user->id_user;

        $userUnit = $user->unit;
        $userUnitName = $userUnit ? $userUnit->unit_name : null;

        // Get local agendas
        $localAgendas = Agenda::whereDate('date', $date)
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
            ->get(['id_agenda as id', 'agenda_name as title', 'start_time', 'end_time', 'location', 'is_public'])
            ->toArray();

        // Get external agendas
        $externalService = new ExternalAgendaService();
        $externalAgendas = $externalService->getAgendasByDate($date);

        $mergedAgendas = $localAgendas;

        // Add external agendas if available
        if ($externalAgendas) {
            $transformedExternal = array_map(function ($agenda) use ($externalService) {
                $transformed = $externalService->transformToLocalFormat($agenda);
                return [
                    'id' => 'ext_' . ($agenda['id'] ?? uniqid()),
                    'title' => $transformed['agenda_name'],
                    'start_time' => $transformed['start_time'],
                    'end_time' => $transformed['end_time'],
                    'location' => $transformed['location'],
                    'is_public' => 1, // External agendas are always public
                    'is_external' => true,
                    'source' => 'external'
                ];
            }, $externalAgendas);

            $mergedAgendas = array_merge($localAgendas, $transformedExternal);
        }

        // Tambahkan warna otomatis buat bedain publik/privat/eksternal
        $mergedAgendas = array_map(function ($item) {
            if (isset($item['is_external']) && $item['is_external']) {
                $item['color'] = '#8e44ad'; // Purple for external
            } else {
                $item['color'] = ($item['is_public'] ?? 1) ? '#3a7bd5' : '#f39c12';
            }
            return $item;
        }, $mergedAgendas);

        return response()->json([
            'success' => true,
            'data' => $mergedAgendas,
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

    public function logs($id_agenda)
    {
        $agenda = Agenda::with('logs.user')->findOrFail($id_agenda);

        return view('agenda.logs', compact('agenda'));
    }

    /**
     * ✅ API untuk mendapatkan detail log agenda (untuk modal)
     */
    public function getAgendaLogs($id_agenda)
    {
        $agenda = Agenda::with(['logs.user' => function ($query) {
            $query->select('id_user', 'name', 'email');
        }])->findOrFail($id_agenda);

        $logs = $agenda->logs()
            ->where('action', 'updated')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'updated_at' => $log->created_at->format('d F Y, H:i'),
                    'updated_at_human' => $log->created_at->locale('id')->diffForHumans(),
                    'user' => $log->user ? [
                        'name' => $log->user->name,
                        'email' => $log->user->email,
                    ] : null,
                    'old_data' => $log->old_data,
                    'new_data' => $log->new_data,
                    'changes' => $this->getChanges($log->old_data, $log->new_data),
                ];
            });

        return response()->json([
            'success' => true,
            'logs' => $logs,
        ]);
    }

    /**
     * ✅ Helper untuk mendapatkan perubahan data
     */
    private function getChanges($oldData, $newData)
    {
        if (!$oldData || !$newData) {
            return [];
        }

        $changes = [];
        $fields = [
            'agenda_name' => 'Nama Agenda',
            'description' => 'Deskripsi',
            'date' => 'Tanggal',
            'start_time' => 'Waktu Mulai',
            'end_time' => 'Waktu Selesai',
            'location' => 'Lokasi',
            'involved_institution' => 'Instansi Terlibat',
            'is_public' => 'Status Publikasi',
            'notes' => 'Catatan',
        ];

        foreach ($fields as $key => $label) {
            $oldValue = $oldData[$key] ?? null;
            $newValue = $newData[$key] ?? null;

            // Handle is_public (0/1 to boolean text)
            if ($key === 'is_public') {
                $oldValue = $oldValue == 1 ? 'Publik' : 'Privasi';
                $newValue = $newValue == 1 ? 'Publik' : 'Privasi';
            }

            // Handle date format
            if ($key === 'date' && $oldValue && $newValue) {
                try {
                    $oldValue = \Carbon\Carbon::parse($oldValue)->locale('id')->translatedFormat('l, d F Y');
                    $newValue = \Carbon\Carbon::parse($newValue)->locale('id')->translatedFormat('l, d F Y');
                } catch (\Exception $e) {
                    // Keep original if parsing fails
                }
            }

            // Handle time format
            if (in_array($key, ['start_time', 'end_time']) && $oldValue && $newValue) {
                try {
                    $oldValue = \Carbon\Carbon::parse($oldValue)->format('H:i');
                    $newValue = \Carbon\Carbon::parse($newValue)->format('H:i');
                } catch (\Exception $e) {
                    // Keep original if parsing fails
                }
            }

            if ($oldValue != $newValue) {
                $changes[] = [
                    'field' => $label,
                    'old_value' => $oldValue ?? '-',
                    'new_value' => $newValue ?? '-',
                ];
            }
        }

        return $changes;
    }

    /**
     * ✅ Fetch agendas from external API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchExternalAgendas(Request $request)
    {
        try {
            $externalService = new ExternalAgendaService();
            
            $year = $request->query('year');
            $month = $request->query('month');
            $date = $request->query('date');

            if ($date) {
                $agendas = $externalService->getAgendasByDate($date);
            } elseif ($year && $month) {
                $agendas = $externalService->getAgendasByMonth($year, $month);
            } else {
                $agendas = $externalService->fetchAgendas();
            }

            if ($agendas === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari API eksternal',
                    'data' => []
                ], 500);
            }

            // Transform to local format
            $transformed = array_map(function ($agenda) use ($externalService) {
                return $externalService->transformToLocalFormat($agenda);
            }, $agendas);

            return response()->json([
                'success' => true,
                'message' => 'Data agenda eksternal berhasil diambil',
                'data' => $transformed,
                'count' => count($transformed)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching external agendas', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * ✅ Get merged agendas (local + external)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMergedAgendas(Request $request)
    {
        try {
            $year = $request->query('year', date('Y'));
            $month = $request->query('month', date('m'));
            $date = $request->query('date');

            $user = Auth::user();
            $userId = $user->id_user;

            // Get local agendas
            $query = Agenda::with(['user', 'approver', 'unit'])
                ->where('status', 'approved')
                ->orderBy('date', 'desc');

            if ($date) {
                $query->whereDate('date', $date);
            } else {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
            }

            // Apply user permissions
            if ($user->role !== 'superadmin') {
                $userUnit = $user->unit;
                $userUnitName = $userUnit ? $userUnit->unit_name : null;

                $query->where(function ($q) use ($userId, $userUnitName) {
                    $q->where('is_public', 1)
                        ->orWhere('id_user', $userId);

                    if ($userUnitName) {
                        $q->orWhere(function ($subQ) use ($userUnitName) {
                            $subQ->where('is_public', 0)
                                ->where('involved_institution', 'like', '%' . $userUnitName . '%');
                        });
                    }
                });
            }

            $localAgendas = $query->get()->toArray();

            // Get external agendas
            $externalService = new ExternalAgendaService();
            $externalAgendas = null;

            if ($date) {
                $externalAgendas = $externalService->getAgendasByDate($date);
            } else {
                $externalAgendas = $externalService->getAgendasByMonth($year, $month);
            }

            $mergedAgendas = $localAgendas;

            // Add external agendas if available
            if ($externalAgendas) {
                $transformedExternal = array_map(function ($agenda) use ($externalService) {
                    $transformed = $externalService->transformToLocalFormat($agenda);
                    // Mark as external
                    $transformed['is_external'] = true;
                    $transformed['source'] = 'external';
                    return $transformed;
                }, $externalAgendas);

                $mergedAgendas = array_merge($localAgendas, $transformedExternal);
            }

            // Sort by date and time
            usort($mergedAgendas, function ($a, $b) {
                $dateA = $a['date'] ?? '';
                $dateB = $b['date'] ?? '';
                
                if ($dateA !== $dateB) {
                    return strcmp($dateA, $dateB);
                }
                
                $timeA = $a['start_time'] ?? $a['end_time'] ?? '';
                $timeB = $b['start_time'] ?? $b['end_time'] ?? '';
                
                return strcmp($timeA, $timeB);
            });

            return response()->json([
                'success' => true,
                'data' => $mergedAgendas,
                'count' => count($mergedAgendas),
                'local_count' => count($localAgendas),
                'external_count' => $externalAgendas ? count($externalAgendas) : 0
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting merged agendas', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * ✅ Get external agendas by date (for dashboard)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExternalAgendasByDate(Request $request)
    {
        $date = $request->input('date');
        
        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal tidak diberikan.'
            ], 400);
        }

        try {
            $externalService = new ExternalAgendaService();
            $agendas = $externalService->getAgendasByDate($date);

            if ($agendas === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari API eksternal',
                    'data' => []
                ], 500);
            }

            // Transform to local format
            $transformed = array_map(function ($agenda) use ($externalService) {
                $item = $externalService->transformToLocalFormat($agenda);
                $item['is_external'] = true;
                $item['source'] = 'external';
                return $item;
            }, $agendas);

            return response()->json([
                'success' => true,
                'message' => 'Agenda eksternal berhasil diambil.',
                'data' => $transformed
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching external agendas by date', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
