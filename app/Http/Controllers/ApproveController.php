<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('q');

        $query = Agenda::with('user')->orderBy('date', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where('agenda_name', 'like', "%$search%");
        }

        $agendas = $query->get();

        return view('approve', compact('agendas', 'status'));
    }
}
