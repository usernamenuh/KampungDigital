@extends('layouts.app')

@section('title', 'Generate Kas Mingguan')

@push('styles')
<style>
.gradient-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.form-modern {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.form-group label {
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.form-control {
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    padding: 0.875rem 1.25rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.95rem;
    background: rgba(255, 255, 255, 0.8);
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
    background: #ffffff;
}

.btn-modern {
    border-radius: 12px;
    padding: 0.875rem 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.btn-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.btn-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-modern:hover::before {
    left: 100%;
}

.info-card {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 20px;
    color: white;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
    transform: translateY(0);
    transition: transform 0.3s ease;
}

.info-card:hover {
    transform: translateY(-5px);
}

.warning-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 20px;
    color: white;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(240, 147, 251, 0.3);
    transform: translateY(0);
    transition: transform 0.3s ease;
}

.warning-card:hover {
    transform: translateY(-5px);
}

.preview-card {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-top: 1.5rem;
    box-shadow: 0 10px 30px rgba(168, 237, 234, 0.4);
    animation: fadeInUp 0.6s ease-out;
}

.admin-badge {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    color: #8b4513;
    padding: 0.75rem 1.5rem;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 1.5rem;
    box-shadow: 0 5px 15px rgba(255, 236, 210, 0.6);
    animation: pulse 2s infinite;
}

.week-range {
    background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    margin: 1.5rem 0;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border: 2px solid #dee2e6;
}

.calculation-preview {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-radius: 15px;
    padding: 1.5rem;
    margin-top: 1.5rem;
    border-left: 5px solid #28a745;
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.2);
}

.error-message {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 15px;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
    animation: shake 0.5s ease-in-out;
}

/* Success Modal Styles */
.success-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}

.success-modal-content {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 25px;
    padding: 3rem;
    text-align: center;
    max-width: 600px;
    width: 90%;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    animation: modalSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.success-modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
    background-size: 200% 100%;
    animation: gradientShift 3s ease infinite;
}

.success-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #51cf66 0%, #40c057 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    animation: bounceIn 0.8s ease-out;
    box-shadow: 0 15px 40px rgba(81, 207, 102, 0.4);
}

.success-icon i {
    font-size: 3rem;
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin: 2rem 0;
}

.stat-item {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 600;
}

