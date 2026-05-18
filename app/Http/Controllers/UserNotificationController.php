<?php

namespace App\Http\Controllers;

use App\Models\ApplicationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = ApplicationNotification::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'unread_count' => $notifications->where('is_read', false)->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markRead(Request $request): JsonResponse
    {
        ApplicationNotification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
