<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/notifications
     * Dukung filter: ?unread=1 dan ?type=assigned
     */
    public function index(Request $request): JsonResponse
    {
        $driver = $request->user();

        $query = Notification::forDriver($driver->id)
            ->with('travelDocument:id,no_travel_document,project,status')
            ->latest();

        // Filter opsional dari mobile
        if ($request->boolean('unread')) {
            $query->unread();
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // $notifications = $query->paginate(20);
        $notifications = $query->paginate(20)->through(fn($n) => [
            'id'              => $n->id,
            'title'           => $n->title,
            'message'         => $n->body,           // ✅ alias body → message
            'type'            => $n->type,
            'is_read'         => !is_null($n->read_at), // ✅ konversi ke boolean
            'is_broadcast'    => $n->is_broadcast,
            'created_at'      => $n->created_at->diffForHumans(), // ✅ "5 menit lalu"
            'travelDocument'  => $n->travelDocument,
        ]);

        return response()->json([
            'success'      => true,
            'unread_count' => Notification::forDriver($driver->id)->unread()->count(),
            'data'         => $notifications,
        ]);
    }

    /**
     * GET /api/notifications/unread-count
     * Khusus untuk update badge tanpa load semua notifikasi.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::forDriver($request->user()->id)->unread()->count();

        return response()->json([
            'success' => true,
            'count'   => $count,
        ]);
    }

    /**
     * POST /api/notifications/{id}/read
     */
    public function markRead(Request $request, int $id): JsonResponse
    {
        $notification = Notification::forDriver($request->user()->id)
            ->whereNull('read_at')
            ->findOrFail($id);

        $notification->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca.',
        ]);
    }

    /**
     * POST /api/notifications/read-all
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $affected = Notification::forDriver($request->user()->id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'success'  => true,
            'message'  => 'Semua notifikasi sudah dibaca.',
            'affected' => $affected,
        ]);
    }
}