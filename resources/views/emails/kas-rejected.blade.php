<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Kas Ditolak</title>
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
            background-color: #dc2626; /* Red 600 */
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
            background-color: #dc2626;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 20px auto;
        }
        .payment-details {
            background-color: #fef2f2;
            border: 2px solid #dc2626;
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
        .rejection-reason {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps {
            background-color: #f0f9ff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps ul {
            margin: 0;
            padding-left: 20px;
            color: #0c4a6e;
        }
        .next-steps li {
            margin-bottom: 8px;
        }
        .cta-button {
            display: inline-block;
            background-color: #dc2626;
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
            <h1>❌ Pembayaran Kas Ditolak</h1>
        </div>
        <div class="email-body">
            <p>Halo <strong>{{ $kasData['penduduk_nama'] ?? 'Warga' }}</strong>,</p>
            
            <p>Mohon maaf, pembayaran kas Anda telah <strong>ditolak</strong> oleh pengurus RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}.</p>

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
                        <td style="color: #dc2626; font-weight: 600; font-size: 18px;">
                            Rp {{ number_format($kasData['amount'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td><span class="status-badge">DITOLAK</span></td>
                    </tr>
                </table>
            </div>

            @if(isset($kasData['rejection_reason']) && $kasData['rejection_reason'])
            <div class="rejection-reason">
                <h3 style="color: #dc2626; margin: 0 0 10px 0; font-size: 16px;">📝 Alasan Penolakan:</h3>
                <p style="margin: 0; color: #991b1b; font-size: 14px;">
                    {{ $kasData['rejection_reason'] }}
                </p>
            </div>
            @endif

            <div class="next-steps">
                <h3 style="color: #0369a1; margin: 0 0 10px 0; font-size: 16px;">🔄 Langkah Selanjutnya:</h3>
                <ul>
                    <li>Periksa kembali bukti pembayaran Anda</li>
                    <li>Pastikan nominal dan metode pembayaran sudah benar</li>
                    <li>Upload ulang bukti pembayaran yang valid</li>
                    <li>Atau hubungi pengurus RT untuk klarifikasi</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/kas" class="cta-button">
                    Upload Ulang Bukti Pembayaran
                </a>
            </div>

            <p style="font-size: 14px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px;">
                Jika ada pertanyaan mengenai penolakan ini, silakan hubungi pengurus RT untuk penjelasan lebih lanjut.<br>
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
