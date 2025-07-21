@extends('layouts.auth')

@section('style')
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: #090b13;
        min-height: 100vh;
    }
    .otp-verify-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: #090b13;
    }
    .otp-verify-card {
        background: linear-gradient(135deg, #0f1120 0%, #151929 100%);
        border-radius: 18px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        padding: 2.5rem 2rem;
        width: 100%;
        max-width: 420px;
        color: #fff;
        position: relative;
    }
    .otp-verify-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .otp-verify-header h2 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }
    .otp-verify-header p {
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
    .password-input-group {
        position: relative;
    }
    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #9ca3af;
        font-size: 1.1rem;
    }
    .btn-verify-otp {
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
    .btn-verify-otp:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    .btn-verify-otp:disabled {
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
    @media (max-width: 600px) {
        .otp-verify-card {
            padding: 1.5rem 1rem;
            margin: 1rem;
        }
    }
    .hidden {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="otp-verify-container">
    <div class="otp-verify-card">
        <div class="otp-verify-header">
            <h2>Verifikasi OTP & Reset Password</h2>
            <p>Masukkan kode OTP yang telah dikirim ke email Anda dan password baru.</p>
        </div>

        @if (session('status'))
            <div class="success-message">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" id="otpVerifyForm">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email <span style="color:#3b82f6">*</span></label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Alamat email Anda">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="otp" class="form-label">Kode OTP <span style="color:#3b82f6">*</span></label>
                <input id="otp" type="text" class="form-control @error('otp') is-invalid @enderror" name="otp" required autocomplete="off" placeholder="Masukkan 6 digit kode OTP">
                @error('otp')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password Baru <span style="color:#3b82f6">*</span></label>
                <div class="password-input-group">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                    <span class="toggle-password" onclick="togglePasswordVisibility('password')">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm" class="form-label">Konfirmasi Password Baru <span style="color:#3b82f6">*</span></label>
                <div class="password-input-group">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru">
                    <span class="toggle-password" onclick="togglePasswordVisibility('password-confirm')">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn-verify-otp" id="submitButton">
                <span id="buttonText">{{ __('Reset Password') }}</span>
                <div id="spinner" class="loading-spinner hidden"></div>
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem;">
            <p style="color: #9ca3af; font-size: 0.875rem;">
                <a href="{{ route('login') }}" style="color: #3b82f6; text-decoration: none;">Kembali ke halaman login</a>
            </p>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function togglePasswordVisibility(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('otpVerifyForm');
        const submitButton = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');
        const spinner = document.getElementById('spinner');

        form.addEventListener('submit', function() {
            submitButton.disabled = true;
            buttonText.classList.add('hidden');
            spinner.classList.remove('hidden');
        });
    });
</script>
@endsection
