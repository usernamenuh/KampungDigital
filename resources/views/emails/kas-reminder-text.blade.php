{{ isset($kasData['is_new_kas']) && $kasData['is_new_kas'] ? 'TAGIHAN KAS BARU' : (isset($kasData['days_until_due']) && $kasData['days_until_due'] <= 0 ? 'KAS SUDAH JATUH TEMPO' : 'REMINDER PEMBAYARAN KAS') }}
RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}

Halo {{ $kasData['penduduk_nama'] ?? 'Warga' }},

@if(isset($kasData['is_new_kas']) && $kasData['is_new_kas'])
Anda memiliki tagihan kas baru yang perlu dibayar. Silakan lakukan pembayaran sebelum tanggal jatuh tempo.
@elseif(isset($kasData['days_until_due']) && $kasData['days_until_due'] <= 0)
Tagihan kas Anda sudah jatuh tempo. Mohon segera lakukan pembayaran untuk menghindari denda.
@else
Tagihan kas Anda akan jatuh tempo dalam {{ $kasData['days_until_due'] ?? 0 }} hari. Silakan lakukan pembayaran sebelum tanggal jatuh tempo.
@endif

DETAIL TAGIHAN:
- Minggu Ke: {{ $kasData['minggu_ke'] ?? '-' }}
- Tahun: {{ $kasData['tahun'] ?? '-' }}
- Jumlah: Rp {{ number_format($kasData['amount'] ?? 0, 0, ',', '.') }}
@if(isset($kasData['due_date']) && $kasData['due_date'])
- Jatuh Tempo: {{ \Carbon\Carbon::parse($kasData['due_date'])->format('d/m/Y') }}
@endif

CARA PEMBAYARAN:
1. Login ke sistem kas RT
2. Pilih tagihan yang akan dibayar
3. Pilih metode pembayaran (tunai/transfer/e-wallet)
4. Upload bukti pembayaran jika diperlukan
5. Tunggu konfirmasi dari pengurus RT

Untuk melakukan pembayaran, kunjungi: {{ config('app.url') }}

Jika ada pertanyaan mengenai pembayaran kas, silakan hubungi pengurus RT.

Pengurus RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}
