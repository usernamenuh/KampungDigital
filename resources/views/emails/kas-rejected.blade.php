@extends('layouts.email')

@section('title', 'Pembayaran Kas Ditolak')

@section('content')
<div style="background-color: #fef2f2; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #dc2626;">
    <h2 style="color: #dc2626; margin: 0 0 10px 0; font-size: 24px;">
        ❌ Pembayaran Kas Ditolak
    </h2>
    <p style="color: #991b1b; margin: 0; font-size: 14px;">
        RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}
    </p>
</div>

<div style="margin-bottom: 20px;">
    <p style="font-size: 16px; line-height: 1.6; color: #374151;">
        Halo <strong>{{ $kasData['penduduk_nama'] ?? 'Warga' }}</strong>,
    </p>
    
    <p style="font-size: 16px; line-height: 1.6; color: #374151;">
        Mohon maaf, pembayaran kas Anda <strong style="color: #dc2626;">ditolak</strong> oleh pengurus RT.
    </p>
</div>

<div style="background-color: #ffffff; border: 2px solid #dc2626; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
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
            <td style="padding: 8px 0; color: #dc2626; font-weight: 600; font-size: 18px;">
                Rp {{ number_format($kasData['amount'] ?? 0, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">Status:</td>
            <td style="padding: 8px 0;">
                <span style="background-color: #dc2626; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                    DITOLAK
                </span>
            </td>
        </tr>
    </table>
</div>

@if(isset($kasData['rejection_reason']) && $kasData['rejection_reason'])
<div style="background-color: #fef2f2; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #dc2626;">
    <h3 style="color: #dc2626; margin: 0 0 10px 0; font-size: 16px;">Alasan Penolakan:</h3>
    <p style="margin: 0; color: #991b1b; font-size: 14px; line-height: 1.6;">
        {{ $kasData['rejection_reason'] }}
    </p>
</div>
@endif

<div style="background-color: #f0f9ff; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <h3 style="color: #0369a1; margin: 0 0 10px 0; font-size: 16px;">Langkah Selanjutnya:</h3>
    <ul style="margin: 0; padding-left: 20px; color: #0c4a6e;">
        <li style="margin-bottom: 8px;">Periksa kembali bukti pembayaran Anda</li>
        <li style="margin-bottom: 8px;">Pastikan nominal dan metode pembayaran sudah benar</li>
        <li style="margin-bottom: 8px;">Upload ulang bukti pembayaran yang valid</li>
        <li style="margin-bottom: 8px;">Atau hubungi pengurus RT untuk klarifikasi</li>
    </ul>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ config('app.url') }}/kas" 
       style="display: inline-block; background-color: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">
        Upload Ulang Bukti Pembayaran
    </a>
</div>

<div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px;">
    <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">
        Jika ada pertanyaan mengenai penolakan ini, silakan hubungi pengurus RT untuk penjelasan lebih lanjut.
    </p>
    <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">
        <strong>Pengurus RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}</strong>
    </p>
</div>
@endsection
