@extends('layouts.auth')

@section('style')
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: #090b13;
        min-height: 100vh;
    }
    .reset-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: #090b13;
    }
    .reset-card {
        background: linear-gradient(135deg, #0f1120 0%, #151929 100%);
        border-radius: 18px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        padding: 2.5rem 2rem;
        width: 100%;
        max-width: 420px;
        color: #fff;
        position: relative;
    }
    .reset-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .reset-header h2 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }
    .reset-header p {
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
    .btn-reset {
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
    .btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    .btn-reset:disabled {
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
    .password-container {
        position: relative;
    }
    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        transition: color 0.3s ease;
    }
    .password-toggle:hover {
        color: #d1d5db;
    }
    @media (max-width: 600px) {
        .reset-card {
            padding: 1.5rem 1rem;
            margin: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="reset-container">
    <div class="reset-card">
        <div class="reset-header">
            <h2>Reset Password</h2>
            <p>Masukkan password baru Anda</p>
        </div>

        @if (session('status'))
            <div class="success-message">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email" class="form-label">Email <span style="color:#3b82f6">*</span></label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Masukkan alamat email Anda">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password Baru <span style="color:#3b82f6">*</span></label>
                <div class="password-container">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Masukkan password baru">
                    <button type="button" class="password-toggle" onclick="togglePassword('password', 'password-icon')">
                        <i class="bi bi-eye" id="password-icon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm" class="form-label">Konfirmasi Password Baru <span style="color:#3b82f6">*</span></label>
                <div class="password-container">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru">
                    <button type="button" class="password-toggle" onclick="togglePassword('password-confirm', 'password-confirm-icon')">
                        <i class="bi bi-eye" id="password-confirm-icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-reset" id="btn-reset">
                <span id="reset-text">Reset Password</span>
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem;">
            <p style="color: #9ca3af; font-size: 0.875rem;">
                Ingat password Anda?
                <a href="{{ route('login') }}" style="color: #3b82f6; text-decoration: none;">Login di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const passwordIcon = document.getElementById(iconId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.className = 'bi bi-eye-slash';
        } else {
            passwordInput.type = 'password';
            passwordIcon.className = 'bi bi-eye';
        }
    }

    // Handle form submission with loading spinner
    document.getElementById('btn-reset').addEventListener('click', function(e) {
        const btnReset = document.getElementById('btn-reset');
        const resetText = document.getElementById('reset-text');

        // Show loading
        btnReset.disabled = true;
        resetText.innerHTML = '<div class="loading-spinner"></div>Mengatur Ulang...';
    });
</script>
@endsection
