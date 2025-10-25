<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * ✅ Halaman utama landing calendar publik (REAL-TIME)
     */
    public function index(Request $request)
    {
        $month = (int) $request->query('month', date('n'));
        $year  = (int) $request->query('year', date('Y'));

        if ($month < 1 || $month > 12) $month = date('n');
        if ($year < 2000 || $year > date('Y') + 2) $year = date('Y');

        // REAL-TIME tanpa cache
        $agenda = Agenda::with('unit:id_unit,unit_name')
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

        return view('landing', compact('month', 'year', 'agenda'));
    }

    /**
     * ✅ API: Ambil agenda publik dalam 1 bulan (REAL-TIME)
     */
    public function getByMonth($year, $month)
    {
        if ($month < 1 || $month > 12) {
            return response()->json(['error' => 'Format bulan tidak valid.'], 400);
        }

        $agenda = Agenda::with('unit:id_unit,unit_name')
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

        return response()->json($agenda);
    }

    /**
     * ✅ API: Ambil agenda per tanggal (REAL-TIME)
     */
    public function getByDate($date)
    {
        if (!strtotime($date)) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $agenda = Agenda::with('unit:id_unit,unit_name')
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

        if ($agenda->isEmpty()) {
            return response()->json(['message' => 'Tidak ada agenda di tanggal ini.']);
        }

        return response()->json($agenda);
    }

    /**
     * ✅ API: Ambil agenda setahun (REAL-TIME)
     */
    public function getByYear($year)
    {
        if ($year < 2000 || $year > date('Y') + 2) {
            return response()->json(['error' => 'Format tahun tidak valid.'], 400);
        }

        $agenda = Agenda::select(
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

        return response()->json($agenda);
    }

    /**
     * ✅ API Search (REAL-TIME)
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
