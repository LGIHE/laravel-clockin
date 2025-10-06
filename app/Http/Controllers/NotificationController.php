<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', 'App\Models\User')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications),
        ]);
    }

    /**
     * Mark a notification as read.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Notification not found',
                ],
            ], 404);
        }

        DB::table('notifications')
            ->where('id', $id)
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        $updatedNotification = DB::table('notifications')
            ->where('id', $id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => new NotificationResource($updatedNotification),
            'message' => 'Notification marked as read',
        ]);
    }
}
