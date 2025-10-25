<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    /**
     * ✅ Halaman utama landing calendar publik
     */
    public function index(Request $request)
    {
        // Ambil bulan & tahun dari query string (default: bulan & tahun sekarang)
        $month = (int) $request->query('month', date('n'));
        $year  = (int) $request->query('year', date('Y'));

        // Validasi input
        if ($month < 1 || $month > 12) $month = date('n');
        if ($year < 2000 || $year > date('Y') + 2) $year = date('Y');

        // Key cache unik per bulan-tahun
        $cacheKey = "landing_agenda_{$year}_{$month}";

        // Ambil data dari cache kalau ada (biar ringan)
        $agenda = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($year, $month) {
            return Agenda::with('unit:id_unit,unit_name')
                ->select(
                    'id_agenda',
                    'agenda_name',
                    'date',
                    'location',
                    'start_time',
                    'end_time',
                    'person_in_charge',
                    'involved_institution',
                    'description',
                    'status',
                    'is_public',
                    'id_unit'
                )
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'approved')
                ->where('is_public', 1)
                ->orderBy('date', 'asc')
                ->get();
        });

        // Kirim ke Blade
        return view('landing', compact('month', 'year', 'agenda'));
    }

    /**
     * ✅ API: Ambil semua agenda publik dalam satu bulan (AJAX)
     * Endpoint: /api/agenda/{year}/{month}
     */
    public function getByMonth($year, $month)
    {
        if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
            return response()->json(['error' => 'Format tahun/bulan tidak valid.'], 400);
        }

        $cacheKey = "api_agenda_{$year}_{$month}";

        $agenda = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($year, $month) {
            return Agenda::with('unit:id_unit,unit_name')
                ->select(
                    'id_agenda',
                    'agenda_name',
                    'date',
                    'location',
                    'start_time',
                    'end_time',
                    'person_in_charge',
                    'involved_institution',
                    'description',
                    'status',
                    'is_public',
                    'id_unit'
                )
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'approved')
                ->where('is_public', 1)
                ->orderBy('date', 'asc')
                ->get();
        });

        return response()->json($agenda);
    }

    /**
     * ✅ API: Ambil semua agenda publik di tanggal tertentu
     */
    public function getByDate($date)
    {
        if (!strtotime($date)) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $cacheKey = "agenda_date_{$date}";

        $agenda = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($date) {
            return Agenda::with('unit:id_unit,unit_name')
                ->select(
                    'id_agenda',
                    'agenda_name',
                    'date',
                    'location',
                    'start_time',
                    'end_time',
                    'person_in_charge',
                    'involved_institution',
                    'description'
                )
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

    /**
     * ✅ API: Ambil semua agenda publik dalam satu tahun
     */
    public function getByYear($year)
    {
        if (!is_numeric($year) || $year < 2000 || $year > date('Y') + 2) {
            return response()->json(['error' => 'Format tahun tidak valid.'], 400);
        }

        $cacheKey = "agenda_year_{$year}";

        $agenda = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($year) {
            return Agenda::select(
                    'id_agenda',
                    'agenda_name',
                    'date',
                    'location'
                )
                ->whereYear('date', $year)
                ->where('status', 'approved')
                ->where('is_public', 1)
                ->orderBy('date', 'asc')
                ->get();
        });

        return response()->json($agenda);
    }

    /**
     * ✅ API: Pencarian agenda publik (search bar di landing)
     */
    public function search(Request $request)
    {
        $keyword = trim($request->input('q', ''));

        if (strlen($keyword) < 2) {
            return response()->json(['error' => 'Minimal 2 karakter untuk pencarian.'], 400);
        }

        $agenda = Agenda::with('unit:id_unit,unit_name')
            ->select(
                'id_agenda',
                'agenda_name',
                'date',
                'location',
                'start_time',
                'end_time',
                'description'
            )
            ->where('status', 'approved')
            ->where('is_public', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('agenda_name', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%")
                    ->orWhereRaw("DATE_FORMAT(date, '%Y-%m-%d') LIKE ?", ["%{$keyword}%"]);
            })
            ->orderBy('date', 'asc')
            ->limit(50)
            ->get();

        return response()->json($agenda);
    }
}
