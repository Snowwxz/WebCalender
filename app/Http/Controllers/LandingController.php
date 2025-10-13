<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;

class LandingController extends Controller
{
    public function index()
    {

        return view('landing.index');
    }

    // ambil semua agenda dalam satu bulan
    public function getByMonth($year, $month)
    {
        $agendas = Agenda::select('id', 'judul', 'tanggal', 'lokasi')
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('status', 'approved')
            ->where('visibility', 'public')
            ->get();

        return response()->json($agendas);
    }

    // ambil semua agenda di tanggal tertentu (untuk modal show)
    public function getByDate($date)
    {
        $agendas = Agenda::select('id', 'judul', 'tanggal', 'lokasi', 'jam_mulai', 'jam_selesai', 'deskripsi')
            ->whereDate('tanggal', $date)
            ->where('status', 'approved')
            ->where('visibility', 'public')
            ->get();

        return response()->json($agendas);
    }

    // fitur search
    public function search(Request $request)
    {
        $keyword = $request->input('q');
        $agendas = Agenda::select('id', 'judul', 'tanggal', 'lokasi')
            ->where('status', 'approved')
            ->where('visibility', 'public')
            ->where(function ($query) use ($keyword) {
                $query->where('judul', 'like', "%{$keyword}%")
                      ->orWhere('lokasi', 'like', "%{$keyword}%")
                      ->orWhereDate('tanggal', $keyword);
            })
            ->get();

        return response()->json($agendas);
    }
}
