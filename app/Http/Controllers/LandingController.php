<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year', date('Y'));
        return view('landing', ['month' => $month, 'year' => (int)$year]);
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
                ->with(['unit'])
                ->selectRaw("id_agenda, agenda_name, DATE(date) as date, location, description, start_time, end_time, involved_institution, is_public, status, id_unit")
                ->whereMonth('date', $month)
                ->where('status', 'approved')
                ->where('is_public', 1)
                ->orderBy('date', 'asc')
                ->get();
        });

        return response()->json($agenda);
    }


    // ambil semua agenda di tanggal tertentu (untuk modal show)
    public function getByDate($date)
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $cacheKey = "agenda_date_{$date}";

        $agenda = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($date) {
            return Agenda::query()
                ->with(['unit'])
                ->select('id_agenda', 'agenda_name', 'date', 'location', 'start_time', 'end_time', 'description', 'involved_institution', 'is_public', 'status', 'id_unit')
                ->whereDate('date', $date)
                ->where('status', 'approved')
                ->where('is_public', 1)
                ->orderBy('start_time', 'asc')
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
            return Agenda::select('id_agenda', 'agenda_name', 'date', 'location')
                ->whereYear('date', $year)
                ->where('status', 'approved')
                ->where('is_public', 1)
                ->orderBy('date', 'asc')
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
            ->select('id_agenda', 'agenda_name', 'date', 'location')
            ->where('status', 'approved')
            ->where('is_public', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('agenda_name', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%")
                    ->orWhereRaw("DATE_FORMAT(date, '%Y-%m-%d') LIKE ?", ["%{$keyword}%"]);
            })
            ->orderBy('date', 'asc')
            ->limit(50) // batasi biar gak berat
            ->get();

        return response()->json($agenda);
    }
}
