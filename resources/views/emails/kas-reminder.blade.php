@extends('layouts.email')

@section('title', 'Reminder Pembayaran Kas')

@section('content')
@php
    $isUrgent = isset($kasData['days_until_due']) && $kasData['days_until_due'] <= 0;
    $isNewKas = isset($kasData['is_new_kas']) && $kasData['is_new_kas'];
@endphp

<div style="background-color: {{ $isUrgent ? '#fef2f2' : '#fef3c7' }}; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid {{ $isUrgent ? '#ef4444' : '#f59e0b' }};">
    <h2 style="color: {{ $isUrgent ? '#dc2626' : '#d97706' }}; margin: 0 0 10px 0; font-size: 24px;">
        @if($isNewKas)
            📋 Tagihan Kas Baru
        @elseif($isUrgent)
            ⚠️ Kas Sudah Jatuh Tempo
        @else
            🔔 Reminder Pembayaran Kas
        @endif
    </h2>
    <p style="color: {{ $isUrgent ? '#b91c1c' : '#b45309' }}; margin: 0; font-size: 14px;">
        RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}
    </p>
</div>

<div style="margin-bottom: 20px;">
    <p style="font-size: 16px; line-height: 1.6; color: #374151;">
        Halo <strong>{{ $kasData['penduduk_nama'] ?? 'Warga' }}</strong>,
    </p>
    
    @if($isNewKas)
        <p style="font-size: 16px; line-height: 1.6; color: #374151;">
            Anda memiliki <strong>tagihan kas baru</strong> yang perlu dibayar. Silakan lakukan pembayaran sebelum tanggal jatuh tempo.
        </p>
    @elseif($isUrgent)
        <p style="font-size: 16px; line-height: 1.6; color: #374151;">
            Tagihan kas Anda <strong style="color: #dc2626;">sudah jatuh tempo</strong>. Mohon segera lakukan pembayaran untuk menghindari denda.
        </p>
    @else
        <p style="font-size: 16px; line-height: 1.6; color: #374151;">
            Tagihan kas Anda akan jatuh tempo dalam <strong style="color: #d97706;">{{ $kasData['days_until_due'] ?? 0 }} hari</strong>. Silakan lakukan pembayaran sebelum tanggal jatuh tempo.
        </p>
    @endif
</div>

<div style="background-color: #ffffff; border: 2px solid {{ $isUrgent ? '#ef4444' : '#f59e0b' }}; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
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
        @if(isset($kasData['due_date']) && $kasData['due_date'])
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">Jatuh Tempo:</td>
            <td style="padding: 8px 0; color: {{ $isUrgent ? '#dc2626' : '#6b7280' }}; font-weight: {{ $isUrgent ? '600' : 'normal' }};">
                {{ \Carbon\Carbon::parse($kasData['due_date'])->format('d/m/Y') }}
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">Status:</td>
            <td style="padding: 8px 0;">
                <span style="background-color: {{ $isUrgent ? '#ef4444' : '#f59e0b' }}; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                    @if($isUrgent)
                        JATUH TEMPO
                    @else
                        BELUM BAYAR
                    @endif
                </span>
            </td>
        </tr>
    </table>
</div>

@if($isUrgent)
<div style="background-color: #fef2f2; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <p style="margin: 0; color: #dc2626; font-weight: 600; text-align: center; font-size: 16px;">
        ⚠️ PERHATIAN: Tagihan Sudah Jatuh Tempo!
    </p>
    <p style="margin: 10px 0 0 0; color: #b91c1c; text-align: center; font-size: 14px;">
        Segera lakukan pembayaran untuk menghindari denda keterlambatan.
    </p>
</div>
@else
<div style="background-color: #f0f9ff; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <p style="margin: 0; color: #0369a1; font-weight: 600; text-align: center; font-size: 16px;">
        💡 Jangan Lupa Bayar Kas!
    </p>
    <p style="margin: 10px 0 0 0; color: #0c4a6e; text-align: center; font-size: 14px;">
        Pembayaran tepat waktu membantu kelancaran kegiatan RT.
    </p>
</div>
@endif

<div style="margin: 30px 0;">
    <p style="font-size: 16px; line-height: 1.6; color: #374151;">
        <strong>Cara Pembayaran:</strong>
    </p>
    <ol style="color: #6b7280; line-height: 1.6;">
        <li>Login ke sistem kas RT</li>
        <li>Pilih tagihan yang akan dibayar</li>
        <li>Pilih metode pembayaran (tunai/transfer/e-wallet)</li>
        <li>Upload bukti pembayaran jika diperlukan</li>
        <li>Tunggu konfirmasi dari pengurus RT</li>
    </ol>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ config('app.url') }}" 
       style="display: inline-block; background-color: {{ $isUrgent ? '#dc2626' : '#d97706' }}; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">
        Bayar Sekarang
    </a>
</div>

<div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px;">
    <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">
        Jika ada pertanyaan mengenai pembayaran kas, silakan hubungi pengurus RT.
    </p>
    <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">
        <strong>Pengurus RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}</strong>
    </p>
</div>
@endsection
