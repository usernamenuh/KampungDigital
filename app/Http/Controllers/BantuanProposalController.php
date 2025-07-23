<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\BantuanProposal;
use App\Models\Rw;
use App\Models\Desa;
use App\Models\SaldoTransaction;
use App\Models\Notifikasi;
use Carbon\Carbon;

class BantuanProposalController extends Controller
{
    /**
     * Display a listing of the proposals for RW
     */
    public function indexRw()
    {
        try {
            $user = Auth::user();
            
            // Get RW data
            $rw = null;
            if ($user->penduduk) {
                $rw = Rw::where('ketua_rw_id', $user->penduduk->id)->first();
            }
            
            if (!$rw) {
                return redirect()->route('dashboard.rw')
                    ->with('error', 'Data RW tidak ditemukan.');
            }
            
            // Get proposals for this RW
            $proposals = BantuanProposal::where('rw_id', $rw->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('bantuan-proposals.index-rw', compact('proposals', 'rw'));
            
        } catch (\Exception $e) {
            Log::error('Error in BantuanProposalController@indexRw', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('dashboard.rw')
                ->with('error', 'Terjadi kesalahan saat memuat data proposal bantuan.');
        }
    }
    
    /**
     * Display a listing of the proposals for Kades
     */
    public function indexKades()
    {
        try {
            $status = request('status', 'all');
            
            // Get proposals query
            $query = BantuanProposal::with('rw');
            
            // Filter by status if provided
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            
            // Get paginated results
            $proposals = $query->orderBy('created_at', 'desc')
                ->paginate(10);
            
            // Get counts for dashboard
            $stats = [
                'total' => BantuanProposal::count(),
                'pending' => BantuanProposal::where('status', 'pending')->count(),
                'approved' => BantuanProposal::where('status', 'approved')->count(),
                'rejected' => BantuanProposal::where('status', 'rejected')->count(),
                'total_amount_requested' => BantuanProposal::sum('jumlah_bantuan'),
                'total_amount_approved' => BantuanProposal::where('status', 'approved')->sum('jumlah_disetujui'),
            ];
            
            return view('bantuan-proposals.index-kades', compact('proposals', 'status', 'stats'));
            
        } catch (\Exception $e) {
            Log::error('Error in BantuanProposalController@indexKades', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('dashboard.kades')
                ->with('error', 'Terjadi kesalahan saat memuat data proposal bantuan.');
        }
    }

    /**
     * Show the form for creating a new proposal.
     */
    public function create()
    {
        try {
            $user = Auth::user();
            
            // Get RW data
            $rw = null;
            if ($user->penduduk) {
                $rw = Rw::where('ketua_rw_id', $user->penduduk->id)->first();
            }
            
            if (!$rw) {
                return redirect()->route('dashboard.rw')
                    ->with('error', 'Data RW tidak ditemukan.');
            }
            
            return view('bantuan-proposals.create', compact('rw'));
            
        } catch (\Exception $e) {
            Log::error('Error in BantuanProposalController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('dashboard.rw')
                ->with('error', 'Terjadi kesalahan saat memuat form proposal bantuan.');
        }
    }

    /**
     * Store a newly created proposal in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Validate request - SESUAI DENGAN NAMA FIELD DI FORM DAN DATABASE
            $validated = $request->validate([
                'judul_proposal' => 'required|string|max:255',
                'deskripsi' => 'required|string|min:50',
                'jumlah_bantuan' => 'required|numeric|min:1000',
                'file_proposal' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            ]);
            
            // Get RW data
            $rw = null;
            if ($user->penduduk) {
                $rw = Rw::where('ketua_rw_id', $user->penduduk->id)->first();
            }
            
            if (!$rw) {
                return redirect()->route('dashboard.rw')
                    ->with('error', 'Data RW tidak ditemukan.');
            }
            
            // Handle file upload if provided
            $filePath = null;
            if ($request->hasFile('file_proposal')) {
                $file = $request->file('file_proposal');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('proposals', $fileName, 'public');
            }
            
            // Create proposal - MENGGUNAKAN NAMA FIELD YANG SESUAI DATABASE
            $proposal = BantuanProposal::create([
                'rw_id' => $rw->id,
                'submitted_by' => $user->id,
                'judul_proposal' => $validated['judul_proposal'],
                'deskripsi' => $validated['deskripsi'],
                'jumlah_bantuan' => $validated['jumlah_bantuan'],
                'file_proposal' => $filePath,
                'status' => 'pending',
            ]);
            
            // Create notification for Kades
            $kadesUsers = \App\Models\User::where('role', 'kades')->get();
            foreach ($kadesUsers as $kades) {
                Notifikasi::create([
                    'user_id' => $kades->id,
                    'judul' => 'Proposal Bantuan Baru',
                    'pesan' => "RW {$rw->nama} mengajukan proposal bantuan: {$validated['judul_proposal']}",
                    'kategori' => 'proposal',
                    'data' => json_encode([
                        'proposal_id' => $proposal->id,
                        'rw_id' => $rw->id,
                        'rw_nama' => $rw->nama,
                        'jumlah' => $validated['jumlah_bantuan']
                    ]),
                    'dibaca' => false,
                ]);
            }
            
            return redirect()->route('bantuan-proposals.index')
                ->with('success', 'Proposal bantuan berhasil diajukan.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Error in BantuanProposalController@store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->route('bantuan-proposals.create')
                ->with('error', 'Terjadi kesalahan saat menyimpan proposal bantuan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified proposal.
     */
    public function show(BantuanProposal $proposal)
    {
        try {
            $user = Auth::user();
            
            // Check if user has permission to view this proposal
            if ($user->role === 'rw') {
                // RW can only view their own proposals
                $rw = null;
                if ($user->penduduk) {
                    $rw = Rw::where('ketua_rw_id', $user->penduduk->id)->first();
                }
                
                if (!$rw || $proposal->rw_id !== $rw->id) {
                    return redirect()->route('bantuan-proposals.index')
                        ->with('error', 'Anda tidak memiliki akses untuk melihat proposal ini.');
                }
            } elseif (!in_array($user->role, ['kades', 'admin'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk melihat proposal ini.');
            }
            
            // Load related data
            $proposal->load('rw', 'submittedBy', 'reviewedBy');
            
            return view('bantuan-proposals.show', compact('proposal'));
            
        } catch (\Exception $e) {
            Log::error('Error in BantuanProposalController@show', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'proposal_id' => $proposal->id ?? null
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat detail proposal bantuan.');
        }
    }

    /**
     * Show the form for processing a proposal (approve/reject).
     */
    public function process(BantuanProposal $proposal)
    {
        try {
            $user = Auth::user();
            
            // Only kades and admin can process proposals
            if (!in_array($user->role, ['kades', 'admin'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk memproses proposal ini.');
            }
            
            // Load related data
            $proposal->load('rw', 'submittedBy');
            
            // Get desa data for checking saldo
            $desa = Desa::first();
            $saldoDesa = $desa ? $desa->saldo : 0;
            
            return view('bantuan-proposals.process', compact('proposal', 'saldoDesa'));
            
        } catch (\Exception $e) {
            Log::error('Error in BantuanProposalController@process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'proposal_id' => $proposal->id ?? null
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat form proses proposal bantuan.');
        }
    }

    /**
     * Update the status of a proposal (approve/reject).
     */
    public function updateStatus(Request $request, BantuanProposal $proposal)
    {
        try {
            $user = Auth::user();
            
            // Only kades and admin can update proposal status
            if (!in_array($user->role, ['kades', 'admin'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk memproses proposal ini.');
            }
            
            // Validate request
            $validated = $request->validate([
                'status' => 'required|in:approved,rejected',
                'catatan_review' => 'nullable|string',
                'jumlah_disetujui' => 'nullable|numeric|min:0',
            ]);
            
            // Begin transaction
            DB::beginTransaction();
            
            try {
                // Update proposal status
                $proposal->update([
                    'status' => $validated['status'],
                    'catatan_review' => $validated['catatan_review'],
                    'jumlah_disetujui' => $validated['jumlah_disetujui'] ?? $proposal->jumlah_bantuan,
                    'reviewed_at' => Carbon::now(),
                    'reviewed_by' => $user->id,
                ]);
                
                // If approved, transfer funds from desa to rw
                if ($validated['status'] === 'approved') {
                    $jumlahDisetujui = $validated['jumlah_disetujui'] ?? $proposal->jumlah_bantuan;
                    
                    // Get desa data
                    $desa = Desa::first();
                    if (!$desa) {
                        throw new \Exception('Data desa tidak ditemukan.');
                    }
                    
                    // Check if desa has enough saldo
                    if ($desa->saldo < $jumlahDisetujui) {
                        throw new \Exception('Saldo desa tidak mencukupi untuk menyetujui proposal ini.');
                    }
                    
                    // Get RW data
                    $rw = Rw::find($proposal->rw_id);
                    if (!$rw) {
                        throw new \Exception('Data RW tidak ditemukan.');
                    }
                    
                    // Create transaction record for desa (expense)
                    SaldoTransaction::create([
                        'desa_id' => $desa->id,
                        'transaction_type' => 'expense',
                        'amount' => $jumlahDisetujui,
                        'description' => "Bantuan untuk RW {$rw->nama}: {$proposal->judul_proposal}",
                        'transaction_date' => Carbon::now(),
                        'created_by' => $user->id,
                    ]);
                    
                    // Create transaction record for RW (income)
                    SaldoTransaction::create([
                        'rw_id' => $rw->id,
                        'transaction_type' => 'income',
                        'amount' => $jumlahDisetujui,
                        'description' => "Bantuan dari Desa: {$proposal->judul_proposal}",
                        'transaction_date' => Carbon::now(),
                        'created_by' => $user->id,
                    ]);
                    
                    // Update saldo
                    $desa->decrement('saldo', $jumlahDisetujui);
                    $rw->increment('saldo', $jumlahDisetujui);
                    
                    // Update tanggal pencairan
                    $proposal->update(['tanggal_pencairan' => Carbon::now()]);
                }
                
                // Create notification for RW
                $rwUser = $proposal->submittedBy;
                if ($rwUser) {
                    $message = $validated['status'] === 'approved' 
                        ? "Proposal bantuan '{$proposal->judul_proposal}' telah disetujui oleh Kepala Desa."
                        : "Proposal bantuan '{$proposal->judul_proposal}' ditolak oleh Kepala Desa.";
                        
                    Notifikasi::create([
                        'user_id' => $rwUser->id,
                        'judul' => 'Status Proposal Bantuan Diperbarui',
                        'pesan' => $message,
                        'kategori' => 'proposal',
                        'data' => json_encode([
                            'proposal_id' => $proposal->id,
                            'status' => $validated['status'],
                            'catatan' => $validated['catatan_review']
                        ]),
                        'dibaca' => false,
                    ]);
                }
                
                DB::commit();
                
                $statusText = $validated['status'] === 'approved' ? 'disetujui' : 'ditolak';
                return redirect()->route('bantuan-proposals.kades.index')
                    ->with('success', "Proposal bantuan berhasil {$statusText}.");
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Error in BantuanProposalController@updateStatus', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'proposal_id' => $proposal->id ?? null
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses proposal bantuan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download proposal file - METHOD YANG DITAMBAHKAN
     */
    public function downloadFile(BantuanProposal $proposal)
    {
        try {
            // Check if user has permission to download this file
            $user = Auth::user();
            
            if ($user->role === 'rw') {
                // RW can only download their own proposal files
                $rw = null;
                if ($user->penduduk) {
                    $rw = Rw::where('ketua_rw_id', $user->penduduk->id)->first();
                }
                
                if (!$rw || $proposal->rw_id !== $rw->id) {
                    return redirect()->back()
                        ->with('error', 'Anda tidak memiliki akses untuk mengunduh file ini.');
                }
            } elseif (!in_array($user->role, ['kades', 'admin'])) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki akses untuk mengunduh file ini.');
            }

            // Check if file exists in proposal
            if (!$proposal->file_proposal) {
                return redirect()->back()
                    ->with('error', 'File proposal tidak tersedia untuk diunduh.');
            }

            // Get full file path
            $filePath = storage_path('app/public/' . $proposal->file_proposal);
            
            // Check if file exists on disk
            if (!file_exists($filePath)) {
                Log::error('Proposal file not found on disk', [
                    'proposal_id' => $proposal->id,
                    'file_path' => $filePath,
                    'stored_path' => $proposal->file_proposal
                ]);
                
                return redirect()->back()
                    ->with('error', 'File proposal tidak ditemukan di server. Silakan hubungi administrator.');
            }

            // Generate download filename
            $originalName = pathinfo($proposal->file_proposal, PATHINFO_BASENAME);
            $extension = pathinfo($proposal->file_proposal, PATHINFO_EXTENSION);
            $downloadName = 'Proposal_' . str_replace(' ', '_', $proposal->judul_proposal) . '_' . $proposal->id . '.' . $extension;

            // Log download activity
            Log::info('Proposal file downloaded', [
                'proposal_id' => $proposal->id,
                'user_id' => $user->id,
                'user_role' => $user->role,
                'file_path' => $proposal->file_proposal,
                'download_name' => $downloadName
            ]);

            // Return file download response
            return response()->download($filePath, $downloadName, [
                'Content-Type' => mime_content_type($filePath),
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Error downloading proposal file', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'proposal_id' => $proposal->id ?? null,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengunduh file proposal: ' . $e->getMessage());
        }
    }
}
