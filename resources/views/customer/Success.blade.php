<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instruksi Pembayaran - Steven Shoes</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#FAF6EE',       /* Background krem lembut sesuai image_ae9f84.jpg */
                            dark: '#3E2511',     /* Warna cokelat tua mewah */
                            secondary: '#6E5D4F', /* Warna abu-cokelat elegan */
                            accent: '#D9B78D',    /* Warna accent kulit/tan */
                            light: '#FAF6EE',
                            card: '#FFFDF9'
                        }
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
</head>
<body class="bg-brand-bg font-sans min-h-screen text-brand-dark overflow-x-hidden antialiased flex flex-col justify-between">

    <!-- Header Navigation -->
    <header class="max-w-4xl mx-auto w-full px-6 py-6 flex items-center justify-between border-b border-[#E4D5BE]">
        <a href="/" class="font-serif text-xl font-bold tracking-wider text-brand-dark hover:opacity-85 transition-opacity">
            STEVEN SHOES
        </a>
        <span class="text-xs font-semibold uppercase tracking-wider text-green-700 bg-green-50 px-3 py-1 rounded-full">
            Invoice Dibuat
        </span>
    </header>

    <!-- Main Container -->
    <main class="max-w-xl mx-auto w-full px-6 py-12 flex-1 flex flex-col justify-center">
        
        <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 sm:p-10 shadow-lg text-center space-y-8">
            
            <!-- Icon Success -->
            <div class="mx-auto w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center text-green-600">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <!-- Title & Nota Transaksi -->
            <div class="space-y-2">
                <h1 class="font-serif text-3xl font-medium tracking-tight">Terima Kasih Atas Pesananmu!</h1>
                <p class="text-brand-secondary text-sm">Pesananmu telah tercatat di dalam sistem kami dengan detail nota berikut:</p>
                <div class="inline-block bg-brand-bg px-4 py-2 rounded-xl border border-[#E4D5BE] mt-2">
                    <span class="text-xs font-mono font-bold tracking-wider text-brand-secondary">
                        NOTA: {{ $transaction->id }}
                    </span>
                </div>
            </div>

            <!-- Detail Pembayaran -->
            <div class="bg-[#FAF6EE] border border-[#EADBCE] rounded-2xl p-6 space-y-4">
                <p class="text-xs text-brand-secondary uppercase tracking-widest font-semibold">Total Yang Harus Dibayar</p>
                <h2 class="font-serif text-3xl font-bold text-brand-dark">
                    Rp {{ number_format($transaction->total_amount ?? 0, 0, ',', '.') }}
                </h2>
            </div>

            <!-- LOGIKA KONDISI BERDASARKAN METODE PEMBAYARAN -->
            <div class="space-y-4">
                @if($transaction->payment_method == 'qris')
                    <!-- Kondisi 1: QRIS -->
                    <div class="space-y-4 bg-white border border-red-100 rounded-2xl p-6">
                        <div class="flex items-center justify-center gap-2 text-red-600 font-bold text-sm uppercase tracking-wider">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m0 11v1m5-10v.01M9 16v.01M5 8h.01M5 12h.01M5 16h.01M9 8h.01M15 8h.01M15 12h.01M15 16h.01M19 8h.01M19 12h.01M19 16h.01M9 12h.01" />
                            </svg>
                            Scan Kode QRIS
                        </div>
                        <p class="text-xs text-brand-secondary leading-relaxed max-w-sm mx-auto">Silakan scan kode QR di bawah ini menggunakan aplikasi dompet digital favorit Anda (Gopay, OVO, ShopeePay, Dana, dll).</p>
                        
                        <!-- QR Code Generator Placeholder (Estetik) -->
                        <div class="mx-auto w-52 h-52 border-2 border-brand-dark rounded-2xl p-3 bg-white flex items-center justify-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=STEVENSHOES-{{ $transaction->id }}" 
                                 alt="QRIS Pembayaran Steven Shoes" 
                                 class="w-full h-full object-contain">
                        </div>
                        <p class="text-[10px] text-brand-secondary font-semibold italic">Masa berlaku QR code ini terbatas dalam waktu 15 menit.</p>
                    </div>
                @else
                    <!-- Kondisi 2: Virtual Account (BCA/Mandiri) -->
                    <div class="space-y-4 bg-white border border-blue-100 rounded-2xl p-6">
                        <div class="flex items-center justify-center gap-2 text-blue-700 font-bold text-sm uppercase tracking-wider">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Instruksi Transfer Virtual Account
                        </div>
                        
                        <p class="text-xs text-brand-secondary max-w-sm mx-auto">
                            Gunakan nomor rekening Virtual Account {{ strtoupper($transaction->payment_method) }} di bawah ini untuk menyelesaikan transfer dana Anda.
                        </p>

                        <!-- Nomor Rekening VA Box -->
                        <div class="flex items-center justify-between bg-brand-bg px-4 py-3 rounded-xl border border-[#E4D5BE] max-w-md mx-auto">
                            <span class="text-xs text-brand-secondary uppercase font-semibold">{{ strtoupper($transaction->payment_method) }} VA</span>
                            <span id="va-code" class="font-mono text-base font-bold tracking-wider text-brand-dark">880123456789</span>
                            
                            <button onclick="copyToClipboard()" id="btn-copy" class="text-[10px] font-bold tracking-wider text-brand-dark bg-[#EFE7D8] hover:bg-[#E4D5BE] px-3 py-1.5 rounded-lg transition-colors focus:outline-none">
                                Salin Kode
                            </button>
                        </div>

                        <!-- Ringkasan Langkah Transfer Singkat -->
                        <div class="text-left max-w-xs mx-auto pt-2">
                            <ol class="list-decimal text-[11px] text-brand-secondary space-y-1.5 pl-4">
                                <li>Pilih menu transfer <span class="font-bold">Virtual Account</span>.</li>
                                <li>Masukkan kode rekening di atas.</li>
                                <li>Pastikan nama tagihan yang muncul adalah <span class="font-bold">Steven Shoes</span>.</li>
                            </ol>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tombol Navigasi Bawah -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-[#FAF6EE]">
                @if($transaction->status == 'menunggu_pembayaran')
                    <form action="{{ route('orders.pay', $transaction) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-brand-dark hover:bg-brand-dark/95 text-white text-xs font-semibold py-3.5 rounded-xl transition-all text-center">
                            Bayar Sekarang
                        </button>
                    </form>
                @endif
                <a href="/customer/dashboard" class="flex-1 border border-brand-dark hover:bg-brand-dark/5 text-brand-dark text-xs font-semibold py-3.5 rounded-xl transition-all text-center">
                    Cek Riwayat Pesanan
                </a>
                <a href="/customer/dashboard" class="flex-1 @if($transaction->status == 'menunggu_pembayaran') border border-[#EADBCE] hover:bg-[#F3ECDF] text-brand-dark @else bg-brand-dark hover:bg-brand-dark/95 text-white @endif text-xs font-semibold py-3.5 rounded-xl transition-all text-center">
                    Kembali Ke Katalog
                </a>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="max-w-4xl mx-auto w-full px-6 py-6 text-center text-[10px] text-brand-secondary">
        &copy; 2026 STEVEN SHOES. All rights reserved. Hubungi admin kami jika Anda mengalami kendala pembayaran.
    </footer>

    <!-- Script Salin No VA -->
    <script>
        function copyToClipboard() {
            const vaText = document.getElementById('va-code').innerText;
            const btnCopy = document.getElementById('btn-copy');

            // Copy to clipboard using standard fallback
            const tempInput = document.createElement('input');
            tempInput.value = vaText;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            // Ganti teks tombol sebagai notifikasi salin sukses
            btnCopy.innerText = 'Tersalin!';
            btnCopy.classList.replace('text-brand-dark', 'text-green-700');
            btnCopy.classList.replace('bg-[#EFE7D8]', 'bg-green-100');

            setTimeout(() => {
                btnCopy.innerText = 'Salin Kode';
                btnCopy.classList.replace('text-green-700', 'text-brand-dark');
                btnCopy.classList.replace('bg-green-100', 'bg-[#EFE7D8]');
            }, 2500);
        }
    </script>

</body>
</html>
