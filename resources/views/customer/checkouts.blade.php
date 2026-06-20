<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pembayaran - Steven Shoes</title>
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
<body class="bg-brand-bg font-sans min-h-screen text-brand-dark overflow-x-hidden antialiased">

    <!-- Header Navigation -->
    <header class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-16 py-6 flex items-center justify-between border-b border-[#E4D5BE]">
        <a href="/" class="font-serif text-2xl font-bold tracking-wider text-brand-dark hover:opacity-85 transition-opacity">
            STEVEN SHOES
        </a>
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-secondary">
            <span class="text-brand-dark">Satu Langkah Lagi</span>
            <span>•</span>
            <span>Checkout Aman</span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-16 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- KIRI: FORM CHECKOUT (7 Kolom) -->
            <div class="lg:col-span-7 space-y-8">
                <div>
                    <h1 class="font-serif text-3xl sm:text-4xl font-medium tracking-tight">Detail Pengiriman & Pembayaran</h1>
                    <p class="text-brand-secondary text-sm mt-2">Mohon lengkapi alamat pengiriman serta pilih metode pembayaran untuk menyelesaikan pesanan Anda.</p>
                </div>

                <form action="/checkout/process" method="POST" class="space-y-6">
                    @csrf

                    <!-- 1. Alamat Pengiriman -->
                    <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 sm:p-8 space-y-4 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-[#FAF6EE] pb-3">
                            <span class="w-8 h-8 rounded-full bg-brand-dark text-white flex items-center justify-center font-bold text-sm">1</span>
                            <h3 class="font-serif text-lg font-semibold">Alamat Pengiriman</h3>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="shipping_address" class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Alamat Lengkap Penerima</label>
                            <textarea 
                                name="shipping_address" 
                                id="shipping_address" 
                                rows="4" 
                                required
                                placeholder="Tuliskan alamat lengkap pengiriman Anda (Nama Jalan, Blok, RT/RW, Kecamatan, Kota, Kode Pos)"
                                class="w-full px-4 py-3 bg-brand-bg/30 border border-[#E4D5BE] focus:border-brand-dark rounded-2xl text-brand-dark text-sm focus:outline-none transition-all placeholder:text-brand-secondary/50"
                            >{{ old('shipping_address', $user->address ?? '') }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 2. Kurir Pengiriman -->
                    <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 sm:p-8 space-y-4 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-[#FAF6EE] pb-3">
                            <span class="w-8 h-8 rounded-full bg-brand-dark text-white flex items-center justify-center font-bold text-sm">2</span>
                            <h3 class="font-serif text-lg font-semibold">Opsi Kurir Pengiriman</h3>
                        </div>

                        <div class="space-y-2">
                            <label for="shipping_courier" class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Pilih Kurir Ekspedisi</label>
                            <div class="relative">
                                <select 
                                    name="shipping_courier" 
                                    id="shipping_courier" 
                                    required 
                                    class="w-full px-4 py-3.5 bg-brand-bg/30 border border-[#E4D5BE] focus:border-brand-dark rounded-2xl text-brand-dark text-sm focus:outline-none transition-all appearance-none cursor-pointer"
                                >
                                    <option value="JNE" {{ old('shipping_courier') == 'JNE' ? 'selected' : '' }}>JNE - Layanan Reguler Express</option>
                                    <option value="J&T" {{ old('shipping_courier') == 'J&T' ? 'selected' : '' }}>J&T - Kiriman Cepat Hemat</option>
                                    <option value="Onsite" {{ old('shipping_courier') == 'Onsite' ? 'selected' : '' }}>Ambil di Toko (Onsite) - Tanpa Ongkos Kirim</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-brand-dark">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Metode Pembayaran -->
                    <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 sm:p-8 space-y-4 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-[#FAF6EE] pb-3">
                            <span class="w-8 h-8 rounded-full bg-brand-dark text-white flex items-center justify-center font-bold text-sm">3</span>
                            <h3 class="font-serif text-lg font-semibold">Metode Pembayaran</h3>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary mb-1">Pilih Cara Membayar</label>
                            
                            <!-- Opsi 1: BCA VA -->
                            <label class="flex items-center justify-between p-4 bg-brand-bg/20 hover:bg-brand-bg/40 border border-[#E4D5BE] rounded-2xl cursor-pointer transition-all">
                                <div class="flex items-center gap-4">
                                    <input type="radio" name="payment_method" value="bca" required class="accent-brand-dark w-4 h-4" {{ old('payment_method') == 'bca' ? 'checked' : '' }}>
                                    <div>
                                        <p class="text-sm font-semibold text-brand-dark">Virtual Account BCA</p>
                                        <p class="text-xs text-brand-secondary mt-0.5">Transfer via m-BCA, KlikBCA, atau ATM BCA</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded">BCA</span>
                            </label>

                            <!-- Opsi 2: Mandiri VA -->
                            <label class="flex items-center justify-between p-4 bg-brand-bg/20 hover:bg-brand-bg/40 border border-[#E4D5BE] rounded-2xl cursor-pointer transition-all">
                                <div class="flex items-center gap-4">
                                    <input type="radio" name="payment_method" value="mandiri" class="accent-brand-dark w-4 h-4" {{ old('payment_method') == 'mandiri' ? 'checked' : '' }}>
                                    <div>
                                        <p class="text-sm font-semibold text-brand-dark">Virtual Account Mandiri</p>
                                        <p class="text-xs text-brand-secondary mt-0.5">Transfer via Livin' by Mandiri atau ATM Mandiri</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-yellow-700 bg-yellow-50 px-2.5 py-1 rounded">MANDIRI</span>
                            </label>

                            <!-- Opsi 3: QRIS -->
                            <label class="flex items-center justify-between p-4 bg-brand-bg/20 hover:bg-brand-bg/40 border border-[#E4D5BE] rounded-2xl cursor-pointer transition-all">
                                <div class="flex items-center gap-4">
                                    <input type="radio" name="payment_method" value="qris" class="accent-brand-dark w-4 h-4" {{ old('payment_method') == 'qris' ? 'checked' : '' }}>
                                    <div>
                                        <p class="text-sm font-semibold text-brand-dark">QRIS Pembayaran Instan</p>
                                        <p class="text-xs text-brand-secondary mt-0.5">Scan otomatis via GoPay, OVO, ShopeePay, Dana, dll</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded">QRIS</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tombol Proses Pembayaran -->
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-brand-dark hover:bg-brand-dark/95 text-white font-semibold text-sm py-4.5 rounded-2xl shadow-lg shadow-brand-dark/15 transition-all text-center flex justify-center items-center gap-2 transform hover:-translate-y-0.5 duration-300">
                            Proses Pembayaran
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>

                </form>
            </div>

            <!-- KANAN: RINGKASAN BELANJA (5 Kolom) -->
            <div class="lg:col-span-5 lg:sticky lg:top-8 space-y-6">
                <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 sm:p-8 space-y-6 shadow-sm">
                    <h3 class="font-serif text-xl font-semibold border-b border-[#FAF6EE] pb-4">Ringkasan Belanja</h3>

                    <!-- Daftar Item Di Dalam Keranjang -->
                    <div class="space-y-4 max-h-72 overflow-y-auto pr-2 no-scrollbar">
                        @forelse($cartItems ?? [] as $item)
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-[#F3ECDF] border border-[#EADBCE] shrink-0">
                                    <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/100x100/F3ECDF/3E2511?text=Sepatu'">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-xs text-brand-dark truncate">{{ $item->name }}</h4>
                                    <p class="text-[10px] text-brand-secondary mt-0.5">Jumlah: {{ $item->quantity }} pasang</p>
                                    <p class="text-xs font-serif font-bold text-brand-dark mt-1">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-brand-secondary py-4 text-center">Tidak ada produk dalam keranjang.</p>
                        @endforelse
                    </div>

                    <!-- Perhitungan Harga -->
                    <div class="space-y-3 pt-6 border-t border-[#FAF6EE] text-sm">
                        <div class="flex justify-between">
                            <span class="text-brand-secondary">Subtotal Sepatu</span>
                            <span class="font-medium">Rp {{ number_format($cartSubtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-brand-secondary">Biaya Pengiriman</span>
                            <span class="font-medium text-green-700">Gratis Ongkir</span>
                        </div>
                        <div class="flex justify-between pt-4 border-t border-[#FAF6EE] text-base font-bold">
                            <span class="font-serif text-brand-dark">Total Pembayaran</span>
                            <span class="font-serif text-lg text-brand-dark">
                                Rp {{ number_format($cartSubtotal ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Jaminan -->
                <div class="flex items-start gap-4 p-5 bg-[#FAF6EE] border border-[#EADBCE] rounded-2xl text-xs">
                    <svg class="h-6 w-6 text-brand-dark shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <div class="space-y-1">
                        <h4 class="font-bold text-brand-dark">Jaminan Keamanan Steven Shoes</h4>
                        <p class="text-brand-secondary leading-relaxed">Setiap transaksi Anda dienkripsi penuh menggunakan sistem gateway pembayaran yang resmi dan terlindungi.</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
