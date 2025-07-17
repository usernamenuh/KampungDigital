<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pembayaran Berhasil')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="bg-green-600 text-white px-6 py-4">
            <h4 class="text-xl font-semibold flex items-center justify-center">
                <i class="fas fa-check-circle mr-3"></i>
                Pembayaran Berhasil
            </h4>
        </div>
        <div class="p-6 sm:p-8 text-center">
            <div class="mb-6">
                <i class="fas fa-check-circle text-green-500" style="font-size: 4.5rem;"></i>
            </div>
            
            <h5 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Pembayaran Kas Berhasil Diproses!</h5>
            
            <div class="p-4 rounded-md relative mb-6 text-sm
                @if($kas->status === 'lunas')
                    bg-green-100 border border-green-400 text-green-700 dark:bg-green-900 dark:text-green-300
                @else
                    bg-blue-100 border border-blue-400 text-blue-700 dark:bg-blue-900 dark:text-blue-300
                @endif
            " role="alert">
                @if($kas->status === 'lunas')
                    <p class="mb-0 font-medium">
                        <i class="fas fa-thumbs-up mr-2"></i>
                        Pembayaran tunai Anda telah dikonfirmasi dan tercatat sebagai <strong class="uppercase">LUNAS</strong>.
                    </p>
                @else
                    <p class="mb-0 font-medium">
                        <i class="fas fa-clock mr-2"></i>
                        Pembayaran Anda sedang <strong class="uppercase">MENUNGGU KONFIRMASI</strong> dari pengurus RT.
                    </p>
                @endif
            </div>

            <!-- Payment Details -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
                <div class="flex items-center mb-4">
                    <h6 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-receipt mr-2 text-gray-600 dark:text-gray-400"></i>
                        Detail Pembayaran
                    </h6>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300 text-left">
                    <div>
                        <p><strong class="font-medium">Minggu ke:</strong> {{ $kas->minggu_ke }}</p>
                        <p><strong class="font-medium">Tahun:</strong> {{ $kas->tahun }}</p>
                        <p><strong class="font-medium">Jumlah Dibayar:</strong> Rp {{ number_format($kas->jumlah_dibayar ?? $kas->jumlah, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p><strong class="font-medium">Metode Bayar:</strong> {{ $kas->metode_bayar_formatted }}</p>
                        <p><strong class="font-medium">Tanggal Bayar:</strong> {{ $kas->tanggal_bayar_formatted }}</p>
                        <p><strong class="font-medium">Status:</strong> 
                            @if($kas->status === 'lunas')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">{{ $kas->status_text }}</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">{{ $kas->status_text }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-gray-100 border border-gray-300 text-gray-800 px-4 py-3 rounded-md relative mb-6 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" role="alert">
                <h6 class="font-bold mb-1 flex items-center"><i class="fas fa-info-circle mr-2"></i>Langkah Selanjutnya:</h6>
                @if($kas->status === 'lunas')
                    <p class="mb-0">Pembayaran Anda sudah selesai. Terima kasih atas partisipasi Anda dalam kas RT.</p>
                @else
                    <p class="mb-0">
                        Silakan tunggu konfirmasi dari pengurus RT. Anda akan mendapat notifikasi setelah pembayaran dikonfirmasi.
                        Jika ada pertanyaan, silakan hubungi pengurus RT.
                    </p>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('dashboard.masyarakat') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-offset-gray-800 w-full sm:w-auto">
                    <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
                </a>
                <a href="{{ route('kas.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800 w-full sm:w-auto">
                    <i class="fas fa-list mr-2"></i> Lihat Daftar Kas
                </a>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>
