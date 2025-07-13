<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Notifikasi::where('user_id', $user->id);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('dibaca', false);
            } elseif ($request->status === 'read') {
                $query->where('dibaca', true);
            }
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $notifikasi = $query->orderBy('created_at', 'desc')->paginate(20);

        // Mark notifications as read when viewed
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $notifikasi->items(),
                'pagination' => [
                    'total' => $notifikasi->total(),
                    'per_page' => $notifikasi->perPage(),
                    'current_page' => $notifikasi->currentPage(),
                    'last_page' => $notifikasi->lastPage(),
                    'has_more' => $notifikasi->hasMorePages(),
                ]
            ]);
        }

        return view('notifikasi.index', compact('notifikasi'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notifikasi $notifikasi)
    {
        $user = Auth::user();
        
        if ($notifikasi->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $notifikasi->update(['dibaca' => true]);

        return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        Notifikasi::where('user_id', $user->id)
                  ->where('dibaca', false)
                  ->update(['dibaca' => true]);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }

    /**
     * Delete notification
     */
    public function destroy(Notifikasi $notifikasi)
    {
        $user = Auth::user();
        
        if ($notifikasi->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $notifikasi->delete();

        return response()->json(['success' => true, 'message' => 'Notifikasi berhasil dihapus.']);
    }

    /**
     * Delete all notifications
     */
    public function destroyAll()
    {
        $user = Auth::user();
        
        Notifikasi::where('user_id', $user->id)->delete();

        return response()->json(['success' => true, 'message' => 'Semua notifikasi berhasil dihapus.']);
    }

    /**
     * Get recent notifications (for AJAX)
     */
    public function getRecent(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);
        
        $notifikasi = Notifikasi::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc')
                                ->limit($limit)
                                ->get();

        return response()->json([
            'success' => true,
            'data' => $notifikasi->map(function($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'pesan' => $item->pesan,
                    'tipe' => $item->tipe,
                    'kategori' => $item->kategori,
                    'dibaca' => $item->dibaca,
                    'created_at' => $item->created_at_formatted,
                    'tipe_icon' => $item->tipe_icon,
                    'tipe_badge' => $item->tipe_badge,
                ];
            })
        ]);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $count = Notifikasi::where('user_id', $user->id)
                           ->where('dibaca', false)
                           ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
