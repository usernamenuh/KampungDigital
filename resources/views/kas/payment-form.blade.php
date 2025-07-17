@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Form Pembayaran Kas
                    </h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Error!</strong> Ada kesalahan dalam input:
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <strong>Error!</strong> {{ session('error') }}
                        </div>
                    @endif

                    <!-- Detail Kas -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Detail Kas</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Minggu ke:</strong> {{ $kas->minggu_ke }}</p>
                                    <p><strong>Tahun:</strong> {{ $kas->tahun }}</p>
                                    <p><strong>Jumlah:</strong> Rp {{ number_format($kas->jumlah, 0, ',', '.') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Keterangan:</strong> Kas Mingguan RT {{ $kas->rt->no_rt ?? 'N/A' }}</p>
                                    <p><strong>Jatuh Tempo:</strong> {{ $kas->tanggal_jatuh_tempo_formatted }}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-warning">{{ $kas->status_text }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form action="{{ route('kas.payment.process', $kas->id) }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                        @csrf
                        
                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label for="payment_method" class="form-label">
                                <strong>Metode Pembayaran</strong>
                            </label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="Tunai (Bayar Langsung)">💰 Tunai (Bayar Langsung)</option>
                                @if($paymentInfo)
                                    @if($paymentInfo->has_bank_transfer)
                                        <option value="{{ $paymentInfo->bank_name }}">🏦 {{ $paymentInfo->bank_name }} - {{ $paymentInfo->bank_account_number }}</option>
                                    @endif
                                    @if($paymentInfo->has_e_wallet)
                                        @if($paymentInfo->dana_number)
                                            <option value="DANA">💳 DANA - {{ $paymentInfo->dana_number }}</option>
                                        @endif
                                        @if($paymentInfo->gopay_number)
                                            <option value="GoPay">💳 GoPay - {{ $paymentInfo->gopay_number }}</option>
                                        @endif
                                        @if($paymentInfo->ovo_number)
                                            <option value="OVO">💳 OVO - {{ $paymentInfo->ovo_number }}</option>
                                        @endif
                                        @if($paymentInfo->shopeepay_number)
                                            <option value="ShopeePay">💳 ShopeePay - {{ $paymentInfo->shopeepay_number }}</option>
                                        @endif
                                    @endif
                                    @if($paymentInfo->has_qr_code)
                                        <option value="QR Code">📱 QR Code</option>
                                    @endif
                                @endif
                            </select>
                        </div>

                        <!-- Payment Info Display -->
                        @if($paymentInfo)
                            <div id="paymentInfoDisplay" class="mb-4" style="display: none;">
                                <!-- Bank Transfer Info -->
                                <div id="bankInfo" class="payment-info" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6><i class="fas fa-university me-2"></i>Informasi Transfer Bank</h6>
                                            <p><strong>Bank:</strong> {{ $paymentInfo->bank_name }}</p>
                                            <p><strong>No. Rekening:</strong> {{ $paymentInfo->bank_account_number }}</p>
                                            <p><strong>Atas Nama:</strong> {{ $paymentInfo->bank_account_name }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- E-Wallet Info -->
                                @if($paymentInfo->dana_number)
                                    <div id="danaInfo" class="payment-info" style="display: none;">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6><i class="fas fa-wallet me-2"></i>Informasi DANA</h6>
                                                <p><strong>Nomor:</strong> {{ $paymentInfo->dana_number }}</p>
                                                <p><strong>Atas Nama:</strong> {{ $paymentInfo->dana_account_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($paymentInfo->gopay_number)
                                    <div id="gopayInfo" class="payment-info" style="display: none;">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6><i class="fas fa-wallet me-2"></i>Informasi GoPay</h6>
                                                <p><strong>Nomor:</strong> {{ $paymentInfo->gopay_number }}</p>
                                                <p><strong>Atas Nama:</strong> {{ $paymentInfo->gopay_account_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($paymentInfo->ovo_number)
                                    <div id="ovoInfo" class="payment-info" style="display: none;">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6><i class="fas fa-wallet me-2"></i>Informasi OVO</h6>
                                                <p><strong>Nomor:</strong> {{ $paymentInfo->ovo_number }}</p>
                                                <p><strong>Atas Nama:</strong> {{ $paymentInfo->ovo_account_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($paymentInfo->shopeepay_number)
                                    <div id="shopeepayInfo" class="payment-info" style="display: none;">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6><i class="fas fa-wallet me-2"></i>Informasi ShopeePay</h6>
                                                <p><strong>Nomor:</strong> {{ $paymentInfo->shopeepay_number }}</p>
                                                <p><strong>Atas Nama:</strong> {{ $paymentInfo->shopeepay_account_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- QR Code Info -->
                                @if($paymentInfo->has_qr_code)
                                    <div id="qrInfo" class="payment-info" style="display: none;">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6><i class="fas fa-qrcode me-2"></i>QR Code Payment</h6>
                                                @if($paymentInfo->qr_code_url)
                                                    <img src="{{ $paymentInfo->qr_code_url }}" alt="QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                                                @endif
                                                @if($paymentInfo->qr_code_description)
                                                    <p>{{ $paymentInfo->qr_code_description }}</p>
                                                @endif
                                                @if($paymentInfo->qr_code_account_name)
                                                    <p><strong>Atas Nama:</strong> {{ $paymentInfo->qr_code_account_name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Cash Payment Info -->
                                <div id="cashInfo" class="payment-info" style="display: none;">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-money-bill-wave me-2"></i>Pembayaran Tunai</h6>
                                        <p class="mb-0">Silakan bayar langsung kepada Ketua RT atau petugas yang ditunjuk. Pembayaran tunai akan langsung dikonfirmasi sebagai lunas.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Amount -->
                        <div class="mb-3">
                            <label for="amount_paid" class="form-label">
                                <strong>Jumlah Dibayar</strong>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="amount_paid" name="amount_paid" 
                                       value="{{ $kas->total_bayar }}" min="0" step="0.01" required>
                            </div>
                            <small class="text-muted">Jumlah yang harus dibayar: Rp {{ number_format($kas->total_bayar, 0, ',', '.') }}</small>
                        </div>

                        <!-- Proof of Payment -->
                        <div class="mb-3" id="proofSection">
                            <label for="proof_of_payment" class="form-label">
                                <strong>Unggah Bukti Pembayaran</strong> <span class="text-muted">(Opsional untuk tunai)</span>
                            </label>
                            <input type="file" class="form-control" id="proof_of_payment" name="proof_of_payment" 
                                   accept="image/*,.pdf" onchange="previewFile()">
                            <small class="text-muted">Format: JPG, PNG, PDF. Maksimal 5MB</small>
                            <div id="filePreview" class="mt-2"></div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">
                                <strong>Catatan</strong> <span class="text-muted">(Opsional)</span>
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>

                        <!-- Payment Notes from RT -->
                        @if($paymentInfo && $paymentInfo->payment_notes)
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Catatan dari RT:</h6>
                                <p class="mb-0">{{ $paymentInfo->payment_notes }}</p>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('kas.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane me-1"></i> Ajukan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const paymentInfoDisplay = document.getElementById('paymentInfoDisplay');
    const proofSection = document.getElementById('proofSection');
    const proofInput = document.getElementById('proof_of_payment');
    const submitBtn = document.getElementById('submitBtn');

    paymentMethodSelect.addEventListener('change', function() {
        const selectedMethod = this.value;
        
        // Hide all payment info sections
        document.querySelectorAll('.payment-info').forEach(info => {
            info.style.display = 'none';
        });

        if (selectedMethod) {
            paymentInfoDisplay.style.display = 'block';
            
            // Show relevant payment info
            if (selectedMethod.includes('{{ $paymentInfo->bank_name ?? "" }}')) {
                document.getElementById('bankInfo').style.display = 'block';
                proofInput.required = true;
                submitBtn.innerHTML = '<i class="fas fa-clock me-1"></i> Ajukan Pembayaran (Menunggu Konfirmasi)';
            } else if (selectedMethod.includes('DANA')) {
                document.getElementById('danaInfo').style.display = 'block';
                proofInput.required = true;
                submitBtn.innerHTML = '<i class="fas fa-clock me-1"></i> Ajukan Pembayaran (Menunggu Konfirmasi)';
            } else if (selectedMethod.includes('GoPay')) {
                document.getElementById('gopayInfo').style.display = 'block';
                proofInput.required = true;
                submitBtn.innerHTML = '<i class="fas fa-clock me-1"></i> Ajukan Pembayaran (Menunggu Konfirmasi)';
            } else if (selectedMethod.includes('OVO')) {
                document.getElementById('ovoInfo').style.display = 'block';
                proofInput.required = true;
                submitBtn.innerHTML = '<i class="fas fa-clock me-1"></i> Ajukan Pembayaran (Menunggu Konfirmasi)';
            } else if (selectedMethod.includes('ShopeePay')) {
                document.getElementById('shopeepayInfo').style.display = 'block';
                proofInput.required = true;
                submitBtn.innerHTML = '<i class="fas fa-clock me-1"></i> Ajukan Pembayaran (Menunggu Konfirmasi)';
            } else if (selectedMethod.includes('QR Code')) {
                document.getElementById('qrInfo').style.display = 'block';
                proofInput.required = true;
                submitBtn.innerHTML = '<i class="fas fa-clock me-1"></i> Ajukan Pembayaran (Menunggu Konfirmasi)';
            } else if (selectedMethod.includes('Tunai')) {
                document.getElementById('cashInfo').style.display = 'block';
                proofInput.required = false;
                submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> Konfirmasi Pembayaran Tunai';
            }
        } else {
            paymentInfoDisplay.style.display = 'none';
            proofInput.required = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Ajukan Pembayaran';
        }
    });
});

function previewFile() {
    const file = document.getElementById('proof_of_payment').files[0];
    const preview = document.getElementById('filePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (file.type.startsWith('image/')) {
                preview.innerHTML = `
                    <div class="mt-2">
                        <p class="text-success"><i class="fas fa-check"></i> File terpilih: ${file.name}</p>
                        <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                `;
            } else {
                preview.innerHTML = `
                    <div class="mt-2">
                        <p class="text-success"><i class="fas fa-check"></i> File terpilih: ${file.name}</p>
                        <p class="text-muted">File PDF berhasil dipilih</p>
                    </div>
                `;
            }
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}
</script>
@endsection
