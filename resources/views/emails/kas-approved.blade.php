<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Kas Disetujui</title>
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
            background-color: #16a34a; /* Green 600 */
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
        .status-badge {
            display: inline-block;
            background-color: #16a34a;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 20px auto;
        }
        .payment-details {
            background-color: #f0fdf4;
            border: 2px solid #16a34a;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .payment-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-details td {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .payment-details td:first-child {
            font-weight: 600;
            color: #374151;
            width: 40%;
        }
        .payment-details td:last-child {
            color: #6b7280;
        }
        .success-message {
            background-color: #f0fdf4;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #16a34a;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
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
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>✅ Pembayaran Kas Disetujui</h1>
        </div>
        <div class="email-body">
            <p>Halo <strong>{{ $kasData['penduduk_nama'] ?? 'Warga' }}</strong>,</p>
            
            <p>Selamat! Pembayaran kas Anda telah <strong>disetujui dan dikonfirmasi</strong> oleh pengurus RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}.</p>

            <div class="payment-details">
                <table>
                    <tr>
                        <td>Minggu Ke:</td>
                        <td>{{ $kasData['minggu_ke'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tahun:</td>
                        <td>{{ $kasData['tahun'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jumlah:</td>
                        <td style="color: #16a34a; font-weight: 600; font-size: 18px;">
                            Rp {{ number_format($kasData['amount'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @if(isset($kasData['payment_method']) && $kasData['payment_method'])
                    <tr>
                        <td>Metode Pembayaran:</td>
                        <td>{{ $kasData['payment_method'] }}</td>
                    </tr>
                    @endif
                    @if(isset($kasData['payment_date']) && $kasData['payment_date'])
                    <tr>
                        <td>Tanggal Konfirmasi:</td>
                        <td>{{ \Carbon\Carbon::parse($kasData['payment_date'])->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Status:</td>
                        <td><span class="status-badge">LUNAS</span></td>
                    </tr>
                </table>
            </div>

            <div class="success-message">
                <p style="margin: 0; color: #16a34a; font-weight: 600; font-size: 16px;">
                    🎉 Terima kasih atas pembayaran kas Anda!
                </p>
                <p style="margin: 10px 0 0 0; color: #15803d; font-size: 14px;">
                    Kontribusi Anda sangat membantu kegiatan RT.
                </p>
            </div>

            <p>Pembayaran Anda telah tercatat dalam sistem dan akan digunakan untuk keperluan RT sesuai dengan ketentuan yang berlaku.</p>

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" class="cta-button">
                    Lihat Status Kas
                </a>
            </div>

            <p style="font-size: 14px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px;">
                Jika ada pertanyaan mengenai pembayaran ini, silakan hubungi pengurus RT.<br>
                <strong>Pengurus RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}</strong>
            </p>

            <p>Terima kasih,<br>Tim {{ config('app.name') }}</p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
