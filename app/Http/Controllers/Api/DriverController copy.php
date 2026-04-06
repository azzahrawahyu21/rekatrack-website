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

class DriverController extends Controller
{
    public function forgotPassword(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email tidak ditemukan.',
            ], 404);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah.',
        ]);
    }


    public function showTravelDocuments(){
        $suratJalanList = TravelDocument::with('items')->get();

        return response()->json([
            'data' => $suratJalanList,
        ]);
    }
    
    public function showDetailTravelDocument($id) {
        $suratJalan = TravelDocument::where('id', $id)->with(['items'])->first();

        if (!$suratJalan) {
            return response()->json([
                'message' => 'Surat jalan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $suratJalan,
        ]);
    }

    // pada db masing menggunakan track id untuk bagian ini
    public function sendLocation(Request $request){
        $request->validate([
            'travel_document_id' => 'required|array',
            'travel_document_id.*' => 'exists:travel_document,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();
        $driverId = $user->id;

        $responses = [];

        foreach ($request->travel_document_id as $documentId) {
            $travelDocument = TravelDocument::find($documentId);

            // Cek status surat jalan, jika sudah terkirim skip
            if ($travelDocument->status === 'Terkirim') {
                $responses[] = [
                    'travel_document_id' => $documentId,
                    'message' => 'Surat jalan sudah terkirim, pengiriman lokasi tidak dapat dilakukan.',
                    'status' => 'error',
                ];
                continue; // skip proses berikutnya untuk dokumen ini
            }

            // Cari track aktif driver terkait dokumen
            $track = Track::where('driver_id', $driverId)
                ->whereHas('trackingSystems', function ($query) use ($documentId) {
                    $query->where('travel_document_id', $documentId);
                })->latest()->first();

            if (!$track) {
                // Buat track baru dan tracking system aktif
                $track = Track::create([
                    'driver_id' => $driverId,
                    'time_stamp' => now(),
                    'status' => 'active',
                ]);

                TrackingSystem::create([
                    'track_id' => $track->id,
                    'travel_document_id' => $documentId,
                    'time_stamp' => now(),
                    'status' => 'active',
                ]);
            } else {
                // Update atau buat tracking system jadi aktif
                TrackingSystem::updateOrCreate(
                    [
                        'track_id' => $track->id,
                        'travel_document_id' => $documentId,
                    ],
                    [
                        'time_stamp' => now(),
                        'status' => 'active',
                    ]
                );

                // Update status track jika belum aktif
                if ($track->status !== 'active') {
                    $track->update(['status' => 'active']);
                }
            }

            // Simpan lokasi terbaru
            Location::create([
                'track_id' => $track->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'time_stamp' => now(),
            ]);


            if (is_null($travelDocument->start_time)) {
                $travelDocument->update([
                    'status' => 'Sedang dikirim',
                    'start_time' => now(),
                ]);
            } else {
                // Hanya update status jika perlu (opsional)
                $travelDocument->update(['status' => 'Sedang dikirim']);
            }
            // // Update status travel document jadi Sedang dikirim
            // $travelDocument->update([
            //     'status' => 'Sedang dikirim',
            // ]);

            $responses[] = [
                'travel_document_id' => $documentId,
                'track_id' => $track->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => 'active',
                'message' => 'Lokasi berhasil dikirim.',
            ];
        }

        return response()->json([
            'message' => 'Proses pengiriman lokasi selesai.',
            'data' => $responses,
        ], 201);
    }



    // Update status tracking system untuk banyak dokumen
    public function updateStatusSendSJN(Request $request) {
        $request->validate([
            'travel_document_id' => 'required|array',
            'travel_document_id.*' => 'exists:travel_document,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $responses = [];

        foreach ($request->travel_document_id as $documentId) {
            // Ambil TrackingSystem terbaru
            $trackingSystem = TrackingSystem::where('travel_document_id', $documentId)
                ->orderBy('time_stamp', 'desc')
                ->first();

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

            // Update status TrackingSystem
            $trackingSystem->update([
                'status' => 'non-active',
                'time_stamp' => now(),
            ]);

            // Simpan lokasi terakhir ke table Location (jika track tersedia)
            if ($trackingSystem->track) {
                $track = $trackingSystem->track;

                Location::create([
                    'track_id' => $trackingSystem->track->id,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'time_stamp' => now(),
                ]);

                $allNonActive = $track->trackingSystems()->where('status', '!=', 'non-active')->count() === 0;

                if ($allNonActive && $track->status !== 'non-active') {
                    $track->update(['status' => 'non-active']);
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

    public function completeDelivery(Request $request){
        $request->validate([
            'travel_document_id' => 'required|array',
            'travel_document_id.*' => 'exists:travel_document,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $responses = [];

        foreach ($request->travel_document_id as $travelDocumentId) {
            $tracking = Track::whereHas('trackingSystems', function ($query) use ($travelDocumentId) {
                $query->where('travel_document_id', $travelDocumentId);
            })->latest()->first();

            if (!$tracking) {
                $responses[] = [
                    'travel_document_id' => $travelDocumentId,
                    'message' => 'Tracking tidak ditemukan',
                ];
                continue;
            }

            $tracking->update(['status' => 'non-active']);

            $travelDocument = TravelDocument::find($travelDocumentId);
            $travelDocument->update(['status' => 'Terkirim']);

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
}
