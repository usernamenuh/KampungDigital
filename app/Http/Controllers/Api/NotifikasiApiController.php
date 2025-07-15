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
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        try {
            $notifications = Auth::user()->notifikasis()
                                ->orderBy('created_at', 'desc')
                                ->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting notifications', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi.'
            ], 500);
        }
    }

    /**
     * Get unread notifications for the authenticated user.
     */
    public function getUnread(Request $request)
    {
        try {
            $notifications = Auth::user()->notifikasis()
                                ->where('is_read', false)
                                ->orderBy('created_at', 'desc')
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting unread notifications', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi belum dibaca.'
            ], 500);
        }
    }

    /**
     * Get unread notifications count for the authenticated user.
     */
    public function getUnreadCount()
    {
        try {
            $count = Auth::user()->notifikasis()->where('is_read', false)->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting unread notifications count', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat jumlah notifikasi belum dibaca.'
            ], 500);
        }
    }

    /**
     * Get recent notifications for the authenticated user.
     */
    public function getRecent(Request $request)
    {
        try {
            $notifications = Auth::user()->notifikasis()
                                ->orderBy('created_at', 'desc')
                                ->limit($request->get('limit', 5))
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting recent notifications', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi terbaru.'
            ], 500);
        }
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Notifikasi $notification)
    {
        try {
            if ($notification->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan.'
                ], 403);
            }

            $notification->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai sudah dibaca.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read', [
                'notification_id' => $notification->notifikasi_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi sudah dibaca.'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead()
    {
        try {
            Auth::user()->notifikasis()->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil ditandai sudah dibaca.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi sudah dibaca.'
            ], 500);
        }
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(Notifikasi $notification)
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
                'message' => 'Gagal menghapus notifikasi.'
            ], 500);
        }
    }

    /**
     * Delete all notifications for the authenticated user.
     */
    public function destroyAll()
    {
        try {
            Auth::user()->notifikasis()->delete();

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
                'message' => 'Gagal menghapus semua notifikasi.'
            ], 500);
        }
    }
}
