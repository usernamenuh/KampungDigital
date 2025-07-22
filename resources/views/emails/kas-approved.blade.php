@extends('layouts.email')

@section('title', 'Pembayaran Kas Disetujui')

@section('content')
<div style="background-color: #f0fdf4; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #22c55e;">
    <h2 style="color: #16a34a; margin: 0 0 10px 0; font-size: 24px;">
        ✅ Pembayaran Kas Disetujui
    </h2>
    <p style="color: #15803d; margin: 0; font-size: 14px;">
        RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}
    </p>
</div>

<div style="margin-bottom: 20px;">
    <p style="font-size: 16px; line-height: 1.6; color: #374151;">
        Halo <strong>{{ $kasData['penduduk_nama'] ?? 'Warga' }}</strong>,
    </p>
    
    <p style="font-size: 16px; line-height: 1.6; color: #374151;">
        Selamat! Pembayaran kas Anda telah <strong style="color: #16a34a;">disetujui dan dikonfirmasi</strong> oleh pengurus RT.
    </p>
</div>

<div style="background-color: #ffffff; border: 2px solid #22c55e; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151; width: 40%;">Minggu Ke:</td>
            <td style="padding: 8px 0; color: #6b7280;">{{ $kasData['minggu_ke'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">Tahun:</td>
            <td style="padding: 8px 0; color: #6b7280;">{{ $kasData['tahun'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">Jumlah:</td>
            <td style="padding: 8px 0; color: #059669; font-weight: 600; font-size: 18px;">
                Rp {{ number_format($kasData['amount'] ?? 0, 0, ',', '.') }}
            </td>
        </tr>
        @if(isset($kasData['payment_method']) && $kasData['payment_method'])
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">Metode Pembayaran:</td>
            <td style="padding: 8px 0; color: #6b7280;">{{ $kasData['payment_method'] }}</td>
        </tr>
        @endif
        @if(isset($kasData['payment_date']) && $kasData['payment_date'])
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">Tanggal Konfirmasi:</td>
            <td style="padding: 8px 0; color: #6b7280;">
                {{ \Carbon\Carbon::parse($kasData['payment_date'])->format('d/m/Y H:i') }}
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">Status:</td>
            <td style="padding: 8px 0;">
                <span style="background-color: #22c55e; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                    LUNAS
                </span>
            </td>
        </tr>
    </table>
</div>

<div style="background-color: #f0fdf4; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <p style="margin: 0; color: #16a34a; font-weight: 600; text-align: center; font-size: 16px;">
        🎉 Terima kasih atas pembayaran kas Anda!
    </p>
    <p style="margin: 10px 0 0 0; color: #15803d; text-align: center; font-size: 14px;">
        Kontribusi Anda sangat membantu kegiatan RT.
    </p>
</div>

<div style="margin: 30px 0;">
    <p style="font-size: 16px; line-height: 1.6; color: #374151;">
        Pembayaran Anda telah tercatat dalam sistem dan akan digunakan untuk keperluan RT sesuai dengan ketentuan yang berlaku.
    </p>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ config('app.url') }}" 
       style="display: inline-block; background-color: #16a34a; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">
        Lihat Status Kas
    </a>
</div>

<div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px;">
    <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">
        Jika ada pertanyaan mengenai pembayaran ini, silakan hubungi pengurus RT.
    </p>
    <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">
        <strong>Pengurus RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}</strong>
    </p>
</div>
@endsection
