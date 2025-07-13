<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotifikasiApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get notifications list
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Notifikasi::where('user_id', $user->id);

            // Apply filters
            if ($request->filled('status')) {
                if ($request->status === 'unread') {
                    $query->where('dibaca', false);
                } elseif ($request->status === 'read') {
                    $query->where('dibaca', true);
                }
            }

            if ($request->filled('kategori')) {
                $query->where('kategori', $request->kategori);
            }

            if ($request->filled('tipe')) {
                $query->where('tipe', $request->tipe);
            }

            // Date range filter
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 20);
            $notifications = $query->paginate($perPage);

            // Get counts
            $unreadCount = Notifikasi::where('user_id', $user->id)->where('dibaca', false)->count();
            $readCount = Notifikasi::where('user_id', $user->id)->where('dibaca', true)->count();

            return response()->json([
                'success' => true,
                'data' => $notifications->items(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'has_more' => $notifications->hasMorePages()
                ],
                'counts' => [
                    'total' => $notifications->total(),
                    'unread' => $unreadCount,
                    'read' => $readCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi',
                'error' => $e->getMessage()
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
            $limit = $request->get('limit', 10);
            
            $notifications = Notifikasi::where('user_id', $user->id)
                                      ->where('dibaca', false)
                                      ->orderBy('created_at', 'desc')
                                      ->limit($limit)
                                      ->get();

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi belum dibaca',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread count
     */
    public function getUnreadCount()
    {
        try {
            $user = Auth::user();
            $count = Notifikasi::where('user_id', $user->id)
                               ->where('dibaca', false)
                               ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat jumlah notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notifikasi $notification)
    {
        try {
            $user = Auth::user();
            
            if ($notification->user_id !== $user->id) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $notification->update(['dibaca' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca',
                'data' => $notification
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            
            $updated = Notifikasi::where('user_id', $user->id)
                                 ->where('dibaca', false)
                                 ->update(['dibaca' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sudah dibaca',
                'updated_count' => $updated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete notification
     */
    public function destroy(Notifikasi $notification)
    {
        try {
            $user = Auth::user();
            
            if ($notification->user_id !== $user->id) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all notifications
     */
    public function destroyAll()
    {
        try {
            $user = Auth::user();
            
            $deleted = Notifikasi::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil dihapus',
                'deleted_count' => $deleted
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus semua notifikasi',
                'error' => $e->getMessage()
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
            $limit = $request->get('limit', 10);
            
            $notifications = Notifikasi::where('user_id', $user->id)
                                      ->orderBy('created_at', 'desc')
                                      ->limit($limit)
                                      ->get()
                                      ->map(function($item) {
                                          return [
                                              'id' => $item->id,
                                              'judul' => $item->judul,
                                              'pesan' => $item->pesan,
                                              'tipe' => $item->tipe,
                                              'kategori' => $item->kategori,
                                              'dibaca' => $item->dibaca,
                                              'created_at' => $item->created_at,
                                              'created_at_formatted' => $item->created_at_formatted,
                                              'tipe_icon' => $item->tipe_icon,
                                              'tipe_badge' => $item->tipe_badge,
                                              'data' => $item->data
                                          ];
                                      });

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi terbaru',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification (for RT/RW/Kades/Admin)
     */
    public function sendNotification(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'judul' => 'required|string|max:255',
                'pesan' => 'required|string|max:1000',
                'tipe' => 'required|in:info,success,warning,error',
                'kategori' => 'required|string|max:50',
                'data' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $notifications = [];
            foreach ($request->user_ids as $userId) {
                $notifications[] = [
                    'user_id' => $userId,
                    'judul' => $request->judul,
                    'pesan' => $request->pesan,
                    'tipe' => $request->tipe,
                    'kategori' => $request->kategori,
                    'data' => $request->data ?? [],
                    'dibaca' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            Notifikasi::insert($notifications);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dikirim',
                'sent_count' => count($notifications)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
