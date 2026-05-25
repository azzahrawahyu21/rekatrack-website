<?php

namespace App\Console\Commands;

use App\Models\TravelDocument;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FallbackUnassignedDocuments extends Command
{
    protected $signature   = 'notifications:fallback
                                {--dry-run : Simulasi tanpa benar-benar kirim notifikasi}';
    protected $description = 'Broadcast surat jalan yang belum diproses dalam 1 hari ke semua driver';

    public function handle(NotificationService $service): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info($isDryRun ? '🔍 Mode dry-run aktif' : '🚀 Menjalankan fallback notifikasi...');

        $docs = TravelDocument::query()
            ->where('status', 'Belum terkirim')
            ->whereNotNull('driver_id')           // sudah pernah di-assign tapi tidak direspon
            // ->where('created_at', '<=', now()->subDay())
            ->where('created_at', '<=', now()->subHours(1))
            ->whereDoesntHave('notifications', fn($q) =>
                $q->where('type', 'fallback')
            )
            ->get();

        if ($docs->isEmpty()) {
            $this->info('✅ Tidak ada surat jalan yang perlu di-broadcast.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$docs->count()} surat jalan:");

        foreach ($docs as $doc) {
            $this->line("  → {$doc->no_travel_document} | {$doc->project}");

            if (!$isDryRun) {
                try {
                    $service->notifyFallbackBroadcast($doc);
                } catch (\Throwable $e) {
                    Log::error('Fallback notification failed', [
                        'travel_document_id' => $doc->id,
                        'error' => $e->getMessage(),
                    ]);
                    $this->error("  ✗ Gagal: {$doc->no_travel_document}");
                    continue;
                }
            }

            $this->info("  ✓ Broadcast: {$doc->no_travel_document}");
        }

        $this->info($isDryRun
            ? "🔍 Dry-run selesai. {$docs->count()} dokumen akan di-broadcast."
            : "✅ Selesai. {$docs->count()} surat jalan di-broadcast."
        );

        return self::SUCCESS;
    }
}