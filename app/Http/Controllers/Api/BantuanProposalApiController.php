<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BantuanProposal;
use App\Models\Rw;
use Carbon\Carbon;

class BantuanProposalApiController extends Controller
{
    /**
     * Get proposal statistics
     */
    public function getStats(Request $request)
    {
        try {
            $user = Auth::user();
            $query = BantuanProposal::query();

            // Apply role-based filtering
            switch ($user->role) {
                case 'rw':
                    $rw = $this->getUserRw($user);
                    if ($rw) {
                        $query->where('rw_id', $rw->id);
                    }
                    break;
                case 'kades':
                case 'admin':
                    // Can see all proposals
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan'
                    ], 403);
            }

            $stats = [
                'total' => $query->count(),
                'pending' => $query->clone()->where('status', 'pending')->count(),
                'approved' => $query->clone()->where('status', 'approved')->count(),
                'rejected' => $query->clone()->where('status', 'rejected')->count(),
                'total_amount_requested' => $query->sum('jumlah_bantuan'),
                'total_amount_approved' => $query->clone()->where('status', 'approved')->sum('jumlah_bantuan'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting proposal stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik proposal'
            ], 500);
        }
    }

    /**
     * Get recent proposals
     */
    public function getRecentProposals(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 10);
            
            $query = BantuanProposal::with(['rw'])
                ->orderBy('created_at', 'desc')
                ->limit($limit);

            // Apply role-based filtering
            switch ($user->role) {
                case 'rw':
                    $rw = $this->getUserRw($user);
                    if ($rw) {
                        $query->where('rw_id', $rw->id);
                    }
                    break;
                case 'kades':
                case 'admin':
                    // Can see all proposals
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan'
                    ], 403);
            }

            $proposals = $query->get()->map(function ($proposal) {
                return [
                    'id' => $proposal->id,
                    'judul' => $proposal->judul,
                    'status' => $proposal->status,
                    'kategori' => $proposal->kategori,
                    'jumlah_bantuan' => $proposal->jumlah_bantuan,
                    'rw_name' => $proposal->rw->nama_rw ?? 'RW ' . $proposal->rw->no_rw,
                    'created_at' => $proposal->created_at,
                    'status_text' => $this->getStatusText($proposal->status),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $proposals
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recent proposals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat proposal terbaru'
            ], 500);
        }
    }

    /**
     * Get proposal analytics
     */
    public function getAnalytics(Request $request)
    {
        try {
            $user = Auth::user();
            $query = BantuanProposal::query();

            // Apply role-based filtering
            switch ($user->role) {
                case 'rw':
                    $rw = $this->getUserRw($user);
                    if ($rw) {
                        $query->where('rw_id', $rw->id);
                    }
                    break;
                case 'kades':
                case 'admin':
                    // Can see all proposals
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan'
                    ], 403);
            }

            // Monthly proposals data
            $monthlyData = $query->clone()
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(jumlah_bantuan) as total_amount')
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $monthlyLabels = [];
            $monthlyCounts = [];
            $monthlyAmounts = [];

            for ($i = 1; $i <= 12; $i++) {
                $monthName = Carbon::create()->month($i)->translatedFormat('F');
                $monthlyLabels[] = $monthName;
                
                $monthData = $monthlyData->firstWhere('month', $i);
                $monthlyCounts[] = $monthData ? $monthData->count : 0;
                $monthlyAmounts[] = $monthData ? $monthData->total_amount : 0;
            }

            // Category distribution
            $categoryData = $query->clone()
                ->selectRaw('kategori, COUNT(*) as count, SUM(jumlah_bantuan) as total_amount')
                ->groupBy('kategori')
                ->get();

            // Status distribution
            $statusData = $query->clone()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'monthly' => [
                        'labels' => $monthlyLabels,
                        'counts' => $monthlyCounts,
                        'amounts' => $monthlyAmounts,
                    ],
                    'categories' => $categoryData->map(function ($item) {
                        return [
                            'kategori' => $item->kategori,
                            'count' => $item->count,
                            'total_amount' => $item->total_amount,
                        ];
                    }),
                    'status' => $statusData->map(function ($item) {
                        return [
                            'status' => $item->status,
                            'status_text' => $this->getStatusText($item->status),
                            'count' => $item->count,
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting proposal analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat analitik proposal'
            ], 500);
        }
    }

    /**
     * Get all proposals with filtering
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = BantuanProposal::with(['rw']);

            // Apply role-based filtering
            switch ($user->role) {
                case 'rw':
                    $rw = $this->getUserRw($user);
                    if ($rw) {
                        $query->where('rw_id', $rw->id);
                    }
                    break;
                case 'kades':
                case 'admin':
                    // Can see all proposals
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan'
                    ], 403);
            }

            // Apply filters
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('kategori') && $request->kategori) {
                $query->where('kategori', $request->kategori);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            $proposals = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            $transformedProposals = $proposals->getCollection()->map(function ($proposal) {
                return [
                    'id' => $proposal->id,
                    'judul' => $proposal->judul,
                    'deskripsi' => $proposal->deskripsi,
                    'kategori' => $proposal->kategori,
                    'jumlah_bantuan' => $proposal->jumlah_bantuan,
                    'status' => $proposal->status,
                    'rw_name' => $proposal->rw->nama_rw ?? 'RW ' . $proposal->rw->no_rw,
                    'file_pendukung' => $proposal->file_pendukung,
                    'file_pendukung_url' => $proposal->file_pendukung ? asset('storage/' . $proposal->file_pendukung) : null,
                    'catatan_kades' => $proposal->catatan_kades,
                    'created_at' => $proposal->created_at,
                    'processed_at' => $proposal->processed_at,
                    'status_text' => $this->getStatusText($proposal->status),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedProposals,
                'pagination' => [
                    'current_page' => $proposals->currentPage(),
                    'last_page' => $proposals->lastPage(),
                    'per_page' => $proposals->perPage(),
                    'total' => $proposals->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting proposals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar proposal'
            ], 500);
        }
    }

    /**
     * Store a new proposal
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'rw') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya RW yang dapat mengajukan proposal'
                ], 403);
            }

            $rw = $this->getUserRw($user);
            if (!$rw) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data RW tidak ditemukan'
                ], 404);
            }

            $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'kategori' => 'required|in:infrastruktur,sosial,ekonomi,pendidikan,kesehatan,lainnya',
                'jumlah_bantuan' => 'required|integer|min:1000',
                'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
            ]);

            DB::beginTransaction();

            $proposalData = [
                'rw_id' => $rw->id,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori,
                'jumlah_bantuan' => $request->jumlah_bantuan,
                'status' => 'pending',
            ];

            // Handle file upload
            if ($request->hasFile('file_pendukung')) {
                $file = $request->file('file_pendukung');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('bantuan-proposals', $filename, 'public');
                $proposalData['file_pendukung'] = $path;
            }

            $proposal = BantuanProposal::create($proposalData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proposal bantuan berhasil diajukan',
                'data' => $proposal
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating proposal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan proposal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific proposal
     */
    public function show(BantuanProposal $proposal)
    {
        try {
            $user = Auth::user();

            // Check access permissions
            if ($user->role === 'rw') {
                $rw = $this->getUserRw($user);
                if (!$rw || $proposal->rw_id !== $rw->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan'
                    ], 403);
                }
            }

            $proposal->load('rw');

            $proposalData = [
                'id' => $proposal->id,
                'judul' => $proposal->judul,
                'deskripsi' => $proposal->deskripsi,
                'kategori' => $proposal->kategori,
                'jumlah_bantuan' => $proposal->jumlah_bantuan,
                'status' => $proposal->status,
                'rw_name' => $proposal->rw->nama_rw ?? 'RW ' . $proposal->rw->no_rw,
                'file_pendukung' => $proposal->file_pendukung,
                'file_pendukung_url' => $proposal->file_pendukung ? asset('storage/' . $proposal->file_pendukung) : null,
                'catatan_kades' => $proposal->catatan_kades,
                'created_at' => $proposal->created_at,
                'processed_at' => $proposal->processed_at,
                'status_text' => $this->getStatusText($proposal->status),
            ];

            return response()->json([
                'success' => true,
                'data' => $proposalData
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing proposal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail proposal'
            ], 500);
        }
    }

    /**
     * Update proposal status (approve/reject)
     */
    public function updateStatus(Request $request, BantuanProposal $proposal)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'kades' && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Kepala Desa yang dapat memproses proposal'
                ], 403);
            }

            $request->validate([
                'status' => 'required|in:approved,rejected',
                'catatan_kades' => 'required|string|max:1000',
            ]);

            $proposal->update([
                'status' => $request->status,
                'catatan_kades' => $request->catatan_kades,
                'processed_at' => now(),
                'processed_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status proposal berhasil diperbarui',
                'data' => $proposal
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating proposal status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status proposal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's RW
     */
    private function getUserRw($user)
    {
        try {
            if ($user->penduduk && $user->penduduk->rwKetua) {
                return $user->penduduk->rwKetua;
            }
            
            if ($user->penduduk) {
                return Rw::where('ketua_rw_id', $user->penduduk->id)->first();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting user RW: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get status text
     */
    private function getStatusText($status)
    {
        $statusMap = [
            'pending' => 'Menunggu Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak'
        ];

        return $statusMap[$status] ?? $status;
    }
}
