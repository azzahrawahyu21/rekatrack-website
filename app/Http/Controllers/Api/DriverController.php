<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Track;
use App\Models\User;
use App\Models\TrackingSystem;
use App\Models\TravelDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\DeliveryConfirmation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(
                [
                    'message' => 'Email tidak ditemukan.',
                ],
                404,
            );
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah.',
        ]);
    }

    public function showTravelDocuments()
    {
        // $suratJalanList = TravelDocument::with('items')->get();
        $suratJalanList = TravelDocument::with(['items', 'driver:id,name'])->get();

        return response()->json([
            'data' => $suratJalanList,
        ]);
    }

    public function showDetailTravelDocument($id)
    {
        // $suratJalan = TravelDocument::where('id', $id)
        //     ->with(['items'])
        //     ->first();
        $suratJalan = TravelDocument::where('id', $id)
            ->with(['items', 'driver:id,name'])
            ->first();

        if (!$suratJalan) {
            return response()->json(
                [
                    'message' => 'Surat jalan tidak ditemukan.',
                ],
                404,
            );
        }

        return response()->json([
            'data' => $suratJalan,
        ]);
    }

    public function sendLocation(Request $request)
    {
        $request->validate([
            'travel_document_id' => 'required|array',
            'travel_document_id.*' => 'exists:travel_document,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            // optional kalau dari device ada timestamp:
            // 'captured_at' => 'nullable|date',
        ]);

        $user = Auth::user();
        $driverId = $user->id;

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        $responses = [];

        foreach ($request->travel_document_id as $documentId) {
            $result = DB::transaction(function () use ($documentId, $driverId, $lat, $lng) {
                // lock dokumen biar tidak race jika 2 shipment update bareng
                $travelDocument = TravelDocument::where('id', $documentId)->lockForUpdate()->first();

                if (!$travelDocument) {
                    return [
                        'travel_document_id' => $documentId,
                        'message' => 'Surat jalan tidak ditemukan.',
                        'status' => 'error',
                    ];
                }

                if ($travelDocument->status === 'Terkirim') {
                    return [
                        'travel_document_id' => $documentId,
                        'message' => 'Surat jalan sudah terkirim, pengiriman lokasi tidak dapat dilakukan.',
                        'status' => 'error',
                    ];
                }

                // =========================
                // RULES (fix)
                // =========================
                $deliveryType = $travelDocument->delivery_type ?? 'Dalam Kota';
                $isOutTown = $deliveryType === 'Luar Kota';

                if ($isOutTown) {
                    // LUAR KOTA
                    $MIN_DISTANCE_KM = 1.0; // 1 KM (FIX)
                    $MIN_TIME_INTERVAL_SECONDS = 1800; // 30 menit
                    $MIN_STOP_TIME_SECONDS = 600; // 10 menit
                } else {
                    // DALAM KOTA
                    $MIN_DISTANCE_KM = 0.005; // 5 meter
                    $MIN_TIME_INTERVAL_SECONDS = 30; // 30 detik
                    $MIN_STOP_TIME_SECONDS = 60; // 60 detik
                }

                // =========================
                // Ambil / buat session per dokumen (TrackingSystem dulu)
                // =========================
                $trackingSystem = TrackingSystem::where('travel_document_id', $documentId)->where('status', 'active')->orderByDesc('time_stamp')->lockForUpdate()->first();

                if ($trackingSystem && $trackingSystem->track) {
                    $track = $trackingSystem->track;

                    // pastikan track driver benar + aktif
                    if ((int) $track->driver_id !== (int) $driverId) {
                        // kalau ternyata track milik driver lain (data kotor), buat baru
                        $track = null;
                    } elseif ($track->status !== 'active') {
                        $track->update(['status' => 'active']);
                    }
                } else {
                    $track = null;
                }

                if (!$track) {
                    $track = Track::create([
                        'driver_id' => $driverId,
                        'time_stamp' => now(),
                        'status' => 'active',
                    ]);

                    $trackingSystem = TrackingSystem::create([
                        'track_id' => $track->id,
                        'travel_document_id' => $documentId,
                        'time_stamp' => now(),
                        'status' => 'active',
                    ]);
                } else {
                    // update heartbeat trackingSystem
                    $trackingSystem->update([
                        'time_stamp' => now(),
                        'status' => 'active',
                    ]);

                    $track->update([
                        'time_stamp' => now(),
                        'status' => 'active',
                    ]);
                }

                // =========================
                // Ambil lokasi terakhir (KHUSUS track milik dokumen ini)
                // =========================
                $lastLocation = Location::where('track_id', $track->id)->orderByDesc('time_stamp')->first();

                $distanceFromLastKm = 0.0;
                $secondsDiff = null;

                if ($lastLocation) {
                    $distanceFromLastKm = $this->calculateDistance((float) $lastLocation->latitude, (float) $lastLocation->longitude, $lat, $lng);

                    $secondsDiff = now()->diffInSeconds(Carbon::parse($lastLocation->time_stamp));

                    // =========================
                    // SKIP logic yang lebih jelas:
                    // skip jika jarak < threshold DAN interval < threshold
                    // =========================
                    if ($distanceFromLastKm < $MIN_DISTANCE_KM && $secondsDiff < $MIN_TIME_INTERVAL_SECONDS) {
                        return [
                            'travel_document_id' => $documentId,
                            'track_id' => $track->id,
                            'message' => $isOutTown ? 'Luar kota: terlalu dekat & interval belum cukup, tidak disimpan.' : 'Dalam kota: terlalu dekat & terlalu cepat, tidak disimpan.',
                            'distance_m' => round($distanceFromLastKm * 1000, 1),
                            'seconds_diff' => $secondsDiff,
                            'status' => 'skipped',
                        ];
                    }
                }

                // checkpoint: pertama kali ATAU berhenti lama
                $isCheckpoint = 0;
                if (!$lastLocation) {
                    $isCheckpoint = 1;
                } elseif ($secondsDiff !== null && $secondsDiff >= $MIN_STOP_TIME_SECONDS) {
                    $isCheckpoint = 1;
                }

                Location::create([
                    'track_id' => $track->id,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'time_stamp' => now(),
                    'is_checkpoint' => $isCheckpoint,
                    'distance_from_last' => round($distanceFromLastKm * 1000, 1),
                ]);

                // start_time dan status
                // $updates = ['status' => 'Sedang dikirim'];
                // if (is_null($travelDocument->start_time)) {
                //     $updates['start_time'] = now();
                // }
                // $travelDocument->update($updates);
                // start_time, status, dan driver_id
                $updates = ['status' => 'Sedang dikirim'];

                // hanya set driver saat pengiriman benar-benar mulai
                if (is_null($travelDocument->start_time)) {
                    $updates['start_time'] = now();
                    $updates['driver_id'] = $driverId; // ✅ catat driver pertama yang memulai
                } else {
                    // opsional: kalau sudah mulai tapi driver_id masih null (data lama), isi sekali
                    if (is_null($travelDocument->driver_id)) {
                        $updates['driver_id'] = $driverId;
                    }

                    // opsional ketat: kalau sudah ada driver_id dan beda driver, tolak
                    // if (!is_null($travelDocument->driver_id) && (int)$travelDocument->driver_id !== (int)$driverId) {
                    //     return [
                    //         'travel_document_id' => $documentId,
                    //         'message' => 'Surat jalan sudah diambil driver lain.',
                    //         'status' => 'error',
                    //     ];
                    // }
                }

                $travelDocument->update($updates);

                return [
                    'travel_document_id' => $documentId,
                    'track_id' => $track->id,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'delivery_type' => $deliveryType,
                    'status' => 'active',
                    'message' => 'Lokasi berhasil dikirim.',
                ];
            });

            $responses[] = $result;
        }

        return response()->json(
            [
                'message' => 'Proses pengiriman lokasi selesai.',
                'data' => $responses,
            ],
            201,
        );
    }

    public function updateStatusSendSJN(Request $request)
    {
        $request->validate([
            'travel_document_id' => 'required|array',
            'travel_document_id.*' => 'exists:travel_document,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $responses = [];

        foreach ($request->travel_document_id as $documentId) {
            $trackingSystem = TrackingSystem::where('travel_document_id', $documentId)->orderBy('time_stamp', 'desc')->first();

            if (!$trackingSystem) {
                $responses[] = [
                    'travel_document_id' => $documentId,
                    'message' => 'Tracking system tidak ditemukan.',
                    'status' => 'error',
                ];
                continue;
            }

            if ($trackingSystem->status === 'non-active') {
                $responses[] = [
                    'travel_document_id' => $documentId,
                    'message' => 'Status sudah non-active.',
                    'status' => 'non-active',
                ];
                continue;
            }

            $trackingSystem->update([
                'status' => 'non-active',
                'time_stamp' => now(),
            ]);

            if ($trackingSystem->track) {
                Location::create([
                    'track_id' => $trackingSystem->track->id,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'time_stamp' => now(),
                ]);

                $allNonActive = $trackingSystem->track->trackingSystems()->where('status', '!=', 'non-active')->count() === 0;

                if ($allNonActive && $trackingSystem->track->status !== 'non-active') {
                    $trackingSystem->track->update(['status' => 'non-active']);
                }
            }

            $responses[] = [
                'travel_document_id' => $documentId,
                'message' => 'Status berhasil diubah menjadi non-active dan lokasi disimpan.',
                'status' => 'non-active',
            ];
        }

        return response()->json([
            'message' => 'Permintaan update status selesai diproses.',
            'results' => $responses,
        ]);
    }

    public function completeDelivery(Request $request)
    {
        Log::info('completeDelivery payload', $request->all());

        $request->validate([
            'travel_document_id' => 'required|array',
            'travel_document_id.*' => 'exists:travel_document,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'receiver_name' => 'required|string|max:255',
            'received_at' => 'required|date',
            'note' => 'nullable|string',
            'photo_paths' => 'required|array|min:1',
            'photo_paths.*' => 'required|string|max:255',
            // 'photo_path' => 'required|string|max:255',
        ]);

        $responses = [];

        foreach ($request->travel_document_id as $travelDocumentId) {
            $tracking = Track::whereHas('trackingSystems', function ($query) use ($travelDocumentId) {
                $query->where('travel_document_id', $travelDocumentId);
            })
                ->latest()
                ->first();

            if (!$tracking) {
                $responses[] = [
                    'travel_document_id' => $travelDocumentId,
                    'message' => 'Tracking tidak ditemukan',
                ];
                continue;
            }

            $travelDocument = TravelDocument::find($travelDocumentId);

            if (is_null($travelDocument->start_time)) {
                $responses[] = [
                    'travel_document_id' => $travelDocumentId,
                    'message' => 'Pengiriman belum dimulai.',
                ];
                continue;
            }

            if (!is_null($travelDocument->end_time)) {
                $responses[] = [
                    'travel_document_id' => $travelDocumentId,
                    'message' => 'Pengiriman sudah selesai sebelumnya.',
                ];
                continue;
            }

            $travelDocument->update([
                'status' => 'Terkirim',
                'end_time' => now(),
            ]);

            $tracking->update(['status' => 'non-active']);

            $receivedAt = Carbon::parse($request->received_at)->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');

            // DeliveryConfirmation::create([
            //     'travel_document_id' => $travelDocumentId,
            //     'receiver_name' => $request->receiver_name,
            //     'received_at' => $receivedAt,
            //     'note' => $request->note,
            //     'photo_path' => $request->photo_path,
            // ]);
            $confirmation = DeliveryConfirmation::create([
                'travel_document_id' => $travelDocumentId,
                'receiver_name' => $request->receiver_name,
                'received_at' => $receivedAt,
                'note' => $request->note,
                // 'photo_path' => ... (opsional: bisa dihapus kalau kolomnya mau kamu drop nanti)
            ]);

            $confirmation->photos()->createMany(collect($request->photo_paths)->map(fn($p) => ['photo_path' => $p])->toArray());

            // PERBAIKAN: Ambil last location untuk hitung distance
            $lastLocation = Location::where('track_id', $tracking->id)->orderBy('time_stamp', 'desc')->first();
            $distanceFromLast = 0;
            if ($lastLocation) {
                $distanceFromLast = $this->calculateDistance($lastLocation->latitude, $lastLocation->longitude, $request->latitude, $request->longitude);
            }

            // Simpan lokasi terakhir sebagai penanda selesai (opsional tapi baik)
            Location::create([
                'track_id' => $tracking->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'time_stamp' => now(),
                'is_checkpoint' => 1, // Mark as end checkpoint
                'distance_from_last' => $distanceFromLast,
            ]);

            $responses[] = [
                'travel_document_id' => $travelDocumentId,
                'tracking_status' => $tracking->status,
                'travel_document_status' => $travelDocument->status,
                'message' => 'Berhasil',
            ];
        }

        return response()->json([
            'message' => 'Proses penyelesaian pengiriman selesai.',
            'data' => $responses,
        ]);
    }

    public function showByScanCode($code)
    {
        if (!str_starts_with($code, 'SJNID:')) {
            return response()->json(['message' => 'Format QR tidak valid'], 400);
        }

        $id = substr($code, 7); // ekstrak dari "SJNID:22"

        if (!is_numeric($id)) {
            return response()->json(['message' => 'ID tidak valid'], 400);
        }

        $travelDocument = TravelDocument::where('id', (int) $id)
            ->with('items') // jika ada relasi items
            ->first();

        if (!$travelDocument) {
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }

        return response()->json(['data' => $travelDocument]);
    }

    public function uploadDeliveryPhoto(Request $request)
    {
        if (!$request->hasFile('photo')) {
            return response()->json(
                [
                    'message' => 'Photo gagal diunggah.',
                    'data' => [
                        'errors' => ['photo' => ['File tidak terkirim (photo missing).']],
                    ],
                ],
                422,
            );
        }

        $file = $request->file('photo');

        if (!$file->isValid()) {
            return response()->json(
                [
                    'message' => 'Photo gagal diunggah.',
                    'data' => [
                        'errors' => ['photo' => ['File tidak valid.']],
                    ],
                ],
                422,
            );
        }

        $request->validate([
            'photo' => 'required|file|max:5120|mimes:jpg,jpeg,png,heic,heif',
        ]);

        $path = $file->store('delivery_photos', 'public');

        return response()->json([
            'photo_path' => $path,
        ]);
    }

    // fungsi bantu untuk menghitung jarak antara dua koordinat (Haversine Formula)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $R = 6371; // Radius bumi dalam km
        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);
        $a = sin($latDiff / 2) * sin($latDiff / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    public function showDeliveryConfirmation($id)
    {
        $confirmation = DeliveryConfirmation::where('travel_document_id', $id)->first();
        if (!$confirmation) {
            return response()->json(
                [
                    'message' => 'Bukti pengiriman tidak ditemukan untuk surat jalan ini',
                    'error' => 'not_found',
                ],
                404,
            );
        }

        return response()->json([
            'data' => $confirmation,
            'photo_url' => $confirmation->photo_path ? asset('storage/' . $confirmation->photo_path) : null,
        ]);
    }
}
