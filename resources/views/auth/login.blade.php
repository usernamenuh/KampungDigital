@extends('layouts.auth')
@section('style')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow: hidden;
            background: #090b13;
        }

        .login-container {
            display: flex;
            height: 100vh;
            background: #090b13;
        }

        /* Left Side - Animated Content */
        .left-side {
            flex: 1;
            background: linear-gradient(135deg, #0f1120 0%, #151929 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-right: 1px solid #1f2130;
        }

        .animated-background {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }

        .dot-map {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0.3;
        }

        .dot {
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            animation: twinkle 3s ease-in-out infinite;
        }

        .route-line {
            position: absolute;
            height: 1px;
            background: linear-gradient(90deg, transparent, #3b82f6, transparent);
            animation: routeMove 4s ease-in-out infinite;
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .floating-circle:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-circle:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .floating-circle:nth-child(3) {
            width: 80px;
            height: 80px;
            top: 40%;
            left: 20%;
            animation-delay: 4s;
        }

        .content-overlay {
            position: relative;
            z-index: 10;
            text-align: center;
            color: white;
            padding: 2rem;
        }

        .logo-container {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: pulse 2s ease-in-out infinite;
        }

        .brand-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            animation: slideInLeft 1s ease-out;
        }

        .brand-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.7);
            max-width: 400px;
            line-height: 1.6;
            animation: slideInLeft 1s ease-out 0.2s both;
        }

        .scrolling-text {
            position: absolute;
            bottom: 10%;
            left: 0;
            right: 0;
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.5);
            white-space: nowrap;
            animation: scrollText 20s linear infinite;
        }

        /* Right Side - Login Form */
        .right-side {
            flex: 1;
            background: #090b13;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: white;
        }

        .login-form-container {
            width: 100%;
            max-width: 400px;
            animation: slideInRight 1s ease-out;
        }

        .login-header {
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: white;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #9ca3af;
            font-size: 1rem;
        }

        .google-btn {
            width: 100%;
            background: #13151f;
            border: 1px solid #2a2d3a;
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .google-btn:hover {
            background: #1a1d2b;
            color: white;
            transform: translateY(-1px);
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #2a2d3a;
        }

        .divider span {
            background: #090b13;
            padding: 0 1rem;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #d1d5db;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .required {
            color: #3b82f6;
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
            color: #6b7280;
        }

        /* Fix autocomplete styling */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus,
        .form-control:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #13151f inset !important;
            -webkit-text-fill-color: #fff !important;
            transition: background-color 5000s ease-in-out 0s;
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

        .btn-login {
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
            position: relative;
            overflow: hidden;
            margin-top: 1rem;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #60a5fa;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            animation: shake 0.5s ease-in-out;
        }

        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            animation: slideInDown 0.5s ease-out;
        }

        /* Animations */
        @keyframes twinkle {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 1; }
        }

        @keyframes routeMove {
            0% { transform: translateX(-100%); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateX(100%); opacity: 0; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes scrollText {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
                max-width: 100vw;
            }
            .left-side {
                height: 24vh;
                min-height: 90px;
                flex: none;
                border-right: none;
                border-bottom: 1px solid #1f2130;
                justify-content: flex-start;
                padding-top: 1rem;
                padding-bottom: 0.5rem;
            }
            .right-side {
                height: auto;
                min-height: 76vh;
                flex: none;
                padding: 0.5rem 0.2rem 1rem 0.2rem;
                justify-content: flex-start;
                align-items: flex-start;
            }
            .login-form-container {
                max-width: 100%;
                width: 100%;
                margin: 0 auto;
                padding: 0 0.1rem;
            }
            .brand-title {
                font-size: 1.1rem;
                margin-bottom: 0.3rem;
                text-align: left;
            }
            .brand-subtitle {
                font-size: 0.75rem;
                text-align: left;
                margin-bottom: 0.2rem;
            }
            .logo-container {
                width: 32px;
                height: 32px;
                margin-bottom: 0.5rem;
            }
            .login-header {
                margin-bottom: 1rem;
                text-align: left;
            }
            .login-header h2 {
                font-size: 1.1rem;
                margin-bottom: 0.2rem;
                text-align: left;
            }
            .login-header p {
                font-size: 0.8rem;
                text-align: left;
            }
            .form-label, .form-control, .btn-login {
                font-size: 0.85rem;
                text-align: left;
            }
            .form-group {
                margin-bottom: 1rem;
            }
            .btn-login {
                padding: 0.5rem;
                font-size: 0.95rem;
            }
            .forgot-link {
                font-size: 0.75rem;
            }
            .google-btn {
                font-size: 0.85rem;
                padding: 0.5rem;
            }
            .divider span {
                font-size: 0.75rem;
            }
            .content-overlay {
                padding: 0.5rem 0.2rem;
                text-align: center !important;
                align-items: center !important;
                justify-content: center !important;
                display: flex;
                flex-direction: column;
            }
            .brand-title,
            .brand-subtitle {
                text-align: center !important;
                width: 100%;
            }
            .logo-container {
                margin-left: auto;
                margin-right: auto;
                display: flex;
                justify-content: center;
            }
        }
        @media (max-width: 480px) {
            .left-side {
                height: 18vh;
                min-height: 60px;
                padding-top: 0.5rem;
            }
            .brand-title {
                font-size: 0.95rem;
            }
            .brand-subtitle {
                font-size: 0.65rem;
            }
            .login-header h2 {
                font-size: 0.95rem;
            }
            .login-header p {
                font-size: 0.7rem;
            }
            .login-form-container {
                padding: 0 0.05rem;
            }
            .content-overlay {
                padding: 0.3rem 0.1rem;
                text-align: center !important;
                align-items: center !important;
                justify-content: center !important;
                display: flex;
                flex-direction: column;
            }
            .brand-title,
            .brand-subtitle {
                text-align: center !important;
                width: 100%;
            }
            .logo-container {
                margin-left: auto;
                margin-right: auto;
                display: flex;
                justify-content: center;
            }
        }
    </style>
@endsection
@section('content')
    <div class="login-container">
        <!-- Left Side - Animated Content -->
        <div class="left-side">
            <div style="position: absolute; top: 2rem; left: 2rem; z-index: 20;">
                <a href="{{ url('/') }}" style="display: inline-block;">
                    <img src="{{ asset('public/assets/sa.webp') }}" alt="Logo" style="width: 48px; height: 48px; border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.15); background: white; object-fit: cover;">
                </a>
            </div>
            <div class="animated-background">
                <!-- Dot Map -->
                <div class="dot-map">
                    <!-- Generate dots with PHP -->
                    <?php for($i = 0; $i < 50; $i++): ?>
                        <div class="dot" style="
                            top: <?= rand(10, 90) ?>%;
                            left: <?= rand(10, 90) ?>%;
                            animation-delay: <?= rand(0, 3000) ?>ms;
                        "></div>
                    <?php endfor; ?>

                    <!-- Route Lines -->
                    <div class="route-line" style="top: 30%; left: 20%; width: 200px; animation-delay: 0s;"></div>
                    <div class="route-line" style="top: 60%; left: 40%; width: 150px; animation-delay: 1s;"></div>
                    <div class="route-line" style="top: 45%; left: 10%; width: 180px; animation-delay: 2s;"></div>
                </div>

                <!-- Floating Elements -->
                <div class="floating-elements">
                    <div class="floating-circle"></div>
                    <div class="floating-circle"></div>
                    <div class="floating-circle"></div>
                </div>
            </div>

            <div class="content-overlay">
                <div class="logo-container">
                    <i class="bi bi-airplane" style="font-size: 1.5rem;"></i>
                </div>
                <h2 class="brand-title">Kampung Digital</h2>
                <p class="brand-subtitle">
                    Menuju Indonesia Yang Lebih Maju Dengan Teknologi.<br>
                    Dimulai Dari Akar Untuk Menciptakan Sesuatu Yang Indah.
                </p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="right-side">
            <div class="login-form-container">
                <div class="login-header">
                    <h2>Selamat Datang</h2>
                    <p>Masuk ke akun Anda</p>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="success-message">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Google Login Button -->
                <a href="#" class="google-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Masuk dengan Google
                </a>

                <div class="divider">
                    <span>atau</span>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">
                            Email <span class="required">*</span>
                        </label>
                        <input id="email"
                               type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus
                               placeholder="Masukkan alamat email Anda">

                        @error('email')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            Password <span class="required">*</span>
                        </label>
                        <div class="password-container">
                            <input id="password"
                                   type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="Masukkan password Anda">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="bi bi-eye" id="password-icon"></i>
                            </button>
                        </div>

                        @error('password')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login">
                        <span style="display: flex; align-items: center; justify-content: center;">
                            Masuk
                            <i class="bi bi-arrow-right ms-2"></i>
                        </span>
                    </button>
                </form>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Lupa password?
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'bi bi-eye';
            }
        }

        // Add ripple effect to login button
        document.querySelector('.btn-login').addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            `;

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
@endsection
