<?php

namespace App\Http\Controllers;

use App\Models\TravelDocument;
use App\Models\Location;
use App\Models\TrackingSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    /**
     * Menampilkan halaman tracking dengan daftar pengiriman aktif
     */
    public function index(Request $request)
    {
        // Hitung jumlah
        $totalActive = TravelDocument::whereRaw("LOWER(status) LIKE '%sedang%'")->count();
        $totalDelivered = TravelDocument::where('status', 'Terkirim')->count();

        // Ambil data (batasi 5 masing-masing)
        $activeShippings = TravelDocument::whereRaw("LOWER(status) LIKE '%sedang%'")
            ->select('id', 'no_travel_document', 'send_to', 'project', 'posting_date')
            ->orderBy('posting_date', 'desc')
            ->limit(5)
            ->get();

        $deliveredShippings = TravelDocument::where('status', 'Terkirim')
            ->select('id', 'no_travel_document', 'send_to', 'project', 'posting_date', 'end_time')
            ->orderByDesc('end_time')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('shippings.index')],
            ['label' => 'Tracking Pengiriman', 'url' => '#']
        ];

        return view('General.tracker', compact(
            'breadcrumbs',
            'activeShippings',
            'deliveredShippings',
            'totalActive',
            'totalDelivered'
        ));
    }

    public function activeLast()
    {
        $activeDocs = TravelDocument::whereRaw("LOWER(status) LIKE '%sedang%'")
            ->select('id','no_travel_document','send_to','project','posting_date','status')
            ->get();

        $data = [];

        foreach ($activeDocs as $td) {
            // tracking terbaru berdasarkan time_stamp
            $latestTs = TrackingSystem::where('travel_document_id', $td->id)
                ->orderBy('time_stamp', 'desc')
                ->first(['track_id','time_stamp','status']);

            $lastLoc = null;

            if ($latestTs && $latestTs->track_id) {
                $lastLoc = Location::where('track_id', $latestTs->track_id)
                    ->orderBy('time_stamp', 'desc')
                    ->first(['latitude','longitude','time_stamp']);
            }

            $data[] = [
                'id' => $td->id,
                'no_travel_document' => $td->no_travel_document,
                'send_to' => $td->send_to,
                'project' => $td->project,
                'status' => $td->status,
                'last_location' => $lastLoc ? [
                    'latitude' => (float) $lastLoc->latitude,
                    'longitude' => (float) $lastLoc->longitude,
                    'timestamp' => $lastLoc->time_stamp,
                ] : null,
            ];
        }

        return response()->json(['success' => true, 'data' => $data]);
    }


    public function detail($id)
    {
        $travelDocument = TravelDocument::findOrFail($id);

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('shippings.index')],
            ['label' => 'Tracking Pengiriman', 'url' => route('tracking.index')],
            ['label' => $travelDocument->no_travel_document, 'url' => '#']
        ];

        return view('General.tracker-show', compact('travelDocument', 'breadcrumbs'));
    }


    /**
     * Cari tracking berdasarkan no_travel_document (dipanggil via AJAX)
     */
    // public function search(Request $request)
    // {
    //     $request->validate([
    //         'no_travel_document' => 'required|string|max:100'
    //     ]);

    //     // Ambil travel document + relasi trackingSystems
    //     $travelDocument = TravelDocument::where('no_travel_document', $request->no_travel_document)
    //         ->with('trackingSystems')
    //         ->first();

    //     if (!$travelDocument || $travelDocument->trackingSystems->isEmpty()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Data pengiriman tidak ditemukan atau belum memiliki tracking'
    //         ]);
    //     }

    //     // Ekstrak lokasi dari tabel `location` berdasarkan track_id
    //     $locations = $this->extractLocations($travelDocument);

    //     if (empty($locations)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Belum ada data lokasi yang direkam untuk pengiriman ini'
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'locations' => $locations
    //     ]);
    // }
    public function search(Request $request)
    {
        $search = trim((string) $request->query('search', $request->query('no_travel_document', '')));

        if ($search === '') {
            return response()->json([
                'success' => false,
                'message' => 'Nomor SJN harus diisi'
            ], 422);
        }

        // Cari berdasarkan nomor SJN / no_travel_document
        $travelDocument = TravelDocument::where('no_travel_document', $search)
            ->with('trackingSystems')
            ->first();

        if (!$travelDocument) {
            return response()->json([
                'success' => false,
                'message' => 'Surat jalan tidak ditemukan'
            ], 404);
        }

        // Ekstrak lokasi dari tabel `location` berdasarkan track_id
        $locations = $this->extractLocations($travelDocument);

        if (empty($locations)) {
            // pastikan field origin ada
            if ($travelDocument->latitude && $travelDocument->longitude) {
                $locations[] = [
                    'latitude'  => (float) $travelDocument->latitude,
                    'longitude' => (float) $travelDocument->longitude,
                    'timestamp' => $travelDocument->date_no_travel_document
                        ?? now()->toDateTimeString()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'status' => $travelDocument->status,
            'locations' => $locations
        ]);
    }

    /**
     * Ambil rute dari OpenRouteService (via backend agar API key aman)
     */
    // public function getRoute(Request $request)
    // {
    //     $request->validate([
    //         'start' => 'required|array|size:2',
    //         'end' => 'required|array|size:2',
    //         'start.0' => 'numeric|between:-90,90',
    //         'start.1' => 'numeric|between:-180,180',
    //         'end.0' => 'numeric|between:-90,90',
    //         'end.1' => 'numeric|between:-180,180',
    //     ]);

    //     $apiKey = env('OPENROUTE_SERVICE_API_KEY');
    //     if (!$apiKey) {
    //         return response()->json(['error' => 'API key rute tidak dikonfigurasi'], 500);
    //     }

    //     try {
    //         $response = Http::timeout(10)
    //             ->withHeaders(['Authorization' => $apiKey])
    //             ->get('https://api.openrouteservice.org/v2/directions/driving-car', [
    //                 'start' => implode(',', [$request->start[1], $request->start[0]]),
    //                 'end' => implode(',', [$request->end[1], $request->end[0]]),
    //                 'geometry_format' => 'geojson'
    //             ]);

    //         if ($response->successful() && !empty($response->json('features'))) {
    //             return response()->json($response->json());
    //         }

    //         return response()->json(['error' => 'Tidak dapat menghasilkan rute'], 500);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Gagal terhubung ke layanan rute'], 500);
    //     }
    // }
    public function getRoute(Request $request)
    {
        $request->validate([
            'waypoints' => 'required|array|min:2', // array [ [lat,long], ... ]
            'waypoints.*' => 'array|size:2',
            'waypoints.*.0' => 'numeric|between:-90,90',
            'waypoints.*.1' => 'numeric|between:-180,180',
        ]);

        $apiKey = env('OPENROUTE_SERVICE_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API key rute tidak dikonfigurasi'], 500);
        }

        try {
            // Format for ORS: [[long1,lat1], [long2,lat2], ...]
            $coordinates = array_map(fn($point) => [$point[1], $point[0]], $request->waypoints);

            $response = Http::timeout(30) // naikkan timeout
                ->withHeaders(['Authorization' => $apiKey])
                ->post('https://api.openrouteservice.org/v2/directions/driving-car/geojson', [
                    'coordinates' => $coordinates,
                ]);

            if ($response->successful() && !empty($response->json('features'))) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'Tidak dapat menghasilkan rute'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal terhubung ke layanan rute: ' . $e->getMessage()], 500);
        }
    }

    // --- HELPERS ---

    /**
     * Ekstrak semua lokasi dari tabel `location` berdasarkan track_id
     */
    private function extractLocations(TravelDocument $travelDocument): array
    {
        $locations = [];

        // foreach ($travelDocument->trackingSystems as $ts) {
        //     // Ambil semua lokasi untuk track_id ini, urutkan berdasarkan time_stamp
        //     $locRecords = Location::where('track_id', $ts->track_id)
        //         ->orderBy('time_stamp', 'asc')
        //         ->get(['latitude', 'longitude', 'time_stamp']);

        //     foreach ($locRecords as $loc) {
        //         if ($loc->latitude && $loc->longitude) {
        //             $locations[] = [
        //                 'latitude' => (float) $loc->latitude,
        //                 'longitude' => (float) $loc->longitude,
        //                 'timestamp' => $loc->time_stamp
        //             ];
        //         }
        //     }
        // }
        $latestTrackingSystem = $travelDocument->trackingSystems()
            ->orderBy('time_stamp', 'desc')
            ->first();

        if (!$latestTrackingSystem) {
            return [];
        }

        $locRecords = Location::where('track_id', $latestTrackingSystem->track_id)
            ->orderBy('time_stamp', 'asc')
            ->get(['latitude', 'longitude', 'time_stamp']);


        foreach ($locRecords as $loc) {
            if ($loc->latitude && $loc->longitude) {
                $locations[] = [
                    'latitude'  => (float) $loc->latitude,
                    'longitude' => (float) $loc->longitude,
                    'timestamp' => $loc->time_stamp
                ];
            }
        }

        // Urutkan seluruh array berdasarkan timestamp (jika ada multi track)
        usort($locations, fn($a, $b) => strtotime($a['timestamp']) - strtotime($b['timestamp']));

        // Hapus duplikat dalam radius 10 meter
        $filtered = [];
        foreach ($locations as $loc) {
            $isDup = false;
            foreach ($filtered as $f) {
                $dist = $this->calculateDistance($f['latitude'], $f['longitude'], $loc['latitude'], $loc['longitude']);
                // if ($dist < 0.01) { // 0.01 km = 10 meter
                if ($dist < 0.015) { // 15 meter
                    $isDup = true;
                    break;
                }
            }
            if (!$isDup) {
                $filtered[] = $loc;
            }
        }

        return $filtered;
    }

    /**
     * Hitung jarak antar dua koordinat (Haversine)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $R = 6371; // Radius bumi dalam km
        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    private function getReverseGeocode($latitude, $longitude)
    {
        try {
            $response = Http::timeout(5)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'jsonv2',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 18,
                'addressdetails' => 1,
                'accept-language' => 'id'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['display_name'])) {
                    return $data['display_name'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Geocode error: ' . $e->getMessage());
        }

        return "Lokasi tidak dikenali";
    }
}
