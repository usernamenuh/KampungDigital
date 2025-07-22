PEMBAYARAN KAS DISETUJUI
RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}

Halo {{ $kasData['penduduk_nama'] ?? 'Warga' }},

Selamat! Pembayaran kas Anda telah disetujui dan dikonfirmasi oleh pengurus RT.

DETAIL PEMBAYARAN:
- Minggu Ke: {{ $kasData['minggu_ke'] ?? '-' }}
- Tahun: {{ $kasData['tahun'] ?? '-' }}
- Jumlah: Rp {{ number_format($kasData['amount'] ?? 0, 0, ',', '.') }}
@if(isset($kasData['payment_method']) && $kasData['payment_method'])
- Metode Pembayaran: {{ $kasData['payment_method'] }}
@endif
@if(isset($kasData['payment_date']) && $kasData['payment_date'])
- Tanggal Konfirmasi: {{ \Carbon\Carbon::parse($kasData['payment_date'])->format('d/m/Y H:i') }}
@endif
- Status: LUNAS

Terima kasih atas pembayaran kas Anda! Kontribusi Anda sangat membantu kegiatan RT.

Pembayaran Anda telah tercatat dalam sistem dan akan digunakan untuk keperluan RT sesuai dengan ketentuan yang berlaku.

Untuk melihat status kas Anda, kunjungi: {{ config('app.url') }}

Jika ada pertanyaan mengenai pembayaran ini, silakan hubungi pengurus RT.

Pengurus RT {{ $kasData['rt_no'] ?? '-' }} / RW {{ $kasData['rw_no'] ?? '-' }}
