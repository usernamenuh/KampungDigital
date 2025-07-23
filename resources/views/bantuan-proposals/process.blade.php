@extends('layouts.app')

@section('title', 'Review Proposal Bantuan')

@section('content')
<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
   <div class="max-w-7xl mx-auto">
       <!-- Header Section -->
       <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
           <div>
               <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                   <i data-lucide="gavel" class="w-8 h-8 text-primary-500 mr-3"></i>
                   Review Proposal Bantuan
               </h1>
               <p class="text-gray-600 dark:text-gray-400 mt-1">
                   Tinjau dan berikan keputusan untuk proposal bantuan
               </p>
           </div>
           <a href="{{ route('bantuan-proposals.kades.index') }}" 
              class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
               <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
               Kembali
           </a>
       </div>

       <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
           <!-- Proposal Details -->
           <div class="lg:col-span-2">
               <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                   <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                       <h2 class="text-xl font-semibold flex items-center">
                           <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
                           Detail Proposal
                       </h2>
                   </div>
                   <div class="p-6 space-y-6">
                       <!-- Basic Info -->
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                           <div>
                               <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                                   Judul Proposal
                               </label>
                               <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                   {{ $proposal->judul_proposal }}
                               </p>
                           </div>
                           <div>
                               <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                                   RW Pengaju
                               </label>
                               <div class="flex items-center">
                                   <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mr-3">
                                       <i data-lucide="home" class="w-4 h-4 text-primary-600 dark:text-primary-400"></i>
                                   </div>
                                   <div>
                                       <p class="font-semibold text-gray-900 dark:text-white">
                                           {{ $proposal->rw->nama_rw ?? 'RW ' . $proposal->rw->no_rw }}
                                       </p>
                                       <p class="text-sm text-gray-600 dark:text-gray-400">
                                           {{ $proposal->submittedBy->name ?? 'N/A' }}
                                       </p>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                           <div>
                               <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                                   Jumlah Diminta
                               </label>
                               <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                   Rp {{ number_format($proposal->jumlah_bantuan, 0, ',', '.') }}
                               </p>
                           </div>
                           <div>
                               <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                                   Tanggal Pengajuan
                               </label>
                               <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                   {{ $proposal->created_at->format('d F Y, H:i') }}
                               </p>
                           </div>
                       </div>

                       <!-- Description -->
                       <div>
                           <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                               Deskripsi Proposal
                           </label>
                           <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                               <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ $proposal->deskripsi }}</p>
                           </div>
                       </div>

                       <!-- File Attachment -->
                       @if($proposal->file_proposal)
                           <div>
                               <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                                   File Proposal
                               </label>
                               <a href="{{ route('bantuan-proposals.download', $proposal) }}" 
                                  class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium rounded-lg transition-colors duration-200">
                                   <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                   Download File Proposal
                               </a>
                           </div>
                       @endif
                   </div>
               </div>

               <!-- Action Buttons -->
               <div class="mt-8 card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                   <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-orange-500 text-white">
                       <h2 class="text-xl font-semibold flex items-center">
                           <i data-lucide="clipboard-check" class="w-5 h-5 mr-2"></i>
                           Keputusan Review
                       </h2>
                   </div>
                   <div class="p-6">
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                           <!-- Approve Button -->
                           <button type="button" 
                                   onclick="showApprovalModal()"
                                   class="flex items-center justify-center p-6 border-2 border-green-200 dark:border-green-700 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-200 group">
                               <div class="text-center">
                                   <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                                       <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                                   </div>
                                   <h3 class="font-semibold text-green-700 dark:text-green-400 mb-1">Setujui Proposal</h3>
                                   <p class="text-sm text-gray-600 dark:text-gray-400">Proposal akan disetujui dan dana dicairkan</p>
                               </div>
                           </button>
                           
                           <!-- Reject Button -->
                           <button type="button" 
                                   onclick="showRejectionModal()"
                                   class="flex items-center justify-center p-6 border-2 border-red-200 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 group">
                               <div class="text-center">
                                   <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-red-200 dark:group-hover:bg-red-800/50 transition-colors">
                                       <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                                   </div>
                                   <h3 class="font-semibold text-red-700 dark:text-red-400 mb-1">Tolak Proposal</h3>
                                   <p class="text-sm text-gray-600 dark:text-gray-400">Proposal akan ditolak dengan alasan</p>
                               </div>
                           </button>
                       </div>
                   </div>
               </div>
           </div>

           <!-- Sidebar -->
           <div class="space-y-6">
               <!-- Saldo Info -->
               <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                   <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white">
                       <h3 class="text-lg font-semibold flex items-center">
                           <i data-lucide="wallet" class="w-5 h-5 mr-2"></i>
                           Informasi Saldo
                       </h3>
                   </div>
                   <div class="p-6 text-center">
                       <div class="mb-4">
                           <h4 class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                               Rp {{ number_format($saldoDesa, 0, ',', '.') }}
                           </h4>
                           <p class="text-sm text-gray-600 dark:text-gray-400">Saldo Desa Tersedia</p>
                       </div>
                       @if($saldoDesa < $proposal->jumlah_bantuan)
                           <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-lg border border-red-200 dark:border-red-800">
                               <div class="flex items-center justify-center text-red-700 dark:text-red-400">
                                   <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                                   <span class="text-sm font-medium">Saldo tidak mencukupi</span>
                               </div>
                           </div>
                       @else
                           <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-800">
                               <div class="flex items-center justify-center text-green-700 dark:text-green-400">
                                   <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                                   <span class="text-sm font-medium">Saldo mencukupi</span>
                               </div>
                           </div>
                       @endif
                   </div>
               </div>

               <!-- RW Info -->
               <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                   <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                       <h3 class="text-lg font-semibold flex items-center">
                           <i data-lucide="home" class="w-5 h-5 mr-2"></i>
                           Informasi RW
                       </h3>
                   </div>
                   <div class="p-6">
                       <div class="text-center mb-4">
                           <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                               <i data-lucide="home" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                           </div>
                           <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                               {{ $proposal->rw->nama_rw ?? 'RW ' . $proposal->rw->no_rw }}
                           </h4>
                           <p class="text-sm text-gray-600 dark:text-gray-400">
                               {{ $proposal->submittedBy->name ?? 'N/A' }}
                           </p>
                       </div>
                       <div class="text-center">
                           <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                               <h5 class="text-lg font-bold text-green-600 dark:text-green-400">
                                   Rp {{ number_format($proposal->rw->saldo ?? 0, 0, ',', '.') }}
                               </h5>
                               <p class="text-sm text-gray-600 dark:text-gray-400">Saldo RW</p>
                           </div>
                       </div>
                   </div>
               </div>

               <!-- Review Guidelines -->
               <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                   <div class="px-6 py-4 bg-gradient-to-r from-gray-600 to-gray-700 text-white">
                       <h3 class="text-lg font-semibold flex items-center">
                           <i data-lucide="list-checks" class="w-5 h-5 mr-2"></i>
                           Panduan Review
                       </h3>
                   </div>
                   <div class="p-6">
                       <div class="space-y-3">
                           <div class="flex items-start">
                               <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                               <span class="text-sm text-gray-700 dark:text-gray-300">Periksa kelengkapan proposal</span>
                           </div>
                           <div class="flex items-start">
                               <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                               <span class="text-sm text-gray-700 dark:text-gray-300">Evaluasi manfaat untuk warga</span>
                           </div>
                           <div class="flex items-start">
                               <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                               <span class="text-sm text-gray-700 dark:text-gray-300">Pastikan ketersediaan saldo</span>
                           </div>
                           <div class="flex items-start">
                               <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                               <span class="text-sm text-gray-700 dark:text-gray-300">Berikan catatan yang jelas</span>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-4">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Setujui Proposal?</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Proposal akan disetujui dan dana dicairkan</p>
                </div>
            </div>
            
            <form id="approvalForm" action="{{ route('bantuan-proposals.update-status', $proposal) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved">
                
                <div class="mb-4">
                    <label for="approval_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jumlah yang Disetujui
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="approval_amount" 
                               name="jumlah_disetujui_display" 
                               value="{{ number_format($proposal->jumlah_bantuan, 0, ',', '.') }}"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <input type="hidden" id="approval_amount_raw" name="jumlah_disetujui" value="{{ $proposal->jumlah_bantuan }}">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Maksimal: Rp {{ number_format($proposal->jumlah_bantuan, 0, ',', '.') }}</p>
                </div>
                
                <div class="mb-6">
                    <label for="approval_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Catatan Review <span class="text-red-500">*</span>
                    </label>
                    <textarea id="approval_notes" 
                              name="catatan_review" 
                              rows="3" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Berikan catatan untuk persetujuan..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="hideApprovalModal()"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        Setujui Proposal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mr-4">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tolak Proposal?</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Proposal akan ditolak dan RW akan mendapat notifikasi</p>
                </div>
            </div>
            
            <form id="rejectionForm" action="{{ route('bantuan-proposals.update-status', $proposal) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">
                
                <div class="mb-6">
                    <label for="rejection_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="rejection_notes" 
                              name="catatan_review" 
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Berikan alasan penolakan yang jelas..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="hideRejectionModal()"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        Tolak Proposal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal functions
function showApprovalModal() {
    document.getElementById('approvalModal').classList.remove('hidden');
    document.getElementById('approvalModal').classList.add('flex');
}

