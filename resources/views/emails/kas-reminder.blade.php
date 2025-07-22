<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Pembayaran Kas</title>
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
            background-color: #d97706; /* Amber 600 */
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .email-header.urgent {
            background-color: #dc2626; /* Red 600 for urgent */
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
            background-color: #d97706;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 20px auto;
        }
        .status-badge.urgent {
            background-color: #dc2626;
        }
        .payment-details {
            background-color: #fef3c7;
            border: 2px solid #d97706;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .payment-details.urgent {
            background-color: #fef2f2;
            border-color: #dc2626;
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
        .alert-message {
            background-color: #fef2f2;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .info-message {
            background-color: #f0f9ff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .payment-steps {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .payment-steps ol {
            margin: 0;
            padding-left: 20px;
            color: #6b7280;
        }
        .payment-steps li {
            margin-bottom: 8px;
        }
        .cta-button {
            display: inline-block;
            background-color: #d97706;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .cta-button.urgent {
            background-color: #dc2626;
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
    @php
        $isUrgent = isset($kasData['days_until_due']) && $kasData['days_until_due'] <= 0;
        $isNewKas = isset($kasData['is_new_kas']) && $kasData['is_new_kas'];
    @endphp
    
    <div class="email-container">
        <div class="email-header {{ $isUrgent ? 'urgent' : '' }}">
            <h1>
                @if($isNewKas)
                    📋 Tagihan Kas Baru
                @elseif($isUrgent)
                    ⚠️ Kas Sudah Jatuh Tempo
                @else
                    🔔 Reminder Pembayaran Kas
                @endif
            </h1>
        </div>
        <div class="email-body">
            <p>Halo <strong>{{ $kasData['penduduk_nama'] ?? 'Warga' }}</strong>,</p>
            
            @if($isNewKas)
                <p>Anda memiliki <strong>tagihan kas baru</strong> yang perlu dibayar. Silakan lakukan pembayaran sebelum tanggal jatuh tempo.</p>
            @elseif($isUrgent)
                <p>Tagihan kas Anda <strong style="color: #dc2626;">sudah jatuh tempo</strong>. Mohon segera lakukan pembayaran untuk menghindari denda.</p>
            @else
                <p>Tagihan kas Anda akan jatuh tempo dalam <strong style="color: #d97706;">{{ $kasData['days_until_due'] ?? 0 }} hari</strong>. Silakan lakukan pembayaran sebelum tanggal jatuh tempo.</p>
            @endif

            <div class="payment-details {{ $isUrgent ? 'urgent' : '' }}">
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
                        <td style="color: #059669; font-weight: 600; font-size: 18px;">
                            Rp {{ number_format($kasData['amount'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @if(isset($kasData['due_date']) && $kasData['due_date'])
                    <tr>
                        <td>Jatuh Tempo:</td>
                        <td style="color: {{ $isUrgent ? '#dc2626' : '#6b7280' }}; font-weight: {{ $isUrgent ? '600' : 'normal' }};">
                            {{ \Carbon\Carbon::parse($kasData['due_date'])->format('d/m/Y') }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td>Status:</td>
                        <td>
                            <span class="status-badge {{ $isUrgent ? 'urgent' : '' }}">
                                {{ $isUrgent ? 'JATUH TEMPO' : 'BELUM BAYAR' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            @if($isUrgent)
            <div class="alert-message">
                <p style="margin: 0; color: #dc2626; font-weight: 600; font-size: 16px;">
                    ⚠️ PERHATIAN: Tagihan Sudah Jatuh Tempo!
                </p>
                <p style="margin: 10px 0 0 0; color: #b91c1c; font-size: 14px;">
                    Segera lakukan pembayaran untuk menghindari denda keterlambatan.
                </p>
            </div>
            @else
            <div class="info-message">
                <p style="margin: 0; color: #0369a1; font-weight: 600; font-size: 16px;">
                    💡 Jangan Lupa Bayar Kas!
                </p>
                <p style="margin: 10px 0 0 0; color: #0c4a6e; font-size: 14px;">
                    Pembayaran tepat waktu membantu kelancaran kegiatan RT.
                </p>
            </div>
            @endif

            <div class="payment-steps">
                <p style="font-weight: 600; color: #374151; margin-bottom: 10px;">Cara Pembayaran:</p>
                <ol>
                    <li>Login ke sistem kas RT</li>
                    <li>Pilih tagihan yang akan dibayar</li>
                    <li>Pilih metode pembayaran (tunai/transfer/e-wallet)</li>
                    <li>Upload bukti pembayaran jika diperlukan</li>
                    <li>Tunggu konfirmasi dari pengurus RT</li>
                </ol>
            </div>

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" class="cta-button {{ $isUrgent ? 'urgent' : '' }}">
                    Bayar Sekarang
                </a>
            </div>

            <p style="font-size: 14px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px;">
                Jika ada pertanyaan mengenai pembayaran kas, silakan hubungi pengurus RT.<br>
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
