<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Services\ExternalAgendaService;
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
            // Get local agendas (public only)
            $localAgendas = Agenda::query()
                ->with(['unit'])
                ->selectRaw("id_agenda, agenda_name, DATE(date) as date, location, description, start_time, end_time, is_public, status, id_unit, notes")
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'approved')
                ->where('is_public', 1)
                ->orderBy('date', 'asc')
                ->get()
                ->toArray();

            // Get external agendas
            $externalService = new ExternalAgendaService();
            $externalAgendas = $externalService->getAgendasByMonth($year, $month);

            $mergedAgendas = $localAgendas;

            // Add external agendas if available (diperlakukan sama seperti agenda lokal)
            if (!empty($externalAgendas) && is_array($externalAgendas) && count($externalAgendas) > 0) {
                $transformedExternal = array_map(function ($agenda) use ($externalService) {
                    $transformed = $externalService->transformToLocalFormat($agenda);
                    // Set id_agenda untuk kompatibilitas
                    $transformed['id_agenda'] = 'ext_' . ($agenda['id'] ?? uniqid());
                    // Tidak set is_external atau source - diperlakukan sama seperti agenda lokal
                    // Ensure date is in correct format
                    if (isset($transformed['date'])) {
                        $transformed['date'] = date('Y-m-d', strtotime($transformed['date']));
                    }
                    // Ensure status and is_public are set
                    if (!isset($transformed['status'])) {
                        $transformed['status'] = 'approved';
                    }
                    if (!isset($transformed['is_public'])) {
                        $transformed['is_public'] = 1;
                    }
                    return $transformed;
                }, $externalAgendas);

                $mergedAgendas = array_merge($localAgendas, $transformedExternal);
            }

            // Sort by date and time
            usort($mergedAgendas, function ($a, $b) {
                $dateA = $a['date'] ?? '';
                $dateB = $b['date'] ?? '';
                
                if ($dateA !== $dateB) {
                    return strcmp($dateA, $dateB);
                }
                
                $timeA = $a['start_time'] ?? $a['end_time'] ?? '';
                $timeB = $b['start_time'] ?? $b['end_time'] ?? '';
                
                return strcmp($timeA, $timeB);
            });

            return $mergedAgendas;
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
            // Get local agendas (public only)
            $localAgendas = Agenda::query()
                ->with(['unit'])
                ->select('id_agenda', 'agenda_name', 'date', 'location', 'start_time', 'end_time', 'description', 'is_public', 'status', 'id_unit', 'notes')
                ->whereDate('date', $date)
                ->where('status', 'approved')
                ->where('is_public', 1)
                ->orderBy('start_time', 'asc')
                ->get()
                ->toArray();

            // Get external agendas
            $externalService = new ExternalAgendaService();
            $externalAgendas = $externalService->getAgendasByDate($date);

            $mergedAgendas = $localAgendas;

            // Add external agendas if available (diperlakukan sama seperti agenda lokal)
            if (!empty($externalAgendas) && is_array($externalAgendas) && count($externalAgendas) > 0) {
                $transformedExternal = array_map(function ($agenda) use ($externalService) {
                    $transformed = $externalService->transformToLocalFormat($agenda);
                    // Set id_agenda untuk kompatibilitas
                    $transformed['id_agenda'] = 'ext_' . ($agenda['id'] ?? uniqid());
                    // Tidak set is_external atau source - diperlakukan sama seperti agenda lokal
                    // Ensure date is in correct format
                    if (isset($transformed['date'])) {
                        $transformed['date'] = date('Y-m-d', strtotime($transformed['date']));
                    }
                    // Ensure status and is_public are set
                    if (!isset($transformed['status'])) {
                        $transformed['status'] = 'approved';
                    }
                    if (!isset($transformed['is_public'])) {
                        $transformed['is_public'] = 1;
                    }
                    return $transformed;
                }, $externalAgendas);

                $mergedAgendas = array_merge($localAgendas, $transformedExternal);
            }

            // Sort by time
            usort($mergedAgendas, function ($a, $b) {
                $timeA = $a['start_time'] ?? $a['end_time'] ?? '';
                $timeB = $b['start_time'] ?? $b['end_time'] ?? '';
                return strcmp($timeA, $timeB);
            });

            return $mergedAgendas;
        });

        if (empty($agenda)) {
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
