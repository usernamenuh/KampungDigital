@extends('layouts.auth')

@section('style')
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: #090b13;
        min-height: 100vh;
    }
    .reset-email-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: #090b13;
    }
    .reset-email-card {
        background: linear-gradient(135deg, #0f1120 0%, #151929 100%);
        border-radius: 18px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        padding: 2.5rem 2rem;
        width: 100%;
        max-width: 420px;
        color: #fff;
        position: relative;
    }
    .reset-email-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .reset-email-header h2 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }
    .reset-email-header p {
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
    .btn-send-link {
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
    .btn-send-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    .btn-send-link:disabled {
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
        .reset-email-card {
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
<div class="reset-email-container">
    <div class="reset-email-card">
        <div class="reset-email-header">
            <h2>Reset Password</h2>
            <p>Masukkan email Anda untuk menerima kode OTP.</p>
        </div>

        @if (session('status'))
            <div class="success-message">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email <span style="color:#3b82f6">*</span></label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Masukkan alamat email Anda">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-send-link" id="submitButton">
                <span id="buttonText">{{ __('Kirim Kode OTP') }}</span>
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('forgotPasswordForm');
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
