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
            @if(session('warning'))
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-left text-xs font-semibold text-amber-800">
                    {{ session('warning') }}
                </div>
            @endif
            
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

            @php
                $midtransReady = ($midtrans['enabled'] ?? false) && filled($transaction->snap_token);
                $paymentLabel = match ($transaction->payment_method) {
                    'bca' => 'Virtual Account BCA',
                    'mandiri' => 'Mandiri Bill Payment',
                    'qris' => 'QRIS',
                    default => strtoupper((string) $transaction->payment_method),
                };
            @endphp

            <div class="space-y-4">
                @if($midtransReady)
                    <div class="space-y-4 bg-white border border-green-100 rounded-2xl p-6">
                        <div class="flex items-center justify-center gap-2 text-green-700 font-bold text-sm uppercase tracking-wider">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Tagihan Midtrans Siap
                        </div>
                        <p class="text-xs text-brand-secondary leading-relaxed max-w-sm mx-auto">
                            Metode pilihan Anda: <span class="font-bold text-brand-dark">{{ $paymentLabel }}</span>. Nomor VA, kode bayar, atau QRIS resmi akan muncul di halaman pembayaran Midtrans.
                        </p>
                        <div class="bg-[#FAF6EE] border border-[#E4D5BE] rounded-xl px-4 py-3 max-w-md mx-auto text-left text-xs text-brand-secondary space-y-2">
                            <div class="flex items-center justify-between gap-4">
                                <span>Invoice</span>
                                <span class="font-mono font-bold text-brand-dark">#{{ $transaction->invoice_number }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Status</span>
                                <span class="font-bold text-amber-700">{{ $transaction->statusLabel() }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="space-y-4 bg-white border border-amber-100 rounded-2xl p-6">
                        <div class="flex items-center justify-center gap-2 text-amber-700 font-bold text-sm uppercase tracking-wider">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                            Midtrans Belum Siap
                        </div>
                        <p class="text-xs text-brand-secondary leading-relaxed max-w-sm mx-auto">
                            Tagihan sudah tercatat, tetapi token pembayaran Midtrans belum tersedia. Pastikan key Midtrans sudah diisi, lalu coba buat ulang link pembayaran.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Tombol Navigasi Bawah -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-[#FAF6EE]">
                @if($transaction->status == 'menunggu_pembayaran')
                    @if($midtransReady)
                        <button type="button" id="midtrans-pay-button" class="flex-1 bg-brand-dark hover:bg-brand-dark/95 text-white text-xs font-semibold py-3.5 rounded-xl transition-all text-center disabled:opacity-60">
                            Bayar dengan Midtrans
                        </button>
                    @else
                        <form action="{{ route('orders.pay', $transaction) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-brand-dark hover:bg-brand-dark/95 text-white text-xs font-semibold py-3.5 rounded-xl transition-all text-center">
                                Coba Buat Link Bayar
                            </button>
                        </form>
                    @endif
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

    @if($midtransReady)
        <script src="{{ $midtrans['snap_script_url'] }}" data-client-key="{{ $midtrans['client_key'] }}"></script>
        <script>
            const midtransPayButton = document.getElementById('midtrans-pay-button');

            function resetMidtransButton() {
                if (!midtransPayButton) {
                    return;
                }

                midtransPayButton.disabled = false;
                midtransPayButton.innerText = 'Bayar dengan Midtrans';
            }

            if (midtransPayButton) {
                midtransPayButton.addEventListener('click', () => {
                    if (!window.snap) {
                        alert('Snap Midtrans belum berhasil dimuat. Silakan muat ulang halaman.');
                        return;
                    }

                    midtransPayButton.disabled = true;
                    midtransPayButton.innerText = 'Membuka Midtrans...';

                    window.snap.pay(@js($transaction->snap_token), {
                        onSuccess: function () {
                            window.location.href = @js(route('orders.payment-success', $transaction));
                        },
                        onPending: function () {
                            window.location.href = @js(route('checkout.success', $transaction));
                        },
                        onError: function () {
                            alert('Pembayaran belum berhasil. Silakan coba lagi.');
                            resetMidtransButton();
                        },
                        onClose: resetMidtransButton
                    });
                });
            }
        </script>
    @endif

</body>
</html>
