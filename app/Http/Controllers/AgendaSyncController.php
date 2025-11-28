<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Agenda;

class AgendaSyncController extends Controller
{
    public function sync()
    {
        $response = Http::get('https://backend.samagov.id/api/ppid/agenda');

        if ($response->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reach external API.'
            ], 500);
        }

        $items = $response->json()['data'] ?? [];

        foreach ($items as $item) {

            Agenda::updateOrCreate(
                [
                    'external_id' => $item['id'],   // mapping ID API
                ],
                [
                    // kamu mapping-kan field mereka → field yang kamu punya
                    'agenda_name'           => $item['judul'],
                    'description'           => $item['perihal'] ?? null,
                    'date'                  => $item['waktu_awal'],
                    'start_time'            => $item['waktu_awal'],
                    'end_time'              => $item['waktu_akhir'],
                    'location'              => $item['tempat'],
                    'involved_institution'  => $item['dihadiri_oleh'],
                    'is_public'             => 1, // API tidak punya ini, jadi default public
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Agenda successfully synced.'
        ]);
    }
}
