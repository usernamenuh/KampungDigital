<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Rw;
use App\Models\Rt;
use App\Http\Requests\BeritaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Berita::with(['user', 'rt', 'rw']);
        
        // Apply access level filtering based on user role
        if ($user->role === 'masyarakat') {
            $query->where(function ($q) use ($user) {
                $q->where('tingkat_akses', 'desa')
                  ->orWhere(function ($subQ) use ($user) {
                      $subQ->where('tingkat_akses', 'rw')
                           ->where('rw_id', $user->rw_id);
                  })
                  ->orWhere(function ($subQ) use ($user) {
                      $subQ->where('tingkat_akses', 'rt')
                           ->where('rt_id', $user->rt_id);
                  });
            })->where('status', 'published');
        } elseif ($user->role === 'rt') {
            $query->where(function ($q) use ($user) {
                $q->where('tingkat_akses', 'desa')
                  ->orWhere(function ($subQ) use ($user) {
                      $subQ->where('tingkat_akses', 'rw')
                           ->where('rw_id', optional($user->rt)->rw_id);
                  })
                  ->orWhere(function ($subQ) use ($user) {
                      $subQ->where('tingkat_akses', 'rt')
                           ->where('rt_id', $user->rt_id);
                  })
                  ->orWhere('user_id', $user->id);
            });
        } elseif ($user->role === 'rw') {
            $query->where(function ($q) use ($user) {
                $q->where('tingkat_akses', 'desa')
                  ->orWhere(function ($subQ) use ($user) {
                      $subQ->where('tingkat_akses', 'rw')
                           ->where('rw_id', $user->rw_id);
                  })
                  ->orWhereHas('rt', function ($subQ) use ($user) {
                      $subQ->where('rw_id', $user->rw_id);
                  })
                  ->orWhere('user_id', $user->id);
            });
        }
        // Admin and Kades can see all news
        
        // Apply filters
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('tingkat_akses')) {
            $query->where('tingkat_akses', $request->tingkat_akses);
        }
        
        // Order by pinned first, then by created date
        $query->orderBy('is_pinned', 'desc')
              ->orderBy('created_at', 'desc');
        
        $berita = $query->paginate(9); // Changed to 9 for better card grid layout
        
        // Calculate statistics for authorized users
        $stats = [];
        if (in_array($user->role, ['admin', 'kades', 'rw', 'rt'])) {
            $statsQuery = Berita::query();
            
            // Apply same access filtering for stats
            if ($user->role === 'rt') {
                $statsQuery->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere(function ($subQ) use ($user) {
                          $subQ->where('tingkat_akses', 'rt')
                               ->where('rt_id', $user->rt_id);
                      });
                });
            } elseif ($user->role === 'rw') {
                $statsQuery->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere(function ($subQ) use ($user) {
                          $subQ->where('tingkat_akses', 'rw')
                               ->where('rw_id', $user->rw_id);
                      })
                      ->orWhereHas('rt', function ($subQ) use ($user) {
                          $subQ->where('rw_id', $user->rw_id);
                      });
                });
            }
            
            $stats = [
                'total' => $statsQuery->count(),
                'published' => $statsQuery->where('status', 'published')->count(),
                'draft' => $statsQuery->where('status', 'draft')->count(),
                'total_views' => $statsQuery->sum('views'),
            ];
        }
        
        return view('berita.index', compact('berita', 'stats'));
    }
    
    public function create()
    {
        $user = Auth::user();
        
        // Get RWs and RTs based on user role
        $rws = collect();
        $rts = collect();
        
        if (in_array($user->role, ['admin', 'kades'])) {
            $rws = Rw::orderBy('no_rw')->get();
            $rts = Rt::with('rw')->orderBy('no_rt')->get();
        } elseif ($user->role === 'rw') {
            $rws = Rw::where('id', $user->rw_id)->get();
            $rts = Rt::where('rw_id', $user->rw_id)->orderBy('no_rt')->get();
        } elseif ($user->role === 'rt') {
            $rws = Rw::where('id', optional($user->rt)->rw_id)->get();
            $rts = Rt::where('id', $user->rt_id)->get();
        }
        
        return view('berita.create', compact('rws', 'rts'));
    }
    
    public function store(BeritaRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['judul']);
        
        // Handle tags
        if (!empty($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }
        
        // Handle file uploads
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('berita/images', 'public');
        }
        
        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('berita/videos', 'public');
        }
        
        // Set publish date if status is published
        if ($data['status'] === 'published') {
            $data['tanggal_publish'] = now();
        }
        
        // Generate excerpt if not provided
        if (empty($data['excerpt'])) {
            $data['excerpt'] = Str::limit(strip_tags($data['konten']), 200);
        }
        
        Berita::create($data);
        
        return redirect()->route('berita.index')
                        ->with('success', 'Berita berhasil dibuat.');
    }
    
    public function show(Berita $berita)
    {
        $user = Auth::user();
        
        // Simple permission check
        $canView = false;
        
        if (in_array($user->role, ['admin', 'kades'])) {
            $canView = true;
        } elseif ($user->role === 'rw') {
            $canView = ($berita->tingkat_akses === 'desa') || 
                      ($berita->tingkat_akses === 'rw' && $berita->rw_id == $user->rw_id) ||
                      ($berita->user_id == $user->id);
        } elseif ($user->role === 'rt') {
            $canView = ($berita->tingkat_akses === 'desa') || 
                      ($berita->tingkat_akses === 'rw' && $berita->rw_id == optional($user->rt)->rw_id) ||
                      ($berita->tingkat_akses === 'rt' && $berita->rt_id == $user->rt_id) ||
                      ($berita->user_id == $user->id);
        } elseif ($user->role === 'masyarakat') {
            $canView = ($berita->tingkat_akses === 'desa' && $berita->status === 'published') || 
                      ($berita->tingkat_akses === 'rw' && $berita->rw_id == $user->rw_id && $berita->status === 'published') ||
                      ($berita->tingkat_akses === 'rt' && $berita->rt_id == $user->rt_id && $berita->status === 'published');
        }
        
        if (!$canView) {
            abort(403, 'Anda tidak memiliki akses untuk melihat berita ini.');
        }
        
        // Increment views
        $berita->increment('views');
        
        // Get related news
        $relatedBerita = Berita::where('id', '!=', $berita->id)
                              ->where('kategori', $berita->kategori)
                              ->where('status', 'published')
                              ->limit(5)
                              ->get();
        
        return view('berita.show', compact('berita', 'relatedBerita'));
    }
    
    public function edit(Berita $berita)
    {
        $user = Auth::user();
        
        // Simple permission check for edit
        $canEdit = false;
        
        if ($user->role === 'admin') {
            $canEdit = true;
        } elseif ($berita->user_id === $user->id) {
            $canEdit = true;
        } elseif ($user->role === 'kades') {
            $canEdit = true;
        }
        
        if (!$canEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit berita ini.');
        }
        
        // Get RWs and RTs based on user role
        $rws = collect();
        $rts = collect();
        
        if (in_array($user->role, ['admin', 'kades'])) {
            $rws = Rw::orderBy('no_rw')->get();
            $rts = Rt::with('rw')->orderBy('no_rt')->get();
        } elseif ($user->role === 'rw') {
            $rws = Rw::where('id', $user->rw_id)->get();
            $rts = Rt::where('rw_id', $user->rw_id)->orderBy('no_rt')->get();
        } elseif ($user->role === 'rt') {
            $rws = Rw::where('id', optional($user->rt)->rw_id)->get();
            $rts = Rt::where('id', $user->rt_id)->get();
        }
        
        return view('berita.edit', compact('berita', 'rws', 'rts'));
    }
    
    public function update(BeritaRequest $request, Berita $berita)
    {
        try {
            $user = Auth::user();
            
            // Simple permission check for update
            $canEdit = false;
            
            if ($user->role === 'admin') {
                $canEdit = true;
            } elseif ($berita->user_id === $user->id) {
                $canEdit = true;
            } elseif ($user->role === 'kades') {
                $canEdit = true;
            }
            
            if (!$canEdit) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit berita ini.');
            }
            
            $data = $request->validated();
            
            // Log the incoming data for debugging
            Log::info('Berita update data:', $data);
            
            $data['slug'] = Str::slug($data['judul']);
            
            // Handle tags
            if (!empty($data['tags'])) {
                $data['tags'] = array_map('trim', explode(',', $data['tags']));
            } else {
                $data['tags'] = null;
            }
            
            // Handle file uploads
            if ($request->hasFile('gambar')) {
                // Delete old image
                if ($berita->gambar) {
                    Storage::disk('public')->delete($berita->gambar);
                }
                $data['gambar'] = $request->file('gambar')->store('berita/images', 'public');
            }
            
            if ($request->hasFile('video')) {
                // Delete old video
                if ($berita->video) {
                    Storage::disk('public')->delete($berita->video);
                }
                $data['video'] = $request->file('video')->store('berita/videos', 'public');
            }
            
            // Set publish date if status changed to published
            if ($data['status'] === 'published' && $berita->status !== 'published') {
                $data['tanggal_publish'] = now();
            }
            
            // Generate excerpt if not provided
            if (empty($data['excerpt'])) {
                $data['excerpt'] = Str::limit(strip_tags($data['konten']), 200);
            }
            
            // Handle checkbox values properly
            $data['is_pinned'] = $request->has('is_pinned') ? 1 : 0;
            
            // Clear rw_id and rt_id based on tingkat_akses
            if ($data['tingkat_akses'] === 'desa') {
                $data['rw_id'] = null;
                $data['rt_id'] = null;
            } elseif ($data['tingkat_akses'] === 'rw') {
                $data['rt_id'] = null;
            }
            
            $updated = $berita->update($data);
            
            Log::info('Berita update result:', ['updated' => $updated, 'berita_id' => $berita->id]);
            
            if ($updated) {
                return redirect()->route('berita.index')
                                ->with('success', 'Berita berhasil diperbarui.');
            } else {
                return redirect()->back()
                                ->with('error', 'Gagal memperbarui berita.')
                                ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error updating berita:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return redirect()->back()
                            ->with('error', 'Terjadi kesalahan saat memperbarui berita: ' . $e->getMessage())
                            ->withInput();
        }
    }
    
    public function destroy(Berita $berita)
    {
        $user = Auth::user();
        
        // Simple permission check for delete
        $canDelete = false;
        
        if ($user->role === 'admin') {
            $canDelete = true;
        } elseif ($berita->user_id === $user->id) {
            $canDelete = true;
        } elseif ($user->role === 'kades') {
            $canDelete = true;
        }
        
        if (!$canDelete) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus berita ini.');
        }
        
        // Delete associated files
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }
        
        if ($berita->video) {
            Storage::disk('public')->delete($berita->video);
        }
        
        $berita->delete();
        
        return redirect()->route('berita.index')
                        ->with('success', 'Berita berhasil dihapus.');
    }
    
    public function togglePin(Berita $berita)
    {
        $user = Auth::user();
        
        // Simple permission check for pin
        $canEdit = false;
        
        if ($user->role === 'admin') {
            $canEdit = true;
        } elseif ($berita->user_id === $user->id) {
            $canEdit = true;
        } elseif ($user->role === 'kades') {
            $canEdit = true;
        }
        
        if (!$canEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status pin berita ini.');
        }
        
        $berita->update(['is_pinned' => !$berita->is_pinned]);
        
        $message = $berita->is_pinned ? 'Berita berhasil di-pin.' : 'Berita berhasil di-unpin.';
        
        return redirect()->back()->with('success', $message);
    }
}
