<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ExternalAgendaService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.external_agenda.url', 'https://backend.samagov.id/api/ppid/agenda');
    }

    /**
     * Fetch agenda data from external API with caching
     * 
     * @param array $params Optional query parameters
     * @param int $cacheMinutes Cache duration in minutes (default: 5)
     * @return array|null
     */
    public function fetchAgendas(array $params = [], int $cacheMinutes = 5)
    {
        // Create cache key based on params
        $cacheKey = 'external_agendas_' . md5(json_encode($params));
        
        return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($params) {
            try {
                $response = Http::timeout(10)->get($this->apiUrl, $params);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                        return $data['data'];
                    }
                }

                Log::warning('External API response failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;
            } catch (\Exception $e) {
                Log::error('Error fetching external agenda API', [
                    'message' => $e->getMessage(),
                    'url' => $this->apiUrl
                ]);

                return null;
            }
        });
    }

    /**
     * Extract OPD/Unit name from dihadiri_oleh field
     * 
     * @param string $dihadiriOleh
     * @return string|null
     */
    protected function extractUnitName($dihadiriOleh)
    {
        if (empty($dihadiriOleh)) {
            return null;
        }

        // Split by comma, newline, or slash to get list of institutions
        $institutions = preg_split('/[,\n\r\/]+/', $dihadiriOleh);
        
        // Common OPD abbreviations
        $opdAbbreviations = [
            'DISDIKBUD',
            'DISPORAPAR',
            'BAPPERIDA',
            'BPKAD',
            'DISPERIKANAN',
            'DISKOMINFO',
            'DLH',
            'DISPUPR',
            'DISPERHUBUNGAN',
            'DINKES',
            'DISPERKIM',
            'BKPSDM',
            'INSPEKTORAT',
            'BPBD',
            'DISKETAPANGTANI',
        ];
        
        // Pattern for full OPD names
        $opdPatterns = [
            '/DINAS\s+[A-Z\s]+/i',
            '/BADAN\s+[A-Z\s]+/i',
            '/BAGIAN\s+[A-Z\s]+/i',
            '/BIDANG\s+[A-Z\s]+/i',
            '/KANTOR\s+[A-Z\s]+/i',
            '/SEKRETARIAT\s+[A-Z\s]+/i',
        ];

        // Try to find matching OPD
        foreach ($institutions as $institution) {
            $institution = trim($institution);
            if (empty($institution)) {
                continue;
            }

            // Check for exact abbreviation match
            $upperInstitution = strtoupper($institution);
            foreach ($opdAbbreviations as $abbr) {
                if (strpos($upperInstitution, $abbr) !== false) {
                    // Return the abbreviation
                    return $abbr;
                }
            }

            // Check for pattern matches (full names)
            foreach ($opdPatterns as $pattern) {
                if (preg_match($pattern, $institution, $matches)) {
                    return trim($matches[0]);
                }
            }
        }

        // If no pattern match, return first non-empty institution that looks like an OPD
        foreach ($institutions as $institution) {
            $institution = trim($institution);
            if (!empty($institution) && strlen($institution) > 3) {
                // Check if it contains common OPD keywords
                if (preg_match('/(DINAS|DIS|BADAN|BAGIAN|BIDANG|KANTOR|SEKRETARIAT|INSPEKTORAT|BPBD|BKPSDM|KA\s+)/i', $institution)) {
                    // Extract OPD name (remove prefixes like "KA", "KADIS", etc.)
                    $cleaned = preg_replace('/^(KA|KADIS|KEPALA|PLT\.?\s*KA|PLT\s*KADIS)\s+/i', '', $institution);
                    if (!empty($cleaned)) {
                        return trim($cleaned);
                    }
                }
                return $institution;
            }
        }

        return null;
    }

    /**
     * Find or create unit based on unit name
     * 
     * @param string|null $unitName
     * @return Unit|null
     */
    protected function findOrCreateUnit($unitName)
    {
        if (empty($unitName)) {
            return null;
        }

        // Try to find exact match first
        $unit = Unit::where('unit_name', $unitName)->first();
        
        if ($unit) {
            return $unit;
        }

        // Try case insensitive exact match
        $unit = Unit::whereRaw('UPPER(unit_name) = ?', [strtoupper($unitName)])->first();
        
        if ($unit) {
            return $unit;
        }

        // Try to find similar unit (case insensitive, partial match)
        $unit = Unit::whereRaw('LOWER(unit_name) LIKE ?', ['%' . strtolower($unitName) . '%'])->first();
        
        if ($unit) {
            return $unit;
        }

        // Try reverse - check if unit_name contains the search term
        $unit = Unit::whereRaw('LOWER(?) LIKE CONCAT("%", LOWER(unit_name), "%")', [$unitName])->first();
        
        if ($unit) {
            return $unit;
        }

        // If not found, you can optionally create new unit
        // Uncomment below if you want to auto-create units from external API
        /*
        try {
            $unit = Unit::create([
                'unit_name' => $unitName,
                'address' => null,
            ]);
            Log::info('Auto-created unit from external API', ['unit_name' => $unitName]);
            return $unit;
        } catch (\Exception $e) {
            Log::warning('Failed to create unit from external API', [
                'unit_name' => $unitName,
                'error' => $e->getMessage()
            ]);
            return null;
        }
        */

        // Log when unit is not found for debugging
        Log::debug('Unit not found for external agenda', [
            'searched_unit_name' => $unitName
        ]);

        return null;
    }

    /**
     * Transform external API data to match local database structure
     * 
     * @param array $externalAgenda
     * @return array
     */
    public function transformToLocalFormat(array $externalAgenda)
    {
        // Parse waktu_awal (date format: Y-m-d) and waktu_akhir (time format: H:i)
        $dateRaw = isset($externalAgenda['waktu_awal']) ? $externalAgenda['waktu_awal'] : null;
        
        // Normalize date to Y-m-d format
        $date = null;
        if ($dateRaw) {
            try {
                $date = Carbon::parse($dateRaw)->format('Y-m-d');
            } catch (\Exception $e) {
                // If parsing fails, try to use as is if it's already in Y-m-d format
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateRaw)) {
                    $date = $dateRaw;
                }
            }
        }
        
        $endTime = isset($externalAgenda['waktu_akhir']) ? $externalAgenda['waktu_akhir'] : null;
        
        // waktu_akhir is the end time, we'll use it as both start and end if no start time
        // In the API, waktu_akhir seems to be the time when the event happens
        $startTime = null;
        if ($endTime && preg_match('/^\d{2}:\d{2}$/', $endTime)) {
            // If only end time is provided, we can use it as reference
            // You might want to adjust this based on your business logic
            $startTime = $endTime; // For now, use the same time
        }

        // Extract unit/OPD from dihadiri_oleh
        $dihadiriOleh = $externalAgenda['dihadiri_oleh'] ?? '';
        $unitName = $this->extractUnitName($dihadiriOleh);
        $unit = $this->findOrCreateUnit($unitName);

        return [
            'external_id' => $externalAgenda['id'] ?? null,
            'external_uuid' => $externalAgenda['uuid'] ?? null,
            'agenda_name' => $externalAgenda['judul'] ?? '',
            'description' => $externalAgenda['perihal'] ?? '',
            'date' => $date, // Ensure date is in Y-m-d format
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => $externalAgenda['tempat'] ?? '',
            'involved_institution' => $dihadiriOleh,
            'status' => 'approved', // External agendas are considered approved
            'is_public' => 1, // External agendas are public
            'notes' => null,
            'external_data' => json_encode($externalAgenda), // Store full external data
            'created_at' => isset($externalAgenda['created_at']) ? Carbon::parse($externalAgenda['created_at']) : now(),
            'updated_at' => isset($externalAgenda['updated_at']) ? Carbon::parse($externalAgenda['updated_at']) : now(),
            // Add unit information
            'id_unit' => $unit ? $unit->id_unit : null,
            'unit' => $unit ? [
                'id_unit' => $unit->id_unit,
                'unit_name' => $unit->unit_name,
            ] : null,
        ];
    }

    /**
     * Get agendas filtered by date range
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array|null
     */
    public function getAgendasByDateRange($startDate = null, $endDate = null)
    {
        $agendas = $this->fetchAgendas();
        
        if (!$agendas) {
            return null;
        }

        // Filter by date if provided
        if ($startDate || $endDate) {
            $agendas = array_filter($agendas, function ($agenda) use ($startDate, $endDate) {
                $agendaDate = $agenda['waktu_awal'] ?? null;
                
                if (!$agendaDate) {
                    return false;
                }

                if ($startDate && $agendaDate < $startDate) {
                    return false;
                }

                if ($endDate && $agendaDate > $endDate) {
                    return false;
                }

                return true;
            });
        }

        return array_values($agendas);
    }

    /**
     * Get agendas for a specific month
     * 
     * @param int $year
     * @param int $month
     * @return array|null
     */
    public function getAgendasByMonth($year, $month)
    {
        $agendas = $this->fetchAgendas();
        
        if (!$agendas) {
            return null;
        }

        // Filter by month - ensure proper date parsing
        $filtered = array_filter($agendas, function ($agenda) use ($year, $month) {
            $agendaDate = $agenda['waktu_awal'] ?? null;
            
            if (!$agendaDate) {
                return false;
            }

            try {
                // Parse date and normalize to Y-m-d format first
                $date = Carbon::parse($agendaDate);
                // Compare year and month
                return $date->year == $year && $date->month == $month;
            } catch (\Exception $e) {
                Log::warning('Failed to parse agenda date', [
                    'date' => $agendaDate,
                    'agenda_id' => $agenda['id'] ?? null
                ]);
                return false;
            }
        });

        return array_values($filtered);
    }

    /**
     * Get agendas for a specific date with caching
     * 
     * @param string $date Format: Y-m-d
     * @return array|null
     */
    public function getAgendasByDate($date)
    {
        // Cache filtered results per date for faster access
        $cacheKey = 'external_agendas_date_' . $date;
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($date) {
            $agendas = $this->fetchAgendas([], 10); // Use longer cache for base data
            
            if (!$agendas) {
                return null;
            }

            // Filter by exact date - ensure proper date parsing
            $filtered = array_filter($agendas, function ($agenda) use ($date) {
                $agendaDate = $agenda['waktu_awal'] ?? null;
                
                if (!$agendaDate) {
                    return false;
                }

                try {
                    // Parse and normalize to Y-m-d format
                    $agendaDateParsed = Carbon::parse($agendaDate)->format('Y-m-d');
                    return $agendaDateParsed === $date;
                } catch (\Exception $e) {
                    Log::warning('Failed to parse agenda date for date filter', [
                        'date' => $agendaDate,
                        'target_date' => $date,
                        'agenda_id' => $agenda['id'] ?? null
                    ]);
                    return false;
                }
            });

            return array_values($filtered);
        });
    }
}

