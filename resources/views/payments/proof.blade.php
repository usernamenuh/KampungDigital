@extends('layouts.app')

@section('title', 'Bukti Pembayaran Kas')
@section('page-title', 'Bukti Pembayaran Kas')
@section('page-description', 'Lihat detail dan bukti pembayaran kas')

@section('content')
<div class="p-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Detail Pembayaran #{{ str_pad($kas->id, 6, '0', STR_PAD_LEFT) }}</h3>
            <a href="{{ route('payments.list') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-3">Informasi Pembayaran</h4>
                <dl class="space-y-2 text-gray-700 dark:text-gray-300">
                    <div>
                        <dt class="font-medium">Warga:</dt>
                        <dd>{{ $kas->penduduk->nama_lengkap ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">RT/RW:</dt>
                        <dd>RT {{ $kas->rt->no_rt ?? 'N/A' }} / RW {{ $kas->rt->rw->no_rw ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Periode Kas:</dt>
                        <dd>Minggu {{ $kas->minggu_ke }}/{{ $kas->tahun }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Jumlah Tagihan:</dt>
                        <dd>Rp {{ number_format($kas->jumlah, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Denda:</dt>
                        <dd>Rp {{ number_format($kas->denda, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Total Dibayar:</dt>
                        <dd class="font-bold text-green-600">Rp {{ number_format($kas->jumlah_dibayar, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Metode Pembayaran:</dt>
                        <dd>{{ $kas->metode_bayar_formatted }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Status:</dt>
                        <dd>
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
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium">Tanggal Bayar:</dt>
                        <dd>{{ $kas->tanggal_bayar_formatted }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Waktu Unggah Bukti:</dt>
                        <dd>{{ $kas->bukti_bayar_uploaded_at_formatted }}</dd>
                    </div>
                    @if($kas->status === 'lunas' || $kas->status === 'ditolak')
                    <div>
                        <dt class="font-medium">Dikonfirmasi Oleh:</dt>
                        <dd>{{ $kas->confirmedBy->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Waktu Konfirmasi:</dt>
                        <dd>{{ $kas->confirmed_at ? \Carbon\Carbon::parse($kas->confirmed_at)->format('d M Y H:i') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Catatan Konfirmasi:</dt>
                        <dd>{{ $kas->confirmation_notes ?? '-' }}</dd>
                    </div>
                    @endif
                    @if($kas->keterangan)
                    <div>
                        <dt class="font-medium">Keterangan:</dt>
                        <dd>{{ $kas->keterangan }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div>
                <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-3">Bukti Pembayaran</h4>
                @if($kas->bukti_bayar_file)
                    <div class="mb-4 border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                        @php
                            $fileExtension = pathinfo($kas->bukti_bayar_file, PATHINFO_EXTENSION);
                        @endphp

                        @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ asset('storage/' . $kas->bukti_bayar_file) }}" alt="Bukti Pembayaran" class="w-full h-auto object-cover max-h-96">
                        @elseif($fileExtension === 'pdf')
                            <div class="p-4 text-center text-gray-600 dark:text-gray-400">
                                <i class="fas fa-file-pdf text-6xl text-red-500 mb-3"></i>
                                <p>File PDF tersedia. Klik tombol di bawah untuk melihat atau mengunduh.</p>
                            </div>
                        @else
                            <div class="p-4 text-center text-gray-600 dark:text-gray-400">
                                <i class="fas fa-file text-6xl text-gray-500 mb-3"></i>
                                <p>Tipe file tidak didukung untuk pratinjau. Silakan unduh.</p>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('payments.download.proof', $kas->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download w-4 h-4 mr-2"></i> Download Bukti
                        </a>
                        @if(in_array($fileExtension, ['pdf']))
                            <a href="{{ asset('storage/' . $kas->bukti_bayar_file) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                                <i class="fas fa-external-link-alt w-4 h-4 mr-2"></i> Lihat PDF
                            </a>
                        @endif
                    </div>
                    @if($kas->bukti_bayar_notes)
                        <p class="text-gray-700 dark:text-gray-300 mt-4">
                            <span class="font-medium">Catatan Pembayar:</span> {{ $kas->bukti_bayar_notes }}
                        </p>
                    @endif
                @else
                    <div class="p-12 text-center border border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                        <i class="fas fa-image text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Tidak Ada Bukti Pembayaran</h3>
                        <p class="text-gray-500 dark:text-gray-400">Pembayaran ini tidak memiliki bukti yang diunggah.</p>
                    </div>
                @endif

                @if($kas->status === 'menunggu_konfirmasi')
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-3">Aksi Konfirmasi</h4>
                    <div class="mb-4">
                        <label for="proofConfirmationNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan Konfirmasi (Opsional)</label>
                        <textarea id="proofConfirmationNotes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="confirmPaymentFromProof({{ $kas->id }}, 'approve')"
                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-1"></i> Setujui Pembayaran
                        </button>
                        <button onclick="confirmPaymentFromProof({{ $kas->id }}, 'reject')"
                                class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times mr-1"></i> Tolak Pembayaran
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Message Modal (for success/error) -->
<div id="messageModalProof" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-sm w-full mx-4 shadow-xl text-center">
        <div id="messageIconProof" class="text-5xl mb-4"></div>
        <h3 id="messageTitleProof" class="text-lg font-semibold text-gray-800 dark:text-white mb-2"></h3>
        <p id="messageTextProof" class="text-gray-600 dark:text-gray-400 mb-6"></p>
        <button onclick="closeMessageModalProof()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            OK
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showMessageModalProof(type, title, message) {
    const modal = document.getElementById('messageModalProof');
    const icon = document.getElementById('messageIconProof');
    const titleEl = document.getElementById('messageTitleProof');
    const textEl = document.getElementById('messageTextProof');

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

function closeMessageModalProof() {
    document.getElementById('messageModalProof').style.display = 'none';
    location.reload(); // Reload page after message is closed
}

function confirmPaymentFromProof(kasId, action) {
    const notes = document.getElementById('proofConfirmationNotes').value;

    const url = `{{ route('payments.confirm', ['kas' => '__KAS_ID__']) }}`.replace('__KAS_ID__', kasId);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            action: action,
            catatan_konfirmasi: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessageModalProof('success', 'Berhasil!', data.message);
        } else {
            showMessageModalProof('error', 'Gagal!', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessageModalProof('error', 'Terjadi Kesalahan', 'Terjadi kesalahan saat memproses konfirmasi. Silakan coba lagi atau hubungi administrator.');
    });
}
</script>
@endpush
