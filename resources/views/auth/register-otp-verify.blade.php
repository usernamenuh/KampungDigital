@extends('layouts.auth')

@section('style')
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: #090b13;
        min-height: 100vh;
    }
    .otp-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: #090b13;
    }
    .otp-card {
        background: linear-gradient(135deg, #0f1120 0%, #151929 100%);
        border-radius: 18px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        padding: 2.5rem 2rem;
        width: 100%;
        max-width: 420px;
        color: #fff;
        position: relative;
    }
    .otp-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .otp-header h2 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }
    .otp-header p {
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
    .btn-otp {
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
    .btn-otp:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    .btn-otp:disabled {
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
        .otp-card {
            padding: 1.5rem 1rem;
            margin: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="otp-container">
    <div class="otp-card">
        <div class="otp-header">
            <h2>Verifikasi Akun</h2>
            <p>Masukkan kode OTP yang telah dikirim ke email Anda.</p>
        </div>

        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.otp.verify') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="otp" class="form-label">Kode OTP <span style="color:#3b82f6">*</span></label>
                <p style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 1rem;">
                    Kode telah dikirim ke: <span style="font-weight: 600; color: #fff;">{{ $email }}</span>
                </p>
                <input id="otp" type="text" class="form-control @error('otp') is-invalid @enderror" name="otp" required autocomplete="off" maxlength="6" placeholder="Masukkan 6 digit kode OTP">
            </div>

            <button type="submit" class="btn-otp" id="btn-verify-otp">
                <span id="otp-btn-text">Verifikasi OTP</span>
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem;">
            <p style="color: #9ca3af; font-size: 0.875rem;">
                Tidak menerima kode? 
                <button type="button" onclick="resendOtp('{{ $email }}')" style="background: none; border: none; color: #3b82f6; text-decoration: none; cursor: pointer; font-size: 0.875rem; padding: 0;" id="btn-resend-otp">
                    Kirim Ulang OTP
                </button>
            </p>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function resendOtp(email) {
    const btnResendOtp = document.getElementById('btn-resend-otp');
    const originalText = btnResendOtp.textContent;
    const otpError = document.querySelector('.error-message');
    const otpSuccess = document.querySelector('.success-message');

    // Clear previous messages
    if (otpError) otpError.style.display = 'none';
    if (otpSuccess) otpSuccess.style.display = 'none';

    btnResendOtp.disabled = true;
    btnResendOtp.innerHTML = '<div class="loading-spinner" style="margin-right: 5px;"></div> Mengirim ulang...';

    fetch('{{ route('register.otp.resend') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayMessage(data.message, 'success');
        } else {
            displayMessage(data.message || 'Gagal mengirim ulang OTP.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayMessage('Terjadi kesalahan saat mengirim ulang OTP. Silakan coba lagi.', 'error');
    })
    .finally(() => {
        btnResendOtp.disabled = false;
        btnResendOtp.textContent = originalText;
    });
}

function displayMessage(message, type) {
    let messageDiv = document.querySelector(`.${type}-message`);
    if (!messageDiv) {
        messageDiv = document.createElement('div');
        messageDiv.className = `${type}-message`;
        document.querySelector('.otp-card').insertBefore(messageDiv, document.querySelector('form'));
    }
    messageDiv.textContent = message;
    messageDiv.style.display = 'block';
    setTimeout(() => messageDiv.style.display = 'none', 5000); // Hide after 5 seconds
}

// Handle Enter key on OTP input
document.getElementById('otp').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('btn-verify-otp').click();
    }
});
</script>
@endsection
