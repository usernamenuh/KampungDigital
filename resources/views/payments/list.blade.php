@extends('layouts.app')

@section('title', 'Daftar Pembayaran Kas')
@section('page-title', 'Daftar Pembayaran Kas')
@section('page-description', 'Kelola dan konfirmasi pembayaran kas warga.')

@section('content')
<div class="p-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Pembayaran Kas</h3>
            <div class="flex items-center space-x-2">
                <form action="{{ route('payments.list') }}" method="GET" class="flex items-center space-x-2">
                    <select name="status" id="statusFilter" class="form-select rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="menunggu_konfirmasi" {{ request('status') == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ID Tagihan
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Warga
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Periode
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Metode Bayar
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tanggal Bayar
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($payments as $kas)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            #{{ str_pad($kas->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $kas->penduduk->nama_lengkap ?? 'N/A' }}
                            <div class="text-xs text-gray-500">RT {{ $kas->rt->no_rt ?? 'N/A' }} / RW {{ $kas->rt->rw->no_rw ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            Minggu {{ $kas->minggu_ke }}/{{ $kas->tahun }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            Rp {{ number_format($kas->total_bayar, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $kas->metode_bayar_formatted }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($kas->status === 'menunggu_konfirmasi')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Menunggu Konfirmasi
                                </span>
                            @elseif($kas->status === 'lunas')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Lunas
                                </span>
                            @elseif($kas->status === 'ditolak')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Ditolak
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    {{ $kas->status_text }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $kas->tanggal_bayar_formatted }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('payments.proof', $kas->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Lihat Bukti">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                @if($kas->status === 'menunggu_konfirmasi')
                                    <button onclick="openConfirmationModal({{ $kas->id }}, 'approve')" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" title="Setujui">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                    <button onclick="openConfirmationModal({{ $kas->id }}, 'reject')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Tolak">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-gray-400">
                            Tidak ada data pembayaran yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6">
            {{ $payments->links() }}
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Konfirmasi Pembayaran</h3>
        <p id="confirmationText" class="text-gray-600 dark:text-gray-400 mb-4"></p>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
            <textarea id="confirmationNotes" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                      placeholder="Tambahkan catatan jika diperlukan..."></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button onclick="closeConfirmationModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">
                Batal
            </button>
            <button id="confirmBtn" onclick="processConfirmation()" class="px-4 py-2 rounded-lg text-white transition-colors">
                Konfirmasi
            </button>
        </div>
    </div>
</div>

<!-- Message Modal (for success/error) -->
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-sm w-full mx-4 shadow-xl text-center">
        <div id="messageIcon" class="text-5xl mb-4"></div>
        <h3 id="messageTitle" class="text-lg font-semibold text-gray-800 dark:text-white mb-2"></h3>
        <p id="messageText" class="text-gray-600 dark:text-gray-400 mb-6"></p>
        <button onclick="closeMessageModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
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
    const notesInput = document.getElementById('confirmationNotes');

    notesInput.value = ''; // Clear notes when opening

    if (action === 'approve') {
        text.textContent = 'Apakah Anda yakin ingin menyetujui pembayaran ini?';
        btn.textContent = 'Setujui';
        btn.className = 'bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition-colors';
    } else { // action is 'reject'
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

function showMessageModal(type, title, message) {
    const modal = document.getElementById('messageModal');
    const icon = document.getElementById('messageIcon');
    const titleEl = document.getElementById('messageTitle');
    const textEl = document.getElementById('messageText');

    icon.className = 'text-5xl mb-4'; // Reset classes
    if (type === 'success') {
        icon.classList.add('text-green-500', 'fas', 'fa-check-circle');
    } else if (type === 'error') {
        icon.classList.add('text-red-500', 'fas', 'fa-times-circle');
    } else {
        icon.classList.add('text-blue-500', 'fas', 'fa-info-circle');
    }

    titleEl.textContent = title;
    textEl.textContent = message;
    modal.style.display = 'flex';
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
    location.reload(); // Reload page after message is closed
}

function processConfirmation() {
    if (!currentKasId || !currentAction) return;

    const notes = document.getElementById('confirmationNotes').value;

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
            showMessageModal('error', 'Gagal!', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closeConfirmationModal(); // Close confirmation modal on fetch error
        showMessageModal('error', 'Terjadi Kesalahan', 'Terjadi kesalahan saat memproses konfirmasi. Silakan coba lagi atau hubungi administrator.');
    });
}
</script>
@endpush
