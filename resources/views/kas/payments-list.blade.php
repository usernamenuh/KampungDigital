@extends('layouts.app')

@section('title', 'Daftar Pembayaran')
@section('page-title', 'Daftar Pembayaran')
@section('page-description', 'Kelola konfirmasi pembayaran kas')

@section('content')
<div class="p-6">
    <!-- Filter Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="menunggu_konfirmasi" {{ request('status') === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                    <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('kas.payments.list') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Menunggu Konfirmasi</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $payments->where('status', 'menunggu_konfirmasi')->count() }}</p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-xl">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lunas</p>
                    <p class="text-2xl font-bold text-green-600">{{ $payments->where('status', 'lunas')->count() }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ditolak</p>
                    <p class="text-2xl font-bold text-red-600">{{ $payments->where('status', 'belum_bayar')->where('confirmed_at', '!=', null)->count() }}</p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $payments->count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Pembayaran</h3>
        </div>

        @if($payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Warga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->penduduk->nama_lengkap }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">RT {{ $payment->rt->no_rt }} / RW {{ $payment->rt->rw->no_rw }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            Minggu {{ $payment->minggu_ke }}/{{ $payment->tahun }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            Rp {{ number_format($payment->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            @if($payment->metode_bayar === 'bank_transfer')
                                <i class="fas fa-university mr-1"></i> Transfer Bank
                            @elseif($payment->metode_bayar === 'e_wallet')
                                <i class="fas fa-mobile-alt mr-1"></i> E-Wallet
                            @elseif($payment->metode_bayar === 'qr_code')
                                <i class="fas fa-qrcode mr-1"></i> QR Code
                            @else
                                {{ $payment->metode_bayar }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->status === 'menunggu_konfirmasi')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Menunggu Konfirmasi
                                </span>
                            @elseif($payment->status === 'lunas')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Lunas
                                </span>
                            @elseif($payment->status === 'belum_bayar' && $payment->confirmed_at)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $payment->bukti_bayar_uploaded_at ? \Carbon\Carbon::parse($payment->bukti_bayar_uploaded_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('kas.payment-proof', $payment) }}" 
                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                                @if($payment->status === 'menunggu_konfirmasi')
                                <span class="text-gray-300">|</span>
                                <button onclick="confirmPayment({{ $payment->id }}, 'approve')" 
                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                    <i class="fas fa-check mr-1"></i> Setujui
                                </button>
                                <span class="text-gray-300">|</span>
                                <button onclick="confirmPayment({{ $payment->id }}, 'reject')" 
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $payments->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <i class="fas fa-file-invoice text-gray-400 text-5xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Tidak Ada Pembayaran</h3>
            <p class="text-gray-500 dark:text-gray-400">Belum ada pembayaran yang perlu dikonfirmasi.</p>
        </div>
        @endif
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Konfirmasi Pembayaran</h3>
        <p id="confirmationText" class="text-gray-600 dark:text-gray-400 mb-4"></p>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
            <textarea id="confirmationNotes" rows="3" 
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                      placeholder="Tambahkan catatan jika diperlukan..."></textarea>
        </div>
        
        <div class="flex gap-2">
            <button onclick="closeConfirmationModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                Batal
            </button>
            <button id="confirmBtn" onclick="processConfirmation()" class="px-4 py-2 rounded-lg text-white transition-colors">
                Konfirmasi
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentKasId = null;
let currentAction = null;

function confirmPayment(kasId, action) {
    currentKasId = kasId;
    currentAction = action;
    
    const modal = document.getElementById('confirmationModal');
    const text = document.getElementById('confirmationText');
    const btn = document.getElementById('confirmBtn');
    
    if (action === 'approve') {
        text.textContent = 'Apakah Anda yakin ingin menyetujui pembayaran ini?';
        btn.textContent = 'Setujui';
        btn.className = 'bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition-colors';
    } else {
        text.textContent = 'Apakah Anda yakin ingin menolak pembayaran ini?';
        btn.textContent = 'Tolak';
        btn.className = 'bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-white transition-colors';
    }
    
    modal.style.display = 'flex';
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    document.getElementById('confirmationNotes').value = '';
    currentKasId = null;
    currentAction = null;
}

function processConfirmation() {
    if (!currentKasId || !currentAction) return;
    
    const notes = document.getElementById('confirmationNotes').value;
    
    fetch(`/kas/${currentKasId}/confirm-payment`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            action: currentAction,
            confirmation_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses konfirmasi');
    })
    .finally(() => {
        closeConfirmationModal();
    });
}
</script>
@endpush
