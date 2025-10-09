<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    public function index()
    {

        return view('landing.index');
    }

    // ambil semua agenda dalam satu bulan
    public function getByMonth($year, $month)
    {
        $cacheKey = "agenda_month_{$year}_{$month}";
        $agendas = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($year, $month) {
            return Agenda::query()
                ->select('id', 'judul', 'tanggal', 'lokasi')
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->where('status', 'approved')
                ->where('visibility', 'public')
                ->orderBy('tanggal', 'asc')
                ->get();
        });

        return response()->json($agendas);
    }

    // ambil semua agenda di tanggal tertentu (untuk modal show)
    public function getByDate($date)
    {
        // validasi format tanggal biar gak error
        if (!strtotime($date)) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $agendas = Agenda::query()
            ->select('id', 'judul', 'tanggal', 'lokasi', 'jam_mulai', 'jam_selesai', 'deskripsi')
            ->whereDate('tanggal', $date)
            ->where('status', 'approved')
            ->where('visibility', 'public')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        if ($agendas->isEmpty()) {
            return response()->json(['message' => 'Tidak ada agenda di tanggal ini.']);
        }

        return response()->json($agendas);
    }

    public function getByYear($year)
    {
        $agendas = Agenda::select('id', 'judul', 'tanggal', 'lokasi')
            ->whereYear('tanggal', $year)
            ->where('status', 'approved')
            ->where('visibility', 'public')
            ->orderBy('tanggal', 'asc')
            ->get();

        return response()->json($agendas);
    }


    // fitur search
    public function search(Request $request)
    {
        $keyword = trim($request->input('q', ''));

        if (strlen($keyword) < 2) {
            return response()->json(['error' => 'Minimal 2 karakter untuk pencarian.'], 400);
        }

        $agendas = Agenda::query()
            ->select('id', 'judul', 'tanggal', 'lokasi')
            ->where('status', 'approved')
            ->where('visibility', 'public')
            ->where(function ($query) use ($keyword) {
                $query->where('judul', 'like', "%{$keyword}%")
                    ->orWhere('lokasi', 'like', "%{$keyword}%")
                    ->orWhereRaw("DATE_FORMAT(tanggal, '%Y-%m-%d') LIKE ?", ["%{$keyword}%"]);
            })
            ->orderBy('tanggal', 'asc')
            ->limit(50) // batasi biar gak berat
            ->get();

        return response()->json($agendas);
    }
}
