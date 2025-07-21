<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }
        .header h1 {
            color: #333333;
            font-size: 24px;
            margin: 0;
        }
        .content {
            padding: 20px 0;
            line-height: 1.6;
            color: #555555;
        }
        .otp-code {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 15px 25px;
            background-color: #e0f2f7; /* Light blue background */
            color: #007bff; /* Blue text */
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            border-radius: 5px;
            border: 1px dashed #a7d9ed;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            font-size: 12px;
            color: #aaaaaa;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Verifikasi Akun Anda</h1>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Terima kasih telah mendaftar di aplikasi kami. Untuk menyelesaikan proses registrasi, silakan gunakan Kode Verifikasi (OTP) berikut:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p>Kode ini berlaku selama <strong>10 menit</strong>. Jangan bagikan kode ini kepada siapa pun.</p>
            <p>Jika Anda tidak mencoba mendaftar, abaikan email ini.</p>
            <p>Hormat kami,<br>Tim {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
