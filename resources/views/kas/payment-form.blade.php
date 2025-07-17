<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pembayaran Kas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Form Pembayaran Kas</h1>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-4 p-4 border rounded-md bg-gray-50">
            <h2 class="text-lg font-semibold mb-2">Detail Kas</h2>
            <p><strong>Tanggal:</strong> {{ $kas->tanggal }}</p>
            <p><strong>Jenis:</strong> {{ ucfirst($kas->jenis) }}</p>
            <p><strong>Jumlah:</strong> Rp{{ number_format($kas->jumlah, 2, ',', '.') }}</p>
            <p><strong>Keterangan:</strong> {{ $kas->keterangan }}</p>
        </div>

        <form action="{{ route('kas.payment.process', $kas->id) }}" method="POST" enctype="multipart/form-data"
            class="space-y-4" x-data="{
                paymentMethod: '{{ old('payment_method', '') }}',
                qrCodeUrl: '{{ $paymentInfo->qr_code_url ?? '' }}',
                init() {
                    // Ensure QR code visibility is correctly set on page load if old input exists
                    this.showQrCode = (this.paymentMethod === 'QRIS' || this.paymentMethod === 'QR Code');
                    this.$watch('paymentMethod', value => {
                        this.showQrCode = (value === 'QRIS' || value === 'QR Code');
                    });
                },
                showQrCode: false,
            }">
            @csrf

            <input type="hidden" name="amount_paid" value="{{ $kas->jumlah }}">

            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                <select id="payment_method" name="payment_method" x-model="paymentMethod"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required>
                    <option value="">Pilih Metode Pembayaran</option>
                    @if ($paymentInfo->has_bank_transfer)
                        <option value="Bank Transfer">Bank Transfer</option>
                    @endif
                    @if ($paymentInfo->has_e_wallet)
                        @if ($paymentInfo->dana_number)
                            <option value="DANA">DANA</option>
                        @endif
                        @if ($paymentInfo->gopay_number)
                            <option value="GoPay">GoPay</option>
                        @endif
                        @if ($paymentInfo->ovo_number)
                            <option value="OVO">OVO</option>
                        @endif
                        @if ($paymentInfo->shopeepay_number)
                            <option value="ShopeePay">ShopeePay</option>
                        @endif
                    @endif
                    @if ($paymentInfo->has_qr_code)
                        <option value="QRIS">QRIS</option>
                    @endif
                    <option value="Cash">Tunai (Bayar Langsung)</option>
                </select>
            </div>

            <div x-show="paymentMethod === 'Bank Transfer'" class="border p-4 rounded-md bg-blue-50">
                <h3 class="text-md font-semibold mb-2">Detail Bank Transfer</h3>
                <p><strong>Nama Bank:</strong> {{ $paymentInfo->bank_name }}</p>
                <p><strong>Nomor Rekening:</strong> {{ $paymentInfo->bank_account_number }}</p>
                <p><strong>Atas Nama:</strong> {{ $paymentInfo->bank_account_name }}</p>
            </div>

            @if ($paymentInfo->has_e_wallet)
                <div x-show="paymentMethod === 'DANA'" class="border p-4 rounded-md bg-green-50">
                    <h3 class="text-md font-semibold mb-2">Detail E-Wallet DANA</h3>
                    <p><strong>Nomor:</strong> {{ $paymentInfo->dana_number }}</p>
                    <p><strong>Atas Nama:</strong> {{ $paymentInfo->dana_account_name }}</p>
                </div>
                <div x-show="paymentMethod === 'GoPay'" class="border p-4 rounded-md bg-green-50">
                    <h3 class="text-md font-semibold mb-2">Detail E-Wallet GoPay</h3>
                    <p><strong>Nomor:</strong> {{ $paymentInfo->gopay_number }}</p>
                    <p><strong>Atas Nama:</strong> {{ $paymentInfo->gopay_account_name }}</p>
                </div>
                <div x-show="paymentMethod === 'OVO'" class="border p-4 rounded-md bg-green-50">
                    <h3 class="text-md font-semibold mb-2">Detail E-Wallet OVO</h3>
                    <p><strong>Nomor:</strong> {{ $paymentInfo->ovo_number }}</p>
                    <p><strong>Atas Nama:</strong> {{ $paymentInfo->ovo_account_name }}</p>
                </div>
                <div x-show="paymentMethod === 'ShopeePay'" class="border p-4 rounded-md bg-green-50">
                    <h3 class="text-md font-semibold mb-2">Detail E-Wallet ShopeePay</h3>
                    <p><strong>Nomor:</strong> {{ $paymentInfo->shopeepay_number }}</p>
                    <p><strong>Atas Nama:</strong> {{ $paymentInfo->shopeepay_account_name }}</p>
                </div>
            @endif

            <div x-show="showQrCode" class="border p-4 rounded-md bg-purple-50">
                <h3 class="text-md font-semibold mb-2">QR Code Pembayaran</h3>
                @if ($paymentInfo->qr_code_url)
                    <img :src="qrCodeUrl" alt="QR Code Pembayaran" class="max-w-full h-auto mx-auto my-4">
                    <p class="text-center text-sm text-gray-600">
                        {{ $paymentInfo->qr_code_description }}
                        @if ($paymentInfo->qr_code_account_name)
                            <br>A/N: {{ $paymentInfo->qr_code_account_name }}
                        @endif
                    </p>
                @else
                    <p class="text-center text-gray-500">QR Code tidak tersedia.</p>
                @endif
            </div>

            <div>
                <label for="proof_of_payment" class="block text-sm font-medium text-gray-700">Unggah Bukti Pembayaran (Opsional)</label>
                <input type="file" id="proof_of_payment" name="proof_of_payment"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                <textarea id="notes" name="notes" rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('kas.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Ajukan Pembayaran
                </button>
            </div>
        </form>
    </div>
</body>
</html>
