<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    public function index()
    {

        return view('landing');
    }

    // ambil semua agenda dalam satu bulan
    public function getByMonth($year, $month)
    {
        if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
            return response()->json(['error' => 'Parameter tahun atau bulan tidak valid.'], 400);
        }

        $cacheKey = "agenda_month_{$year}_{$month}";
        $agenda = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($year, $month) {
            return Agenda::query()
                ->select('id', 'judul', 'tanggal', 'lokasi')
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->where('status', 'approved')
                ->where('visibility', 'public')
                ->orderBy('tanggal', 'asc')
                ->get();
        });

        return response()->json($agenda);
    }

    // ambil semua agenda di tanggal tertentu (untuk modal show)
    public function getByDate($date)
    {
        if (!strtotime($date)) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $cacheKey = "agenda_date_{$date}";

        $agenda = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($date) {
            return Agenda::query()
                ->select('id', 'judul', 'tanggal', 'lokasi', 'jam_mulai', 'jam_selesai', 'deskripsi')
                ->whereDate('tanggal', $date)
                ->where('status', 'approved')
                ->where('visibility', 'public')
                ->orderBy('jam_mulai', 'asc')
                ->get();
        });

        if ($agenda->isEmpty()) {
            return response()->json(['message' => 'Tidak ada agenda di tanggal ini.']);
        }

        return response()->json($agenda);
    }

    public function getByYear($year)
    {
        $cacheKey = "agenda_year_{$year}";

        $agenda = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($year) {
            return Agenda::select('id', 'judul', 'tanggal', 'lokasi')
                ->whereYear('tanggal', $year)
                ->where('status', 'approved')
                ->where('visibility', 'public')
                ->orderBy('tanggal', 'asc')
                ->get();
        });

        return response()->json($agenda);
    }

    // fitur search
    public function search(Request $request)
    {
        $keyword = trim($request->input('q', ''));

        if (strlen($keyword) < 2) {
            return response()->json(['error' => 'Minimal 2 karakter untuk pencarian.'], 400);
        }

        $agenda = Agenda::query()
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

        return response()->json($agenda);
    }
}
