@extends('layouts.app')

@section('title', 'Kirim Notifikasi Kas RT')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Kirim Notifikasi Kas RT</h1>
            <p class="mb-0 text-muted">Kirim notifikasi kas mingguan ke seluruh warga RT</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-bell"></i> Form Notifikasi Kas Mingguan
                    </h6>
                </div>
                <div class="card-body">
                    <div id="notification-form">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Informasi:</strong> Notifikasi akan dikirim ke semua warga yang terdaftar di RT Anda.
                        </div>

                        <form id="kasNotificationForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jumlah">Jumlah Kas <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number" class="form-control" id="jumlah" name="jumlah" 
                                                   placeholder="10000" min="1000" required>
                                        </div>
                                        <small class="form-text text-muted">Minimal Rp 1.000</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jatuh_tempo">Jatuh Tempo <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo" 
                                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                        <small class="form-text text-muted">Minimal besok</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan (Opsional)</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                          placeholder="Keterangan tambahan untuk kas ini..."></textarea>
                            </div>

                            <div class="form-group">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Preview Notifikasi:</h6>
                                        <div id="notification-preview" class="border p-3 rounded bg-white">
                                            <strong>Kas RT Minggu ke-<span id="preview-week">{{ date('W') }}</span></strong><br>
                                            <small class="text-muted">Kas RT sebesar <span id="preview-amount">Rp 0</span> telah dibuat. 
                                            Jatuh tempo: <span id="preview-date">-</span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary" id="sendButton">
                                    <i class="fas fa-paper-plane"></i> Kirim Notifikasi
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Success Message -->
                    <div id="success-message" class="d-none">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Berhasil!</strong> <span id="success-text"></span>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary" onclick="resetForm()">
                                <i class="fas fa-plus"></i> Kirim Notifikasi Lagi
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-home"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi RT</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-home fa-3x text-primary mb-2"></i>
                        <h5>{{ auth()->user()->rt_name ?? 'RT 001' }}</h5>
                        <p class="text-muted">{{ auth()->user()->rw_name ?? 'RW 01' }} - {{ auth()->user()->village_name ?? 'Desa Sukamaju' }}</p>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-primary" id="total-warga">-</h4>
                                <small class="text-muted">Total Warga</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success" id="minggu-ini">{{ date('W') }}</h4>
                            <small class="text-muted">Minggu Ini</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning"></i>
                            Tentukan jumlah kas yang wajar
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-calendar text-warning"></i>
                            Berikan waktu yang cukup untuk pembayaran
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-bell text-warning"></i>
                            Notifikasi akan dikirim secara otomatis
                        </li>
                        <li>
                            <i class="fas fa-check text-warning"></i>
                            Pastikan data sudah benar sebelum mengirim
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set default jatuh tempo (7 hari dari sekarang)
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    $('#jatuh_tempo').val(nextWeek.toISOString().split('T')[0]);
    
    // Update preview saat input berubah
    $('#jumlah, #jatuh_tempo').on('input change', updatePreview);
    
    // Load RT info
    loadRtInfo();
    
    // Form submission
    $('#kasNotificationForm').on('submit',function(e) {
        e.preventDefault();
        sendNotification();
    });
    
    // Initial preview update
    updatePreview();
});

function updatePreview() {
    const jumlah = $('#jumlah').val();
    const jatuhTempo = $('#jatuh_tempo').val();
    
    // Format currency
    const formattedAmount = jumlah ? 'Rp ' + parseInt(jumlah).toLocaleString('id-ID') : 'Rp 0';
    $('#preview-amount').text(formattedAmount);
    
    // Format date
    if (jatuhTempo) {
        const date = new Date(jatuhTempo);
        const formattedDate = date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
        $('#preview-date').text(formattedDate);
    } else {
        $('#preview-date').text('-');
    }
}

function loadRtInfo() {
    // Mock data - replace with actual API call
    $('#total-warga').text('85');
}

function sendNotification() {
    const formData = {
        jumlah: $('#jumlah').val(),
        jatuh_tempo: $('#jatuh_tempo').val(),
        keterangan: $('#keterangan').val(),
        _token: $('input[name="_token"]').val()
    };
    
    // Validate
    if (!formData.jumlah || formData.jumlah < 1000) {
        alert('Jumlah kas minimal Rp 1.000');
        return;
    }
    
    if (!formData.jatuh_tempo) {
        alert('Tanggal jatuh tempo harus diisi');
        return;
    }
    
    // Disable button
    $('#sendButton').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');
    
    // Simulate API call
    setTimeout(() => {
        // Mock success response
        const response = {
            success: true,
            message: `Berhasil mengirim notifikasi kas minggu ke-${$('#minggu-ini').text()} kepada ${$('#total-warga').text()} warga`,
            data: {
                kas_created: parseInt($('#total-warga').text()),
                notifications_sent: parseInt($('#total-warga').text()),
                minggu: parseInt($('#minggu-ini').text()),
                tahun: new Date().getFullYear(),
                jumlah: formData.jumlah,
                jatuh_tempo: formData.jatuh_tempo
            }
        };
        
        if (response.success) {
            $('#success-text').text(response.message);
            $('#notification-form').addClass('d-none');
            $('#success-message').removeClass('d-none');
            
            // Show success notification
            if (typeof showNotification === 'function') {
                showNotification('Notifikasi kas berhasil dikirim!', 'success');
            }
        } else {
            alert('Gagal mengirim notifikasi: ' + response.message);
            $('#sendButton').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Kirim Notifikasi');
        }
    }, 2000);
}

function resetForm() {
    $('#kasNotificationForm')[0].reset();
    $('#notification-form').removeClass('d-none');
    $('#success-message').addClass('d-none');
    $('#sendButton').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Kirim Notifikasi');
    
    // Reset default date
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    $('#jatuh_tempo').val(nextWeek.toISOString().split('T')[0]);
    
    updatePreview();
}
</script>
@endpush
