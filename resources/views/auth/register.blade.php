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
        }
        .btn-register:hover, .btn-nik:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
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
        @media (max-width: 600px) {
            .register-card {
                padding: 1.5rem 0.5rem;
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
        <div id="nik-section">
            <div class="form-group">
                <label for="nik" class="form-label">NIK <span style="color:#3b82f6">*</span></label>
                <input id="nik" type="text" class="form-control" maxlength="16" placeholder="Masukkan 16 digit NIK" autocomplete="off">
                <div id="nik-error" class="error-message" style="display:none;"></div>
            </div>
            <button class="btn-nik" type="button" onclick="cekNik()">Cek NIK</button>
        </div>
        <form id="register-form" method="POST" action="{{ route('register') }}" style="display:none;">
            @csrf
            <input type="hidden" name="nik" id="hidden-nik">
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
            <button type="submit" class="btn-register">Register</button>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
function cekNik() {
    const nikInput = document.getElementById('nik');
    const nikError = document.getElementById('nik-error');
    const nik = nikInput.value.trim();
    if (!/^\d{16}$/.test(nik)) {
        nikError.textContent = 'NIK harus 16 digit angka.';
        nikError.style.display = 'block';
        return;
    }
    nikError.style.display = 'none';
    document.getElementById('register-form').style.display = 'block';
    document.getElementById('nik-section').style.display = 'none';
    document.getElementById('hidden-nik').value = nik;
}
</script>
@endsection
