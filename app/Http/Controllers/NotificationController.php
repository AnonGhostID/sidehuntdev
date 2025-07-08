<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the authenticated user (for dropdown)
     */
    public function index(Request $request): JsonResponse
    {
        if (!session()->has('account')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = session('account');
        $limit = $request->get('limit', 10);

        // Only get unread notifications for the dropdown
        $notifications = Notification::where('user_id', $user['id'])
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $unreadCount = Notification::where('user_id', $user['id'])
            ->unread()
            ->count();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'is_read' => $notification->isRead(),
                    'time_ago' => $notification->time_ago,
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Show notifications page
     */
    public function page()
    {
        if (!session()->has('account')) {
            return redirect('/Login');
        }

        $user = session('account');
        
        // Get all notifications (read and unread) with pagination
        $notifications = Notification::where('user_id', $user['id'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('manajemen.notifications.page', compact('notifications'));
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(): JsonResponse
    {
        if (!session()->has('account')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = session('account');
        $unreadCount = Notification::where('user_id', $user['id'])
            ->unread()
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id): JsonResponse
    {
        if (!session()->has('account')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = session('account');
        $notification = Notification::where('id', $id)
            ->where('user_id', $user['id'])
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        if (!session()->has('account')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = session('account');
        Notification::where('user_id', $user['id'])
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function delete($id): JsonResponse
    {
        if (!session()->has('account')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = session('account');
        $notification = Notification::where('id', $id)
            ->where('user_id', $user['id'])
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get recent notifications for polling
     */
    public function poll(): JsonResponse
    {
        if (!session()->has('account')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = session('account');
        
        // Get notifications from last 5 minutes to check for new ones
        $recentNotifications = Notification::where('user_id', $user['id'])
            ->where('created_at', '>=', now()->subMinutes(5))
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Notification::where('user_id', $user['id'])
            ->unread()
            ->count();

        return response()->json([
            'has_new' => $recentNotifications->count() > 0,
            'unread_count' => $unreadCount,
            'recent_notifications' => $recentNotifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->isRead(),
                    'time_ago' => $notification->time_ago,
                ];
            }),
        ]);
    }
}
