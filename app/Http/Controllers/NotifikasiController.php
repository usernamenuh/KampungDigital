<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotifikasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);
        
        $notifikasi = $user->notifikasi()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $notifikasi->items(),
                'pagination' => [
                    'current_page' => $notifikasi->currentPage(),
                    'per_page' => $notifikasi->perPage(),
                    'total' => $notifikasi->total(),
                    'last_page' => $notifikasi->lastPage(),
                ]
            ]);
        }
        
        return view('notifikasi.index', compact('notifikasi'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notifikasi $notifikasi): JsonResponse
    {
        try {
            // Check if notification belongs to current user
            if ($notifikasi->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            $notifikasi->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai sebagai dibaca'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi sebagai dibaca'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            $count = $user->notifikasi()->where('dibaca', false)->count();
            
            $user->notifikasi()->where('dibaca', false)->update([
                'dibaca' => true,
                'dibaca_pada' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menandai {$count} notifikasi sebagai dibaca"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi sebagai dibaca'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notifikasi $notifikasi): JsonResponse
    {
        try {
            // Check if notification belongs to current user
            if ($notifikasi->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            $notifikasi->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi'
            ], 500);
        }
    }

    /**
     * Delete all notifications
     */
    public function destroyAll(): JsonResponse
    {
        try {
            $user = Auth::user();
            $count = $user->notifikasi()->count();
            $user->notifikasi()->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$count} notifikasi"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting all notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus semua notifikasi'
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $user = Auth::user();
            $count = $user->notifikasi()->where('dibaca', false)->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jumlah notifikasi belum dibaca',
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get recent notifications
     */
    public function getRecent(): JsonResponse
    {
        try {
            $user = Auth::user();
            $notifications = $user->notifikasi()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($notif) {
                    return [
                        'id' => $notif->id,
                        'title' => $notif->judul,
                        'message' => $notif->pesan,
                        'type' => $notif->tipe,
                        'category' => $notif->kategori,
                        'read' => $notif->dibaca,
                        'timestamp' => $notif->created_at->toISOString(),
                        'time_ago' => $notif->created_at->diffForHumans(),
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting recent notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil notifikasi terbaru',
                'data' => []
            ], 500);
        }
    }
}
