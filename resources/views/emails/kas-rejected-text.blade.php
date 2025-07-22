PEMBAYARAN KAS DITOLAK
RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}

Halo {{ $kasData['penduduk_nama'] ?? 'Warga' }},

Mohon maaf, pembayaran kas Anda telah ditolak oleh pengurus RT. Silakan periksa detail dan alasan penolakan di bawah ini.

DETAIL TAGIHAN:
- Minggu Ke: {{ $kasData['minggu_ke'] ?? '-' }}
- Tahun: {{ $kasData['tahun'] ?? '-' }}
- Jumlah: Rp {{ number_format($kasData['amount'] ?? 0, 0, ',', '.') }}
- Status: DITOLAK

@if(isset($kasData['rejection_reason']) && $kasData['rejection_reason'])
ALASAN PENOLAKAN:
{{ $kasData['rejection_reason'] }}
@endif

LANGKAH SELANJUTNYA:
Silakan perbaiki pembayaran sesuai catatan dan kirim ulang bukti pembayaran.

YANG PERLU ANDA LAKUKAN:
1. Periksa alasan penolakan di atas
2. Perbaiki masalah yang disebutkan
3. Upload ulang bukti pembayaran yang benar
4. Atau hubungi pengurus RT untuk klarifikasi

Untuk upload ulang bukti pembayaran, kunjungi: {{ config('app.url') }}

Jika ada pertanyaan mengenai penolakan ini, silakan hubungi pengurus RT untuk klarifikasi lebih lanjut.

Pengurus RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}