.confetti {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #667eea;
    animation: confetti-fall 3s linear infinite;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.8) translateY(-50px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes bounceIn {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes confetti-fall {
    0% {
        transform: translateY(-100vh) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
    }
}

.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
}

.floating-element {
    position: absolute;
    opacity: 0.1;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.input-group-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
}

.custom-control-label {
    font-weight: 600;
    color: #2d3748;
}

.custom-control-input:checked ~ .custom-control-label::before {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Floating Elements -->
    <div class="floating-elements">
        <i class="fas fa-calendar-alt floating-element" style="top: 10%; left: 10%; font-size: 2rem;"></i>
        <i class="fas fa-clock floating-element" style="top: 20%; right: 15%; font-size: 1.5rem;"></i>
        <i class="fas fa-coins floating-element" style="bottom: 20%; left: 20%; font-size: 1.8rem;"></i>
        <i class="fas fa-chart-line floating-element" style="bottom: 30%; right: 10%; font-size: 2.2rem;"></i>
    </div>

    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="gradient-header p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-2 font-weight-bold">
                            <i class="fas fa-calendar-plus mr-3"></i>
                            Generate Kas Mingguan
                        </h2>
                        <p class="mb-0 opacity-90 font-weight-500">Buat tagihan kas untuk beberapa minggu sekaligus dengan mudah</p>
                    </div>
                    <div>
                        <a href="{{ route('kas.index') }}" class="btn btn-light btn-modern">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Form Section -->
        <div class="col-lg-8">
            <div class="form-modern">
                @if(in_array(Auth::user()->role, ['admin', 'kades']))
                    <div class="admin-badge">
                        <i class="fas fa-crown mr-2"></i>
                        Mode Administrator - Dapat generate kas untuk semua RT
                    </div>
                @endif

                @if($errors->any())
                    <div class="error-message">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Terjadi Kesalahan!</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('kas.generate-weekly') }}" method="POST" id="generateForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rt_id">
                                    <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                    RT/RW <span class="text-danger">*</span>
                                </label>
                                
                                @if($rtList->count() > 10)
                                    <input type="text" id="rtSearch" class="form-control mb-3" 
                                           placeholder="🔍 Cari RT atau RW...">
                                @endif
                                
                                <select name="rt_id" id="rt_id" class="form-control" required>
                                    <option value="">Pilih RT</option>
                                    @foreach($rtList as $rt)
                                        <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
                                            RT {{ $rt->no_rt ?? $rt->nama_rt }} / RW {{ $rt->rw->no_rw ?? $rt->rw->nama_rw }}
                                            @if($rt->rw->desa ?? false)
                                                - {{ $rt->rw->desa->nama_desa }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah">
                                    <i class="fas fa-money-bill text-success mr-2"></i>
                                    Jumlah Kas per Minggu <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" name="jumlah" id="jumlah" class="form-control" 
                                           value="{{ old('jumlah', 10000) }}" min="1000" step="1000" required>
                                </div>
                                <small class="form-text text-muted">Minimal Rp 1.000 per minggu</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tahun">
                                    <i class="fas fa-calendar text-warning mr-2"></i>
                                    Tahun <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="tahun" id="tahun" class="form-control" 
                                       value="{{ old('tahun', now()->year) }}" min="2020" max="2030" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="minggu_mulai">
                                    <i class="fas fa-play text-success mr-2"></i>
                                    Minggu Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="minggu_mulai" id="minggu_mulai" class="form-control" 
                                       value="{{ old('minggu_mulai', now()->weekOfYear) }}" min="1" max="52" required>
                                <small class="form-text text-muted">Minggu ke-{{ now()->weekOfYear }} (saat ini)</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="minggu_selesai">
                                    <i class="fas fa-stop text-danger mr-2"></i>
                                    Minggu Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="minggu_selesai" id="minggu_selesai" class="form-control" 
                                       value="{{ old('minggu_selesai', now()->weekOfYear + 3) }}" min="1" max="52" required>
                                <small class="form-text text-muted">Maksimal minggu ke-52</small>
                            </div>
                        </div>
                    </div>

                    <!-- Week Range Preview -->
                    <div class="week-range" id="weekRange" style="display: none;">
                        <h5 class="font-weight-bold mb-3"><i class="fas fa-calendar-alt mr-2 text-primary"></i>Rentang Minggu</h5>
                        <div id="weekRangeText"></div>
                    </div>

                    <!-- Calculation Preview -->
                    <div class="calculation-preview" id="calculationPreview" style="display: none;">
                        <h5 class="font-weight-bold mb-3"><i class="fas fa-calculator mr-2 text-success"></i>Perkiraan Kas yang Akan Dibuat</h5>
                        <div id="calculationText"></div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="send_notification" id="send_notification" 
                                   value="1" checked class="custom-control-input">
                            <label for="send_notification" class="custom-control-label">
                                <i class="fas fa-bell text-primary mr-2"></i>
                                Kirim notifikasi ke warga yang memiliki akun
                            </label>
                        </div>
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-success btn-modern mr-3">
                            <i class="fas fa-magic mr-2"></i>Generate Kas Mingguan
                        </button>
                        <a href="{{ route('kas.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Section -->
        <div class="col-lg-4">
            <div class="info-card">
                <h6><i class="fas fa-info-circle mr-2"></i>Cara Kerja Generate Mingguan</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3"><i class="fas fa-check mr-2"></i>Pilih RT dan rentang minggu</li>
                    <li class="mb-3"><i class="fas fa-check mr-2"></i>Sistem akan membuat kas untuk setiap minggu</li>
                    <li class="mb-3"><i class="fas fa-check mr-2"></i>Kas dibuat untuk semua penduduk aktif</li>
                    <li class="mb-3"><i class="fas fa-check mr-2"></i>Tanggal jatuh tempo otomatis dihitung</li>
                    <li class="mb-0"><i class="fas fa-check mr-2"></i>Notifikasi dikirim ke setiap warga</li>
                </ul>
            </div>

            <div class="warning-card">
                <h6><i class="fas fa-exclamation-triangle mr-2"></i>Perhatian Penting</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3"><i class="fas fa-exclamation mr-2"></i>Kas duplikat akan diabaikan</li>
                    <li class="mb-3"><i class="fas fa-exclamation mr-2"></i>Pastikan rentang minggu benar</li>
                    <li class="mb-3"><i class="fas fa-exclamation mr-2"></i>Proses tidak dapat dibatalkan</li>
                    <li class="mb-0"><i class="fas fa-exclamation mr-2"></i>Backup data sebelum generate</li>
                </ul>
            </div>

            <div class="preview-card" id="summaryCard" style="display: none;">
                <h6 class="font-weight-bold mb-3"><i class="fas fa-chart-bar mr-2 text-primary"></i>Ringkasan</h6>
                <div id="summaryContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="success-modal" id="successModal">
    <div class="success-modal-content">
        <div class="success-icon">
            <i class="fas fa-magic"></i>
        </div>
        <h3 class="text-success font-weight-bold mb-3">Kas Mingguan Berhasil Digenerate!</h3>
        <p class="text-muted mb-4" id="successMessage">Kas mingguan telah berhasil dibuat untuk periode yang dipilih.</p>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number text-primary" id="totalWeeks">0</div>
                <div class="stat-label">Minggu</div>
            </div>
            <div class="stat-item">
                <div class="stat-number text-success" id="totalCreated">0</div>
                <div class="stat-label">Kas Dibuat</div>
            </div>
            <div class="stat-item">
                <div class="stat-number text-info" id="totalNotifications">0</div>
                <div class="stat-label">Notifikasi</div>
            </div>
            <div class="stat-item">
                <div class="stat-number text-warning" id="totalAmount">Rp 0</div>
                <div class="stat-label">Total Nilai</div>
            </div>
        </div>
        
        <div class="d-flex justify-content-center">
            <button class="btn btn-primary btn-modern mr-3" onclick="generateAnother()">
                <i class="fas fa-plus mr-2"></i>Generate Lagi
            </button>
            <button class="btn btn-success btn-modern" onclick="viewKasList()">
                <i class="fas fa-list mr-2"></i>Lihat Daftar Kas
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Check for success message from session
    @if(session('success'))
        showSuccessModal({
            message: "{{ session('success') }}",
            weeks: {{ session('total_weeks', 0) }},
            created: {{ session('kas_created', 0) }},
            notifications: {{ session('notifications_sent', 0) }},
            amount: {{ session('total_amount', 0) }}
        });
    @endif

    // RT Search functionality
    $('#rtSearch').on('keyup', function() {
        let searchTerm = $(this).val().toLowerCase();
        $('#rt_id option').each(function() {
            let optionText = $(this).text().toLowerCase();
            if (optionText.includes(searchTerm) || $(this).val() === '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Update preview when inputs change
    function updatePreview() {
        let mingguMulai = parseInt($('#minggu_mulai').val()) || 0;
        let mingguSelesai = parseInt($('#minggu_selesai').val()) || 0;
        let jumlah = parseInt($('#jumlah').val()) || 0;
        let tahun = $('#tahun').val();
        let rtText = $('#rt_id option:selected').text();

        if (mingguMulai && mingguSelesai && mingguMulai <= mingguSelesai) {
            let totalMinggu = mingguSelesai - mingguMulai + 1;
            
            // Show week range
            $('#weekRange').show();
            $('#weekRangeText').html(`
                <h4 class="font-weight-bold text-primary">Minggu ${mingguMulai} - ${mingguSelesai} Tahun ${tahun}</h4>
                <p class="mb-0 text-muted font-weight-600">Total: ${totalMinggu} minggu</p>
            `);

            // Show calculation preview
            if (jumlah > 0) {
                $('#calculationPreview').show();
                $('#calculationText').html(`
                    <div class="row text-center">
                        <div class="col-3">
                            <h4 class="font-weight-bold text-primary">${totalMinggu}</h4>
                            <small class="text-muted font-weight-600">Minggu</small>
                        </div>
                        <div class="col-3">
                            <h4 class="font-weight-bold text-success">Rp ${new Intl.NumberFormat('id-ID').format(jumlah)}</h4>
                            <small class="text-muted font-weight-600">Per Minggu</small>
                        </div>
                        <div class="col-3">
                            <h4 class="font-weight-bold text-info">Rp ${new Intl.NumberFormat('id-ID').format(jumlah * totalMinggu)}</h4>
                            <small class="text-muted font-weight-600">Total per Orang</small>
                        </div>
                        <div class="col-3">
                            <h4 class="font-weight-bold text-warning">~${totalMinggu * 50}</h4>
                            <small class="text-muted font-weight-600">Est. Total Kas</small>
                        </div>
                    </div>
                `);
            }

            // Show summary
            if (rtText !== 'Pilih RT') {
                $('#summaryCard').show();
                $('#summaryContent').html(`
                    <div class="text-center">
                        <p class="mb-2"><strong>RT:</strong> ${rtText}</p>
                        <p class="mb-2"><strong>Periode:</strong> ${totalMinggu} minggu</p>
                        <p class="mb-2"><strong>Total per Orang:</strong> Rp ${new Intl.NumberFormat('id-ID').format(jumlah * totalMinggu)}</p>
                        <p class="mb-0"><strong>Status:</strong> <span class="text-success font-weight-bold">✓ Siap Generate</span></p>
                    </div>
                `);
            }
        } else {
            $('#weekRange, #calculationPreview, #summaryCard').hide();
        }
    }

    // Bind events
    $('#minggu_mulai, #minggu_selesai, #jumlah, #tahun, #rt_id').on('change input', updatePreview);

    // Validation for week range
    $('#minggu_selesai').on('change', function() {
        let mingguMulai = parseInt($('#minggu_mulai').val()) || 0;
        let mingguSelesai = parseInt($(this).val()) || 0;
        
        if (mingguSelesai < mingguMulai) {
            showErrorAlert('Minggu selesai tidak boleh lebih kecil dari minggu mulai');
            $(this).val(mingguMulai);
            updatePreview();
        }
    });

    $('#minggu_mulai').on('change', function() {
        let mingguMulai = parseInt($(this).val()) || 0;
        let mingguSelesai = parseInt($('#minggu_selesai').val()) || 0;
        
        if (mingguMulai > mingguSelesai) {
            $('#minggu_selesai').val(mingguMulai);
            updatePreview();
        }
    });

    // Form validation before submit
    $('#generateForm').on('submit', function(e) {
        let rtId = $('#rt_id').val();
        let mingguMulai = parseInt($('#minggu_mulai').val()) || 0;
        let mingguSelesai = parseInt($('#minggu_selesai').val()) || 0;
        let jumlah = parseInt($('#jumlah').val()) || 0;
        let tahun = $('#tahun').val();
        
        if (!rtId) {
            showErrorAlert('Silakan pilih RT terlebih dahulu');
            e.preventDefault();
            return false;
        }
        
        if (mingguMulai > mingguSelesai) {
            showErrorAlert('Minggu mulai tidak boleh lebih besar dari minggu selesai');
            e.preventDefault();
            return false;
        }
        
        if (jumlah < 1000) {
            showErrorAlert('Jumlah kas minimal Rp 1.000');
            e.preventDefault();
            return false;
        }
        
        let totalMinggu = mingguSelesai - mingguMulai + 1;
        let rtText = $('#rt_id option:selected').text();
        
        // Confirmation dialog
        let message = `Apakah Anda yakin ingin generate kas mingguan?\n\n`;
        message += `RT: ${rtText}\n`;
        message += `Periode: Minggu ${mingguMulai} - ${mingguSelesai} (${totalMinggu} minggu)\n`;
        message += `Jumlah per minggu: Rp ${new Intl.NumberFormat('id-ID').format(jumlah)}\n`;
        message += `Total per orang: Rp ${new Intl.NumberFormat('id-ID').format(jumlah * totalMinggu)}\n\n`;
        message += `Kas akan dibuat untuk semua penduduk aktif di RT tersebut.`;
        
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Generating...');
    });

    // Format currency input
    $('#jumlah').on('input', function() {
        let value = $(this).val();
        if (value) {
            let formatted = new Intl.NumberFormat('id-ID').format(value);
            $(this).siblings('.input-group-prepend').find('.input-group-text').text('Rp');
        }
    });

    // Initial preview update
    updatePreview();
});

function showSuccessModal(data) {
    $('#successMessage').text(data.message);
    $('#totalWeeks').text(data.weeks);
    $('#totalCreated').text(data.created);
    $('#totalNotifications').text(data.notifications);
    $('#totalAmount').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.amount));
    
    $('#successModal').css('display', 'flex');
    createConfetti();
}

function createConfetti() {
    const colors = ['#667eea', '#764ba2', '#4facfe', '#00f2fe', '#51cf66', '#f093fb'];
    
    for (let i = 0; i < 100; i++) {
        setTimeout(() => {
            const confetti = $('<div class="confetti"></div>');
            confetti.css({
                left: Math.random() * 100 + '%',
                backgroundColor: colors[Math.floor(Math.random() * colors.length)],
                animationDelay: Math.random() * 3 + 's'
            });
            $('.success-modal-content').append(confetti);
            
            setTimeout(() => confetti.remove(), 3000);
        }, i * 50);
    }
}

function generateAnother() {
    $('#successModal').hide();
    $('form')[0].reset();
    $('#weekRange, #calculationPreview, #summaryCard').hide();
    $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-magic mr-2"></i>Generate Kas Mingguan');
}

function viewKasList() {
    window.location.href = '{{ route("kas.index") }}';
}

function showErrorAlert(message) {
    const alert = $(`
        <div class="error-message">
            <h6><i class="fas fa-exclamation-triangle mr-2"></i>Error!</h6>
            <p class="mb-0">${message}</p>
        </div>
    `);
    
    $('.form-modern').prepend(alert);
    
    setTimeout(() => {
        alert.fadeOut(() => alert.remove());
    }, 5000);
}
</script>
@endpush
