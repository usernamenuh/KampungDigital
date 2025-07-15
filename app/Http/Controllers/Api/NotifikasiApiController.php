<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Notifikasi;

class NotifikasiApiController extends Controller
{
    /**
     * Get all notifications for authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $notifications = Notifikasi::where('user_id', $user->id)
                                     ->orderBy('created_at', 'desc')
                                     ->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => $notifications->items(),
                'pagination' => [
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'from' => $notifications->firstItem(),
                    'to' => $notifications->lastItem(),
                    'has_more' => $notifications->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting notifications', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get unread notifications
     */
    public function getUnread(Request $request)
    {
        try {
            $user = Auth::user();
            $unreadNotifications = Notifikasi::where('user_id', $user->id)
                                           ->where('dibaca', false)
                                           ->orderBy('created_at', 'desc')
                                           ->limit($request->get('limit', 10))
                                           ->get();

            return response()->json([
                'success' => true,
                'data' => $unreadNotifications
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting unread notifications', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi belum dibaca: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $user = Auth::user();
            $unreadCount = Notifikasi::where('user_id', $user->id)
                                   ->where('dibaca', false)
                                   ->count();

            return response()->json([
                'success' => true,
                'count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting unread notifications count', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat jumlah notifikasi belum dibaca: ' . $e->getMessage(),
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get recent notifications
     */
    public function getRecent(Request $request)
    {
        try {
            $user = Auth::user();
            $recentNotifications = Notifikasi::where('user_id', $user->id)
                                           ->orderBy('created_at', 'desc')
                                           ->limit($request->get('limit', 5))
                                           ->get();

            return response()->json([
                'success' => true,
                'data' => $recentNotifications
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recent notifications', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi terbaru: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, Notifikasi $notification)
    {
        try {
            if ($notification->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan.'
                ], 403);
            }

            $notification->update(['dibaca' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai sebagai dibaca.',
                'notification' => $notification
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read', [
                'notification_id' => $notification->notifikasi_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi sebagai dibaca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = Auth::user();
            Notifikasi::where('user_id', $user->id)->update(['dibaca' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil ditandai sebagai dibaca.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi sebagai dibaca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, Notifikasi $notification)
    {
        try {
            if ($notification->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan.'
                ], 403);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting notification', [
                'notification_id' => $notification->notifikasi_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all notifications
     */
    public function destroyAll(Request $request)
    {
        try {
            $user = Auth::user();
            Notifikasi::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting all notifications', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus semua notifikasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
