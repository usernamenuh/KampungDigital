@extends('layouts.auth')

@section('style')
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: #090b13;
        min-height: 100vh;
    }
    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: #090b13;
    }
    .register-card {
        background: linear-gradient(135deg, #0f1120 0%, #151929 100%);
        border-radius: 18px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        padding: 2.5rem 2rem;
        width: 100%;
        max-width: 420px;
        color: #fff;
        position: relative;
    }
    .register-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .register-header h2 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }
    .register-header p {
        color: #9ca3af;
        font-size: 1rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #d1d5db;
        font-weight: 500;
        font-size: 0.95rem;
    }
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background: #13151f;
        border: 1px solid #2a2d3a;
        border-radius: 8px;
        color: #fff;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: #1a1d2b;
    }
    .form-control::placeholder {
        color: #bfc9db;
    }
    .form-control:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .btn-register, .btn-nik {
        width: 100%;
        padding: 0.75rem;
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        border: none;
        border-radius: 8px;
        color: white;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-register:hover, .btn-nik:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    .btn-register:disabled, .btn-nik:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .error-message {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #f87171;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        margin-bottom: 1rem;
    }
    .success-message {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: #4ade80;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        margin-bottom: 1rem;
    }
    .loading-spinner {
        width: 20px;
        height: 20px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 8px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .back-btn {
        background: rgba(107, 114, 128, 0.2);
        color: #9ca3af;
        border: 1px solid rgba(107, 114, 128, 0.3);
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }
    .back-btn:hover {
        background: rgba(107, 114, 128, 0.3);
        color: #d1d5db;
    }
    @media (max-width: 600px) {
        .register-card {
            padding: 1.5rem 1rem;
            margin: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="register-container">
<div class="register-card">
    <div class="register-header">
        <h2>Register</h2>
        <p>Buat akun baru untuk Kampung Digital</p>
    </div>

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    <!-- NIK Validation Section -->
    <div id="nik-section" style="{{ old('nik') && !$errors->has('nik') ? 'display:none;' : 'display:block;' }}">
        <div class="form-group">
            <label for="nik" class="form-label">NIK <span style="color:#3b82f6">*</span></label>
            <input id="nik" type="text" class="form-control" maxlength="16" placeholder="Masukkan 16 digit NIK" autocomplete="off" value="{{ old('nik') }}">
            <div id="nik-error" class="error-message" style="display:none;"></div>
            <div id="nik-success" class="success-message" style="display:none;"></div>
        </div>
        <button class="btn-nik" type="button" onclick="cekNik()" id="btn-cek-nik">
            <span id="btn-text">Cek NIK</span>
        </button>
    </div>

    <!-- Registration Form -->
    <form id="register-form" method="POST" action="{{ route('register') }}" style="{{ old('nik') && !$errors->has('nik') ? 'display:block;' : 'display:none;' }}">
        @csrf
        <input type="hidden" name="nik" id="hidden-nik" value="{{ old('nik') }}">
        
        <button type="button" class="back-btn" onclick="backToNik()">
            ← Kembali ke Input NIK
        </button>

        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap <span style="color:#3b82f6">*</span></label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Masukkan nama lengkap">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email <span style="color:#3b82f6">*</span></label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Masukkan email">
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password <span style="color:#3b82f6">*</span></label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Masukkan password">
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm" class="form-label">Konfirmasi Password <span style="color:#3b82f6">*</span></label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password">
        </div>

        <button type="submit" class="btn-register" id="btn-register">
            <span id="register-text">Register</span>
        </button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem;">
        <p style="color: #9ca3af; font-size: 0.875rem;">
            Sudah punya akun? 
            <a href="{{ route('login') }}" style="color: #3b82f6; text-decoration: none;">Login di sini</a>
        </p>
    </div>
</div>
</div>
@endsection

@section('script')
<script>
let nikValidated = false;
let pendudukData = null;

document.addEventListener('DOMContentLoaded', function() {
    // If there are validation errors from a previous registration attempt,
    // and old('nik') exists, it means the registration form was shown.
    // We should then show the registration form directly.
    @if($errors->any() && old('nik'))
        document.getElementById('nik-section').style.display = 'none';
        document.getElementById('register-form').style.display = 'block';
        nikValidated = true; // Assume NIK was validated if form is shown
    @else
        // Default state: show NIK section
        document.getElementById('nik-section').style.display = 'block';
        document.getElementById('register-form').style.display = 'none';
    @endif
});


function cekNik() {
    const nikInput = document.getElementById('nik');
    const nikError = document.getElementById('nik-error');
    const nikSuccess = document.getElementById('nik-success');
    const btnCekNik = document.getElementById('btn-cek-nik');
    const btnText = document.getElementById('btn-text');
    const nik = nikInput.value.trim();

    // Reset messages
    nikError.style.display = 'none';
    nikSuccess.style.display = 'none';

    // Validate NIK format
    if (!/^\d{16}$/.test(nik)) {
        nikError.textContent = 'NIK harus 16 digit angka.';
        nikError.style.display = 'block';
        return;
    }

    // Show loading
    btnCekNik.disabled = true;
    btnText.innerHTML = '<div class="loading-spinner"></div>Memvalidasi...';

    // Send AJAX request
    fetch('{{ route('check-nik') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ nik: nik })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error(`Server returned non-JSON response: ${text}`);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            nikSuccess.textContent = data.message;
            nikSuccess.style.display = 'block';
            pendudukData = data.data;
            nikValidated = true;
            
            // Show registration form after delay
            setTimeout(() => {
                showRegistrationForm();
            }, 1000);
        } else {
            nikError.textContent = data.message;
            nikError.style.display = 'block';
            nikValidated = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        nikError.textContent = 'Terjadi kesalahan saat memvalidasi NIK. Silakan coba lagi. Detail: ' + error.message;
        nikError.style.display = 'block';
        nikValidated = false;
    })
    .finally(() => {
        btnCekNik.disabled = false;
        btnText.textContent = 'Cek NIK';
    });
}

function showRegistrationForm() {
    document.getElementById('nik-section').style.display = 'none';
    document.getElementById('register-form').style.display = 'block';
    document.getElementById('hidden-nik').value = document.getElementById('nik').value;

    // Pre-fill name if available
    if (pendudukData && pendudukData.nama_lengkap) {
        document.getElementById('name').value = pendudukData.nama_lengkap;
    }
}

function backToNik() {
    document.getElementById('register-form').style.display = 'none';
    document.getElementById('nik-section').style.display = 'block';
    nikValidated = false;
    pendudukData = null;
    // Clear any previous messages
    document.getElementById('nik-error').style.display = 'none';
    document.getElementById('nik-success').style.display = 'none';
}

// Handle form submission for registration
document.getElementById('register-form').addEventListener('submit', function(e) {
    const btnRegister = document.getElementById('btn-register');
    const registerText = document.getElementById('register-text');

    if (!nikValidated) {
        e.preventDefault();
        alert('Silakan validasi NIK terlebih dahulu.');
        return;
    }

    // Show loading
    btnRegister.disabled = true;
    registerText.innerHTML = '<div class="loading-spinner"></div>Mendaftar...';
});

// Handle Enter key on NIK input
document.getElementById('nik').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        cekNik();
    }
});

</script>
@endsection
