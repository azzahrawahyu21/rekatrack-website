<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationWebController extends Controller
{    
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $this->notificationQuery($request->user())
            ->unread()
            ->count();

        return response()->json(['success' => true, 'count' => $count]);
    }

    /** GET /web/notifications */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $this->notificationQuery($user)
            ->with('travelDocument:id,no_travel_document,project,status')
            ->latest()
            ->take(20)
            ->get()
            ->map(fn($n) => [
                'id'               => $n->id,
                'title'            => $n->title,
                'message'          => $n->body,
                'type'             => $n->type,
                'is_read'          => !is_null($n->read_at),
                'created_at'       => $n->created_at->diffForHumans(),
                'travel_document'  => $n->travelDocument ? [
                    'id'                 => $n->travelDocument->id,
                    'no_travel_document' => $n->travelDocument->no_travel_document,
                    'project'            => $n->travelDocument->project,
                    'status'             => $n->travelDocument->status,
                ] : null,
            ]);

        return response()->json([
            'success'      => true,
            'unread_count' => $this->notificationQuery($user)->unread()->count(),
            'data'         => ['data' => $notifications],
        ]);
    }

    /** POST /web/notifications/read-all */
    public function markAllRead(Request $request): JsonResponse
    {
        $this->notificationQuery($request->user())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /** POST /web/notifications/{id}/read */
    public function markRead(Request $request, int $id): JsonResponse
    {
        $this->notificationQuery($request->user())
            ->findOrFail($id)
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    private function isAdmin(User $user): bool
    {
        return in_array(strtolower($user->role?->name ?? ''), ['admin', 'super admin']);
    }

    private function notificationQuery(User $user)
    {
        if ($this->isAdmin($user)) {
            // Admin terima notifikasi tipe pickup_admin & delivered_admin
            return Notification::where('user_id', $user->id)
                ->whereIn('type', ['pickup_admin', 'delivered_admin', 'assigned']);
        }

        // Driver terima notifikasi personal + broadcast
        return Notification::forDriver($user->id);
    }

}
