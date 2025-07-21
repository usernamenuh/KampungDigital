<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background-color: #4f46e5; /* Indigo 600 */
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }
        .email-body p {
            margin-bottom: 15px;
        }
        .otp-code {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 15px 25px;
            background-color: #e0e7ff; /* Indigo 100 */
            color: #4f46e5; /* Indigo 600 */
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            border-radius: 8px;
            letter-spacing: 3px;
        }
        .email-footer {
            background-color: #f0f0f0;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777777;
            border-top: 1px solid #eeeeee;
        }
        .email-footer p {
            margin: 0;
        }
        .email-footer a {
            color: #4f46e5;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Reset Password Anda</h1>
        </div>
        <div class="email-body">
            <p>Halo,</p>
            <p>Anda telah meminta kode OTP untuk mereset password akun Anda. Berikut adalah kode OTP Anda:</p>
            <span class="otp-code">{{ $otp }}</span>
            <p>Kode ini berlaku selama {{ config('auth.passwords.users.expire') }} menit. Jangan bagikan kode ini kepada siapa pun.</p>
            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
            <p>Terima kasih,<br>Tim {{ config('app.name') }}</p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
