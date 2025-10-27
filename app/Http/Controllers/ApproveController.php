<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('q'); // 🔍 ambil kata kunci pencarian

        // Hitung jumlah agenda per status
        $countAll = Agenda::count();
        $countPending = Agenda::where('status', 'pending')->count();
        $countApproved = Agenda::where('status', 'approved')->count();
        $countRejected = Agenda::where('status', 'rejected')->count();

        // Ambil agenda sesuai filter
        $agendaQuery = Agenda::with('unit', 'user') // relasi untuk akses nama instansi & user
            ->orderBy('date', 'desc');

        // Filter status
        if ($status !== 'all') {
            $agendaQuery->where('status', $status);
        }

        // 🔍 Filter pencarian
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

        $agendas = $agendaQuery->get();

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
