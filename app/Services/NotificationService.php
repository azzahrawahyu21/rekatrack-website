<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\TravelDocument;
use App\Models\User;
use App\Mail\DriverNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    // ── Template teks terpusat ─────────────────────────────
    private function body(TravelDocument $doc, string $line2): string
    {
        return "{$doc->no_travel_document} • {$doc->project}\n{$line2}";
    }

    // ── Kirim Email ───────────────────────────────────────
    private function sendEmailNotification(Notification $notif): void
    {
        if (!$notif->user_id) {
            Log::warning('No user_id', ['notif_id' => $notif->id]);
            return;
        }

        $user = User::find($notif->user_id);

        if (!$user) {
            Log::warning('User not found', ['user_id' => $notif->user_id]);
            return;
        }

        if (empty($user->email)) {
            Log::warning('User has no email', ['user_id' => $user->id, 'name' => $user->name]);
            return;
        }

        Log::info('Attempting to send email', [
            'to' => $user->email,
            'title' => $notif->title,
            'driver_name' => $user->name
        ]);

        try {
            Mail::to($user->email)->send(new DriverNotification($notif));

            Log::info('✅ QUEUE EMAIL SUCCESS', ['email' => $user->email]);
        } catch (\Exception $e) {
            Log::error('❌ EMAIL FAILED', [
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    // ── 1. Assign ke driver spesifik ──────────────────────
    public function notifyAssigned(TravelDocument $doc, int $driverId): Notification
    {
        $notification = Notification::create([
            'user_id'            => $driverId,
            'travel_document_id' => $doc->id,
            'type'               => 'assigned',
            'title'              => 'Tugas Pengiriman Baru',
            'body'               => $this->body($doc, 'Ditugaskan kepada Anda, silakan ambil surat jalan'),
            'is_broadcast'       => false,
        ]);

        $this->sendEmailNotification($notification);

        return $notification;
    }

    // ── 2. Fallback broadcast ─────────────────────────────
    public function notifyFallbackBroadcast(TravelDocument $doc): int
    {
        $driverIds = User::whereHas('role', fn($q) => $q->where('name', 'driver'))
            ->pluck('id');

        if ($driverIds->isEmpty()) {
            Log::warning('notifyFallbackBroadcast: tidak ada driver aktif');
            return 0;
        }

        $now  = now();
        $body = $this->body($doc, 'Tersedia untuk diambil di kantor');

        $rows = $driverIds->map(fn($id) => [
            'user_id'            => $id,
            'travel_document_id' => $doc->id,
            'type'               => 'fallback',
            'title'              => 'Surat Jalan Tersedia',
            'body'               => $body,
            'is_broadcast'       => true,
            'read_at'            => null,
            'created_at'         => $now,
            'updated_at'         => $now,
        ])->values()->all();

        DB::table('notifications')->insert($rows);

        // Kirim email ke semua driver
        $notifications = Notification::where('travel_document_id', $doc->id)
            ->where('type', 'fallback')
            ->get();

        foreach ($notifications as $notif) {
            $this->sendEmailNotification($notif);
        }

        return count($rows);
    }

    // ── Helper untuk Pickup, In Transit, Delivered ───────
    private function createForDriver(
        TravelDocument $doc,
        string $type,
        string $title,
        string $line2
    ): ?Notification {
        if (!$doc->driver_id) return null;

        $notification = Notification::create([
            'user_id'            => $doc->driver_id,
            'travel_document_id' => $doc->id,
            'type'               => $type,
            'title'              => $title,
            'body'               => $this->body($doc, $line2),
            'is_broadcast'       => false,
        ]);

        $this->sendEmailNotification($notification);

        return $notification;
    }

    public function notifyPickup(TravelDocument $doc): ?Notification
    {
        return $this->createForDriver($doc, 'pickup', 'Surat Jalan Diambil', 'Pengiriman telah dimulai');
    }

    public function notifyInTransit(TravelDocument $doc): ?Notification
    {
        return $this->createForDriver($doc, 'in_transit', 'Pengiriman Berlangsung', 'Dalam proses pengantaran');
    }

    public function notifyDelivered(TravelDocument $doc): ?Notification
    {
        return $this->createForDriver($doc, 'delivered', 'Pengiriman Selesai', 'Telah terkirim ke tujuan');
    }

    // Notifikasi Admin (bisa ditambah email juga nanti)
    public function notifyAdminPickup(TravelDocument $doc): void { /* ... */ }
    public function notifyAdminDelivered(TravelDocument $doc): void { /* ... */ }
}