function hideApprovalModal() {
    document.getElementById('approvalModal').classList.add('hidden');
    document.getElementById('approvalModal').classList.remove('flex');
}

function showRejectionModal() {
    document.getElementById('rejectionModal').classList.remove('hidden');
    document.getElementById('rejectionModal').classList.add('flex');
}

function hideRejectionModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
    document.getElementById('rejectionModal').classList.remove('flex');
}

// Currency formatting for approval amount
document.getElementById('approval_amount').addEventListener('input', function() {
    let value = this.value.replace(/[^\d]/g, '');
    let maxValue = {{ $proposal->jumlah_bantuan }};
    
    if (value) {
        if (parseInt(value) > maxValue) {
            value = maxValue.toString();
        }
        
        document.getElementById('approval_amount_raw').value = value;
        this.value = new Intl.NumberFormat('id-ID').format(value);
    } else {
        document.getElementById('approval_amount_raw').value = '';
        this.value = '';
    }
});

// Form validation
document.getElementById('approvalForm').addEventListener('submit', function(e) {
    const amount = document.getElementById('approval_amount_raw').value;
    const saldoDesa = {{ $saldoDesa }};
    
    if (parseInt(amount) > saldoDesa) {
        e.preventDefault();
        alert('Jumlah yang disetujui melebihi saldo desa yang tersedia!');
        return false;
    }
});

// Close modals when clicking outside
document.getElementById('approvalModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideApprovalModal();
    }
});

document.getElementById('rejectionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectionModal();
    }
});

// Initialize icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

// Show messages
@if(session('success'))
    alert('{{ session('success') }}');
@endif

@if(session('error'))
    alert('{{ session('error') }}');
@endif
</script>
@endsection
