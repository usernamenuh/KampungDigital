@extends('layouts.app')

@section('title', 'Daftar Pembayaran Kas')
@section('page-title', 'Daftar Pembayaran Kas')
@section('page-description', 'Kelola dan konfirmasi pembayaran kas warga.')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-menunggu {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #fbbf24;
}

.status-lunas {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.status-ditolak {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    margin: 0 0.125rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    border: 1px solid transparent;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-view {
    background-color: #3b82f6;
    color: white;
}

.btn-view:hover {
    background-color: #2563eb;
    color: white;
}

.btn-approve {
    background-color: #10b981;
    color: white;
}

.btn-approve:hover {
    background-color: #059669;
    color: white;
}

.btn-reject {
    background-color: #ef4444;
    color: white;
}

.btn-reject:hover {
    background-color: #dc2626;
    color: white;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    max-width: 28rem;
    width: 100%;
    margin: 1rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.dark .modal-content {
    background: #374151;
    color: white;
}

@media (max-width: 768px) {
    .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        margin: 0.125rem;
    }
    
    .action-btn i {
        margin-right: 0.25rem;
    }
}
</style>
@endpush

@section('content')
<div class="p-4 md:p-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg md:text-xl font-semibold text-gray-800 dark:text-white">Daftar Pembayaran Kas</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola dan konfirmasi pembayaran kas warga</p>
            </div>
            <div class="flex items-center space-x-2 w-full md:w-auto">
                <form action="{{ route('payments.list') }}" method="GET" class="flex items-center space-x-2 w-full md:w-auto">
                    <select name="status" id="statusFilter" class="form-select rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm flex-1 md:flex-none">
                        <option value="">Semua Status</option>
                        <option value="menunggu_konfirmasi" {{ request('status') == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <button type="submit" class="px-3 md:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm whitespace-nowrap">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ID Tagihan
                        </th>
                        <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Warga
                        </th>
                        <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Periode
                        </th>
                        <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Metode Bayar
                        </th>
                        <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tanggal Bayar
                        </th>
                        <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($payments as $kas)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            #{{ str_pad($kas->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <div class="font-medium">{{ $kas->penduduk->nama_lengkap ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                RT {{ $kas->rt->no_rt ?? 'N/A' }} / RW {{ $kas->rt->rw->no_rw ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <div class="font-medium">Minggu {{ $kas->minggu_ke }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $kas->tahun }}</div>
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <div class="font-semibold text-green-600">
                                Rp {{ number_format($kas->jumlah + ($kas->denda ?? 0), 0, ',', '.') }}
                            </div>
                            @if($kas->denda > 0)
                                <div class="text-xs text-red-500">
                                    Denda: Rp {{ number_format($kas->denda, 0, ',', '.') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            @if($kas->metode_bayar)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    @switch($kas->metode_bayar)
                                        @case('tunai')
                                            <i class="fas fa-money-bill-wave mr-1"></i>
                                            Tunai
                                            @break
                                        @case('transfer_bank')
                                            <i class="fas fa-university mr-1"></i>
                                            Transfer Bank
                                            @break
                                        @case('e_wallet')
                                            <i class="fas fa-mobile-alt mr-1"></i>
                                            E-Wallet
                                            @break
                                        @default
                                            <i class="fas fa-credit-card mr-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $kas->metode_bayar)) }}
                                    @endswitch
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm">
                            @switch($kas->status)
                                @case('menunggu_konfirmasi')
                                    <span class="status-badge status-menunggu">
                                        <i class="fas fa-clock mr-1"></i>
                                        Menunggu Konfirmasi
                                    </span>
                                    @break
                                @case('lunas')
                                    <span class="status-badge status-lunas">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Lunas
                                    </span>
                                    @break
                                @case('ditolak')
                                    <span class="status-badge status-ditolak">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Ditolak
                                    </span>
                                    @break
                                @default
                                    <span class="status-badge" style="background-color: #f3f4f6; color: #374151; border-color: #d1d5db;">
                                        {{ ucfirst($kas->status) }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            @if($kas->tanggal_bayar)
                                <div class="font-medium">{{ $kas->tanggal_bayar->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $kas->tanggal_bayar->format('H:i') }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex flex-wrap items-center gap-1">
                                <a href="{{ route('payments.proof', $kas->id) }}" 
                                   class="action-btn btn-view" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                    <span class="hidden sm:inline ml-1">Detail</span>
                                </a>
                                
                                @if($kas->status === 'menunggu_konfirmasi' && in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
                                    <button onclick="openConfirmationModal({{ $kas->id }}, 'approve')" 
                                            class="action-btn btn-approve" 
                                            title="Setujui Pembayaran">
                                        <i class="fas fa-check"></i>
                                        <span class="hidden sm:inline ml-1">Setujui</span>
                                    </button>
                                    
                                    <button onclick="openConfirmationModal({{ $kas->id }}, 'reject')" 
                                            class="action-btn btn-reject" 
                                            title="Tolak Pembayaran">
                                        <i class="fas fa-times"></i>
                                        <span class="hidden sm:inline ml-1">Tolak</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data pembayaran</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pembayaran kas yang perlu dikonfirmasi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-4 md:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $payments->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                Konfirmasi Pembayaran
            </h3>
            <button onclick="closeConfirmationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <p id="confirmationText" class="text-gray-600 dark:text-gray-400 mb-4"></p>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-sticky-note mr-1"></i>
                Catatan (Opsional)
            </label>
            <textarea id="confirmationNotes" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                      placeholder="Tambahkan catatan jika diperlukan..."></textarea>
        </div>

        <div class="flex justify-end gap-3">
            <button onclick="closeConfirmationModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">
                <i class="fas fa-times mr-1"></i>
                Batal
            </button>
            <button id="confirmBtn" onclick="processConfirmation()" 
                    class="px-4 py-2 rounded-lg text-white transition-colors">
                <i class="fas fa-check mr-1"></i>
                <span id="confirmBtnText">Konfirmasi</span>
            </button>
        </div>
    </div>
</div>

<!-- Success/Error Message Modal -->
<div id="messageModal" class="modal-overlay" style="display: none;">
    <div class="modal-content text-center">
        <div id="messageIcon" class="text-5xl mb-4"></div>
        <h3 id="messageTitle" class="text-lg font-semibold text-gray-800 dark:text-white mb-2"></h3>
        <p id="messageText" class="text-gray-600 dark:text-gray-400 mb-6"></p>
        <button onclick="closeMessageModal()" 
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-check mr-1"></i>
            OK
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentKasId = null;
let currentAction = null;

function openConfirmationModal(kasId, action) {
    currentKasId = kasId;
    currentAction = action;

    const modal = document.getElementById('confirmationModal');
    const text = document.getElementById('confirmationText');
    const btn = document.getElementById('confirmBtn');
    const btnText = document.getElementById('confirmBtnText');
    const notesInput = document.getElementById('confirmationNotes');

    notesInput.value = ''; // Clear notes when opening

    if (action === 'approve') {
        text.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-2"></i>Apakah Anda yakin ingin <strong>menyetujui</strong> pembayaran ini?';
        btnText.textContent = 'Setujui';
        btn.className = 'px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white transition-colors';
    } else { // action is 'reject'
        text.innerHTML = '<i class="fas fa-times-circle text-red-500 mr-2"></i>Apakah Anda yakin ingin <strong>menolak</strong> pembayaran ini?';
        btnText.textContent = 'Tolak';
        btn.className = 'px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white transition-colors';
    }

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    document.getElementById('confirmationNotes').value = '';
    document.body.style.overflow = 'auto'; // Restore scrolling
    currentKasId = null;
    currentAction = null;
}

function showMessageModal(type, title, message) {
    const modal = document.getElementById('messageModal');
    const icon = document.getElementById('messageIcon');
    const titleEl = document.getElementById('messageTitle');
    const textEl = document.getElementById('messageText');

    // Reset icon classes
    icon.className = 'text-5xl mb-4';
    
    if (type === 'success') {
        icon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
    } else if (type === 'error') {
        icon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
    } else {
        icon.innerHTML = '<i class="fas fa-info-circle text-blue-500"></i>';
    }

    titleEl.textContent = title;
    textEl.textContent = message;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore scrolling
    location.reload(); // Reload page after message is closed
}

function processConfirmation() {
    if (!currentKasId || !currentAction) return;

    const notes = document.getElementById('confirmationNotes').value;
    const btn = document.getElementById('confirmBtn');
    const btnText = document.getElementById('confirmBtnText');
    
    // Disable button and show loading state
    btn.disabled = true;
    btnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Memproses...';

    const url = `{{ route('payments.confirm', ['kas' => '__KAS_ID__']) }}`.replace('__KAS_ID__', currentKasId);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            action: currentAction,
            catatan_konfirmasi: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        closeConfirmationModal(); // Close confirmation modal first
        if (data.success) {
            showMessageModal('success', 'Berhasil!', data.message);
        } else {
            showMessageModal('error', 'Gagal!', data.message || 'Terjadi kesalahan saat memproses konfirmasi.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closeConfirmationModal(); // Close confirmation modal on fetch error
        showMessageModal('error', 'Terjadi Kesalahan', 'Terjadi kesalahan saat memproses konfirmasi. Silakan coba lagi atau hubungi administrator.');
    })
    .finally(() => {
        // Re-enable button
        btn.disabled = false;
        btnText.innerHTML = '<i class="fas fa-check mr-1"></i>Konfirmasi';
    });
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const confirmationModal = document.getElementById('confirmationModal');
    const messageModal = document.getElementById('messageModal');
    
    if (event.target === confirmationModal) {
        closeConfirmationModal();
    }
    
    if (event.target === messageModal) {
        closeMessageModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeConfirmationModal();
        closeMessageModal();
    }
});

// Auto-submit filter form when status changes
document.getElementById('statusFilter').addEventListener('change', function() {
    this.form.submit();
});
</script>
@endpush
