<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApproveController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('q');
        $sort = $request->query('sort', 'newest_submitted'); // default

        // Hitung jumlah agenda per status
        $countAll = Agenda::count();
        $countPending = Agenda::where('status', 'pending')->count();
        $countApproved = Agenda::where('status', 'approved')->count();
        $countRejected = Agenda::where('status', 'rejected')->count();

        // Base query
        $agendaQuery = Agenda::with('unit', 'user');

        // Filter status
        if ($status !== 'all') {
            $agendaQuery->where('status', $status);
        }

        // Pencarian
        if (!empty($search)) {
            $agendaQuery->where(function ($q) use ($search) {
                $q->where('agenda_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('person_in_charge', 'like', "%{$search}%")
                    ->orWhere('involved_institution', 'like', "%{$search}%")
                    ->orWhereHas('unit', function ($unitQuery) use ($search) {
                        $unitQuery->where('unit_name', 'like', "%{$search}%");
                    });
            });
        }

        // 🔽 Sorting Options
        switch ($sort) {
            case 'oldest_submitted':
                $agendaQuery->orderBy('created_at', 'asc');
                break;

            case 'newest_submitted':
                $agendaQuery->orderBy('created_at', 'desc');
                break;

            case 'earliest_event':
                $agendaQuery->orderBy('date', 'asc');
                break;

            case 'latest_event':
                $agendaQuery->orderBy('date', 'desc');
                break;

            default:
                $agendaQuery->orderBy('created_at', 'desc');
                break;
        }

        $agendas = $agendaQuery->get();

        // Tandai semua ajuan sudah "dibuka" oleh admin agar badge notifikasi hilang
        // Simpan ke database agar tetap tersimpan setelah logout/login
        $user = Auth::user();
        if ($user && $user->role === 'admin') {
            $user->last_seen_approve_at = now();
            $user->save();
        }

        return view('approve', compact(
            'status',
            'agendas',
            'countAll',
            'countPending',
            'countApproved',
            'countRejected'
        ));
    }
}
