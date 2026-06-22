<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="4;url={{ route('customer.dashboard') }}">
    <title>Pembayaran Berhasil - Steven Shoes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#FAF6EE',
                            dark: '#3E2511',
                            secondary: '#6E5D4F',
                            accent: '#D9B78D',
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
</head>
<body class="bg-brand-bg font-sans min-h-screen text-brand-dark antialiased flex flex-col">

    <header class="max-w-4xl mx-auto w-full px-6 py-6 flex items-center justify-between border-b border-[#E4D5BE]">
        <a href="/" class="font-serif text-xl font-bold tracking-wider text-brand-dark hover:opacity-85 transition-opacity">
            STEVEN SHOES
        </a>
        <span class="text-xs font-semibold uppercase tracking-wider text-green-700 bg-green-50 px-3 py-1 rounded-full">
            Pembayaran Berhasil
        </span>
    </header>

    <main class="max-w-xl mx-auto w-full px-6 py-12 flex-1 flex items-center">
        <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 sm:p-10 shadow-lg text-center space-y-8 w-full">
            <div class="mx-auto w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center text-green-600">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <div class="space-y-2">
                <h1 class="font-serif text-3xl font-medium tracking-tight">Pembayaran Berhasil</h1>
                <p class="text-brand-secondary text-sm leading-relaxed">
                    Terima kasih. Pesanan Anda sudah masuk ke tahap diproses dan akan segera ditangani oleh admin Steven Shoes.
                </p>
            </div>

            <div class="bg-[#FAF6EE] border border-[#EADBCE] rounded-2xl p-5 space-y-3">
                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="text-brand-secondary">Invoice</span>
                    <span class="font-mono font-bold text-brand-dark">#{{ $transaction->invoice_number }}</span>
                </div>
                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="text-brand-secondary">Total Pembayaran</span>
                    <span class="font-serif font-bold text-brand-dark">Rp {{ number_format($transaction->total_amount ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="text-brand-secondary">Status Pesanan</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full">
                        {{ $transaction->status }}
                    </span>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-xs text-brand-secondary">Anda akan dialihkan ke dashboard dalam beberapa detik.</p>
                <a href="{{ route('customer.dashboard') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-brand-dark px-4 py-3.5 text-xs font-semibold text-white transition-all hover:bg-brand-dark/95">
                    Kembali Ke Dashboard
                </a>
            </div>
        </div>
    </main>

    <footer class="max-w-4xl mx-auto w-full px-6 py-6 text-center text-[10px] text-brand-secondary">
        &copy; 2026 STEVEN SHOES. All rights reserved.
    </footer>

</body>
</html>
