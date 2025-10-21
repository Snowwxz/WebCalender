<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
    public function index(Request $request)
{
    $status = $request->query('status', 'all');

    // Hitung jumlah agenda per status
    $countAll = Agenda::count();
    $countPending = Agenda::where('status', 'pending')->count();
    $countApproved = Agenda::where('status', 'approved')->count();
    $countRejected = Agenda::where('status', 'rejected')->count();

    // Ambil agenda sesuai filter
    $agendaQuery = Agenda::orderBy('date', 'desc');
    if ($status !== 'all') {
        $agendaQuery->where('status', $status);
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
