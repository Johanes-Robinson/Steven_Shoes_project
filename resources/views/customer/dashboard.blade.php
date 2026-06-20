<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Belanja - Steven Shoes</title>
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
                            card: '#FFFDF9'       /* Background card sedikit lebih terang */
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
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-brand-bg font-sans min-h-screen text-brand-dark overflow-x-hidden antialiased">

    <!-- JEMBATAN DATA LARAVEL ASLI KE JAVASCRIPT -->
    <div id="laravel-bridge-data" class="hidden" 
         data-cart="{{ json_encode($cartItems ?? []) }}"
         data-user-name="{{ $user->name ?? '' }}">
    </div>

    <div class="min-h-screen flex flex-col md:flex-row">
        
        <!-- SIDEBAR: Navigasi Kiri -->
        <aside class="w-full md:w-64 lg:w-72 bg-[#FAF6EE] border-r border-[#E4D5BE] flex flex-col justify-between p-6 shrink-0 z-20">
            <div>
                <!-- Brand Logo -->
                <div class="flex items-center justify-between pb-8 border-b border-[#E4D5BE]">
                    <a href="/" class="font-serif text-xl font-bold tracking-wider hover:opacity-85 transition-opacity">
                        STEVEN SHOES
                    </a>
                </div>

                <!-- Profile Brief -->
                <div class="py-6 flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-brand-dark text-[#FAF6EE] flex items-center justify-center font-serif text-lg font-bold">
                        {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm leading-tight">{{ $user->name ?? 'Pembeli' }}</h4>
                        <span class="text-xs text-brand-secondary">Akun Pembeli</span>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="space-y-1.5 pt-4">
                    <!-- Menu Utama: Produk -->
                    <button onclick="switchTab('toko')" id="btn-tab-toko" class="sidebar-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all bg-brand-dark text-white shadow-md shadow-brand-dark/15">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Produk
                    </button>

                    <!-- Menu 2: Pesanan Saya -->
                    <button onclick="switchTab('pesanan')" id="btn-tab-pesanan" class="sidebar-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-brand-secondary hover:text-brand-dark hover:bg-[#F3ECDF] transition-all">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Pesanan Saya
                        <span class="ml-auto bg-brand-accent/30 text-brand-dark text-[10px] px-2 py-0.5 rounded-full font-bold">
                            {{ count($orders ?? []) }}
                        </span>
                    </button>

                    <!-- Menu 3: Pengaturan Akun -->
                    <button onclick="switchTab('profil')" id="btn-tab-profil" class="sidebar-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-brand-secondary hover:text-brand-dark hover:bg-[#F3ECDF] transition-all">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan Akun
                    </button>
                </nav>
            </div>

            <!-- Keluar Akun -->
            <div class="pt-6 border-t border-[#E4D5BE] mt-8 md:mt-0">
                <form action="/logout" method="POST" id="logout-form">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-red-700 hover:bg-red-50 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar Akun
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN WORKSPACE: Area Toko & Pengaturan -->
        <main class="flex-1 p-4 sm:p-8 lg:p-12 overflow-y-auto max-w-7xl mx-auto w-full">
            
            <!-- HEADER DASHBOARD DENGAN CART COUNTER (Desain Presisi Sesuai image_a34d24.png) -->
            <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h1 class="font-serif text-3xl font-medium tracking-tight">Katalog Steven Shoes</h1>
                    <p class="text-brand-secondary text-sm mt-1">Pilih sepatu favoritmu langsung dari katalog di bawah ini.</p>
                </div>
                <div class="flex items-center gap-3 self-end sm:self-auto">
                    <!-- Cart Counter Button (Mengadopsi visualisasi presisi image_a34d24.png) -->
                    <button onclick="toggleCartDrawer()" class="bg-white hover:bg-[#F3ECDF] border border-[#EADBCE] rounded-full px-5 py-2.5 flex items-center gap-4 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-brand-dark/20">
                        <!-- Ikon Cart -->
                        <svg class="h-5 w-5 text-brand-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <!-- Badge Counter yang persis seperti image_a34d24.png -->
                        <span id="cart-counter" class="text-xs font-bold text-brand-dark bg-[#F1E6D2] px-3.5 py-1 rounded-full min-w-[28px] text-center">
                            {{ count($cartItems ?? []) }}
                        </span>
                    </button>
                </div>
            </header>

            <!-- ================= TAB 1: TOKO STEVEN (KATALOG UTAMA) ================= -->
            <div id="tab-content-toko" class="tab-content space-y-8">
                
                <!-- FILTER KATEGORI (10 Kategori Sesuai image_a3b228.png) -->
                <div>
                    <h3 class="font-serif text-lg font-semibold mb-4">Cari Berdasarkan Kategori</h3>
                    
                    <div class="flex gap-3 overflow-x-auto pb-3 no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
                        <button onclick="filterCategory('all')" id="btn-cat-all" class="category-btn shrink-0 bg-brand-dark text-white text-xs font-semibold px-5 py-3 rounded-full transition-all border border-brand-dark">
                            Semua Produk
                        </button>
                        <button onclick="filterCategory('sepatu-sneakers-wanita')" id="btn-cat-sepatu-sneakers-wanita" class="category-btn shrink-0 bg-white hover:bg-[#F3ECDF] text-brand-dark text-xs font-medium px-5 py-3 rounded-full transition-all border border-[#EADBCE]">
                            Sepatu Sneakers Wanita
                        </button>
                        <button onclick="filterCategory('sepatu-sneakers-pria')" id="btn-cat-sepatu-sneakers-pria" class="category-btn shrink-0 bg-white hover:bg-[#F3ECDF] text-brand-dark text-xs font-medium px-5 py-3 rounded-full transition-all border border-[#EADBCE]">
                            Sepatu Sneakers Pria
                        </button>
                        <button onclick="filterCategory('sandal-wanita')" id="btn-cat-sandal-wanita" class="category-btn shrink-0 bg-white hover:bg-[#F3ECDF] text-brand-dark text-xs font-medium px-5 py-3 rounded-full transition-all border border-[#EADBCE]">
                            Sandal Wanita
                        </button>
                        <button onclick="filterCategory('sepatu-lari-pria')" id="btn-cat-sepatu-lari-pria" class="category-btn shrink-0 bg-white hover:bg-[#F3ECDF] text-brand-dark text-xs font-medium px-5 py-3 rounded-full transition-all border border-[#EADBCE]">
                            Sepatu Lari Pria
                        </button>
                        <button onclick="filterCategory('sandal-pria')" id="btn-cat-sandal-pria" class="category-btn shrink-0 bg-white hover:bg-[#F3ECDF] text-brand-dark text-xs font-medium px-5 py-3 rounded-full transition-all border border-[#EADBCE]">
                            Sandal Pria
                        </button>
                        <button onclick="filterCategory('sepatu-lari-wanita')" id="btn-cat-sepatu-lari-wanita" class="category-btn shrink-0 bg-white hover:bg-[#F3ECDF] text-brand-dark text-xs font-medium px-5 py-3 rounded-full transition-all border border-[#EADBCE]">
                            Sepatu Lari Wanita
                        </button>
                        <button onclick="filterCategory('sandal-baim-viral')" id="btn-cat-sandal-baim-viral" class="category-btn shrink-0 bg-white hover:bg-[#F3ECDF] text-brand-dark text-xs font-medium px-5 py-3 rounded-full transition-all border border-[#EADBCE]">
                            Sandal Baim Viral
                        </button>
                        <button onclick="filterCategory('sandal-baim-pria')" id="btn-cat-sandal-baim-pria" class="category-btn shrink-0 bg-white hover:bg-[#F3ECDF] text-brand-dark text-xs font-medium px-5 py-3 rounded-full transition-all border border-[#EADBCE]">
                            Sandal Baim Pria
                        </button>
                        <button onclick="filterCategory('sandal-anak2')" id="btn-cat-sandal-anak2" class="category-btn shrink-0 bg-white hover:bg-[#F3ECDF] text-brand-dark text-xs font-medium px-5 py-3 rounded-full transition-all border border-[#EADBCE]">
                            Sandal Anak2
                        </button>
                        <button onclick="filterCategory('sandal-crocs-original')" id="btn-cat-sandal-crocs-original" class="category-btn shrink-0 bg-white hover:bg-[#F3ECDF] text-brand-dark text-xs font-medium px-5 py-3 rounded-full transition-all border border-[#EADBCE]">
                            Sandal Crocs Original
                        </button>
                    </div>
                </div>

                <!-- GRID KATALOG PRODUK -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="product-grid">
                    @forelse($products ?? [] as $product)
                        <div class="product-card bg-white border border-[#EADBCE] rounded-3xl p-4 flex flex-col justify-between hover:shadow-lg transition-all" data-category="{{ $product->category_slug }}">
                            <div>
                                <div class="relative rounded-2xl overflow-hidden aspect-square bg-[#F3ECDF] mb-4">
                                    <img src="{{ $product->image_url }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                                         onerror="this.onerror=null; this.src='https://placehold.co/400x400/F3ECDF/3E2511?text=Steven+Shoes';">
                                </div>
                                <span class="text-[10px] uppercase font-bold text-brand-secondary tracking-wider block">
                                    {{ $product->category_name }}
                                </span>
                                <h4 class="font-serif text-base font-semibold text-brand-dark mt-1 line-clamp-1">{{ $product->name }}</h4>
                                <p class="text-xs text-brand-secondary mt-1 line-clamp-2">{{ $product->description }}</p>
                            </div>
                            <div class="flex items-center justify-between pt-4 mt-4 border-t border-[#FAF6EE]">
                                <span class="font-serif text-base font-bold text-brand-dark">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                
                                <button onclick="addToCart(@js($product->id), @js($product->name), @js((float) $product->price), @js($product->image_url))" 
                                        class="bg-brand-dark text-white p-2.5 rounded-xl hover:bg-brand-dark/95 transition-all flex items-center justify-center"
                                        title="Masukkan ke keranjang"
                                        aria-label="Masukkan {{ $product->name }} ke keranjang">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center bg-white border border-[#EADBCE] rounded-3xl p-8">
                            <h3 class="font-serif text-xl font-semibold">Katalog Masih Kosong</h3>
                            <p class="text-sm text-brand-secondary mt-1">Admin sedang menata produk terbaik untuk Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ================= TAB 2: PESANAN SAYA ================= -->
            <div id="tab-content-pesanan" class="tab-content hidden space-y-6">
                <div>
                    <h2 class="font-serif text-2xl font-semibold">Riwayat Pesanan</h2>
                    <p class="text-xs text-brand-secondary mt-1">Lacak status pesanan sepatu yang telah Anda beli di sini.</p>
                </div>

                <!-- DAFTAR RIWAYAT TRANSAKSI -->
                <div class="space-y-4 max-w-4xl">
                    @forelse($orders ?? [] as $order)
                        <div class="bg-white border border-[#EADBCE] rounded-2xl p-6 space-y-4 shadow-xs">
                            <div class="flex flex-wrap justify-between items-center gap-2 pb-4 border-b border-dashed border-[#EADBCE]">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-brand-dark">
                                        {{ $order->created_at }}
                                    </span>
                                    <span class="text-brand-secondary/40">|</span>
                                    <span class="text-xs font-mono font-medium text-brand-secondary">#{{ $order->invoice_number }}</span>
                                </div>
                                <span class="px-3 py-1 rounded-full font-bold text-[10px] uppercase tracking-wider 
                                    @if($order->status == 'selesai') bg-green-500/10 text-green-700 
                                    @elseif($order->status == 'diproses') bg-blue-500/10 text-blue-700 
                                    @else bg-amber-500/10 text-amber-700 @endif">
                                    {{ $order->status }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                @foreach($order->items as $item)
                                    <div class="flex items-start gap-4">
                                        <img src="{{ $item->product->image_url }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-16 h-16 rounded-xl object-cover border border-[#EADBCE]"
                                             onerror="this.onerror=null; this.src='https://placehold.co/100x100/F3ECDF/3E2511?text=Sepatu';">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-sm text-brand-dark">{{ $item->product->name }}</h4>
                                            <p class="text-xs text-brand-secondary mt-0.5">Kuantitas: {{ $item->quantity }} pcs</p>
                                            <p class="text-sm font-serif font-bold text-brand-dark mt-2">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="pt-4 border-t border-[#FAF6EE] flex justify-between items-center">
                                <span class="text-xs text-brand-secondary">Total Pembelian</span>
                                <span class="font-serif text-lg font-bold text-brand-dark">Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center bg-white border border-[#EADBCE] rounded-3xl p-8">
                            <h3 class="font-serif text-xl font-semibold">Belum Ada Transaksi</h3>
                            <p class="text-sm text-brand-secondary mt-1">Silakan pilih produk kesukaan Anda di menu "Produk" untuk mulai berbelanja.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ================= TAB 3: PENGATURAN AKUN ================= -->
            <div id="tab-content-profil" class="tab-content hidden space-y-6">
                <div>
                    <h2 class="font-serif text-2xl font-semibold">Pengaturan Profil</h2>
                    <p class="text-xs text-brand-secondary mt-1">Ubah data pengiriman dan informasi detail kontak Anda di bawah ini.</p>
                </div>

                <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 sm:p-8 max-w-4xl">
                    <form action="/profile/update" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ $user->name ?? '' }}" class="w-full px-4 py-3 bg-brand-bg/40 border border-[#E4D5BE] focus:border-brand-dark rounded-xl text-brand-dark text-sm focus:outline-none transition-all">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Alamat Email</label>
                                <input type="email" name="email" value="{{ $user->email ?? '' }}" class="w-full px-4 py-3 bg-brand-bg/40 border border-[#E4D5BE] focus:border-brand-dark rounded-xl text-brand-dark text-sm focus:outline-none transition-all" readonly>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Nomor Handphone</label>
                                <input type="text" name="phone" value="{{ $user->phone ?? '' }}" class="w-full px-4 py-3 bg-brand-bg/40 border border-[#E4D5BE] focus:border-brand-dark rounded-xl text-brand-dark text-sm focus:outline-none transition-all">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Ukuran Sepatu (EU)</label>
                                <select name="shoe_size" class="w-full px-4 py-3 bg-brand-bg/40 border border-[#E4D5BE] focus:border-brand-dark rounded-xl text-brand-dark text-sm focus:outline-none transition-all">
                                    <option value="">Pilih Ukuran</option>
                                    @for($i = 36; $i <= 46; $i++)
                                        <option value="{{ $i }}" {{ ($user->shoe_size ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Alamat Utama Pengiriman</label>
                            <textarea name="address" rows="3" class="w-full px-4 py-3 bg-brand-bg/40 border border-[#E4D5BE] focus:border-brand-dark rounded-xl text-brand-dark text-sm focus:outline-none transition-all">{{ $user->address ?? '' }}</textarea>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="bg-brand-dark hover:bg-brand-dark/95 text-white font-semibold text-sm px-8 py-3.5 rounded-full transition-all transform hover:-translate-y-0.5">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <!-- ================= SLIDE-OVER CART DRAWER (MENDUKUNG MULTI-KONDISI REALTIME) ================= -->
    <div id="cart-drawer" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Backdrop Overlay -->
            <div onclick="toggleCartDrawer()" class="absolute inset-0 bg-brand-dark/40 backdrop-blur-xs transition-opacity" aria-hidden="true"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="pointer-events-auto w-screen max-w-md transform transition-all duration-500 ease-in-out translate-x-full" id="cart-drawer-panel">
                    <div class="flex h-full flex-col overflow-y-scroll bg-[#FAF6EE] border-l border-[#E4D5BE] shadow-2xl">
                        
                        <!-- Header Drawer -->
                        <div class="p-6 border-b border-[#E4D5BE] flex items-center justify-between">
                            <h2 class="font-serif text-xl font-bold tracking-wide" id="slide-over-title">Keranjang Belanja</h2>
                            <button onclick="toggleCartDrawer()" class="text-brand-secondary hover:text-brand-dark p-1 rounded-full hover:bg-[#F3ECDF] transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- DAFTAR ITEM BELANJA (KONDISI NATIVE LARAVEL BLADE) -->
                        <div class="flex-1 overflow-y-auto p-6 space-y-4 no-scrollbar" id="cart-items-container">
                            
                            @if(isset($cartItems) && count($cartItems) > 0)
                                <!-- KONDISI 1: ADA PRODUK (Server-Side) -->
                                <div id="cart-list" class="space-y-4">
                                    @foreach($cartItems as $item)
                                        <div class="flex items-center gap-4 bg-white border border-[#EADBCE] p-3 rounded-2xl transition-all" data-item-id="{{ $item->id }}">
                                            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-16 h-16 rounded-xl object-cover border border-[#EADBCE]" onerror="this.src='https://placehold.co/100x100/F3ECDF/3E2511?text=Sepatu'">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-bold text-xs text-brand-dark truncate">{{ $item->name }}</h4>
                                                <p class="text-xs font-serif font-bold text-brand-dark mt-1">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                                
                                                <div class="flex items-center gap-2.5 mt-2">
                                                    <button onclick="updateCartQuantity('{{ $item->id }}', -1)" class="w-6 h-6 rounded-full bg-brand-bg hover:bg-[#EADBCE] text-brand-dark text-xs font-bold flex items-center justify-center">-</button>
                                                    <span class="text-xs font-bold">{{ $item->quantity }}</span>
                                                    <button onclick="updateCartQuantity('{{ $item->id }}', 1)" class="w-6 h-6 rounded-full bg-brand-bg hover:bg-[#EADBCE] text-brand-dark text-xs font-bold flex items-center justify-center">+</button>
                                                </div>
                                            </div>
                                            <button onclick="removeCartItem('{{ $item->id }}')" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- KONDISI 2: KERANJANG KOSONG (Sesuai image_a2c9e0.png) -->
                                <div id="cart-empty-state" class="text-center py-20 flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-[#F1E6D2] rounded-full flex items-center justify-center mb-6">
                                        <svg class="h-8 w-8 text-brand-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-serif font-bold text-2xl text-brand-dark">Keranjang Masih Kosong</h4>
                                    <p class="text-sm text-brand-secondary mt-2 max-w-[280px] leading-relaxed">Pilih sepatu impianmu dari katalog dan masukkan ke keranjang.</p>
                                </div>
                            @endif

                            <!-- Container Render Sisi Client-Side (Saat user menambah produk secara langsung tanpa reload) -->
                            <div id="cart-list-js" class="space-y-4 hidden"></div>

                        </div>

                        <!-- Footer Drawer (Perhitungan Total & Checkout) -->
                        <div class="border-t border-[#E4D5BE] p-6 bg-white space-y-4">
                            <div class="flex justify-between text-sm font-medium">
                                <p class="text-brand-secondary">Subtotal</p>
                                <p id="cart-subtotal" class="font-serif font-bold text-lg text-brand-dark">
                                    Rp {{ number_format($cartSubtotal ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                            <p class="text-[11px] text-brand-secondary leading-relaxed">Pengiriman dan biaya tambahan akan dihitung otomatis saat pembuatan tagihan pesanan.</p>
                            
                            <div class="pt-2">
                                <button onclick="checkoutCart()" id="btn-checkout-submit" class="w-full bg-brand-dark hover:bg-brand-dark/95 text-white font-semibold text-sm py-4 rounded-xl shadow-lg shadow-brand-dark/15 transition-all text-center flex justify-center items-center gap-2">
                                    <span id="btn-checkout-text">Proses Pembayaran (Checkout)</span>
                                    <svg id="checkout-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification System -->
    <div id="toast" class="fixed bottom-6 right-6 bg-brand-dark border border-brand-accent/20 text-white text-xs font-semibold tracking-wider px-6 py-3.5 rounded-2xl shadow-xl transition-all duration-300 z-50 flex items-center gap-2 opacity-0 pointer-events-none">
        <span id="toast-message">✓ Berhasil!</span>
    </div>

    <!-- Script Manajemen Interaktif Sisi Browser -->
    <script>
        let cart = [];

        try {
            const bridgeEl = document.getElementById('laravel-bridge-data');
            if (bridgeEl) {
                const rawCart = bridgeEl.getAttribute('data-cart');
                if (rawCart) {
                    cart = JSON.parse(rawCart);
                }
            }
        } catch (e) {
            console.warn("Gagal membaca inisialisasi data Laravel.");
            cart = [];
        }

        function cartItemProductId(item) {
            return String(item.product_id || item.id);
        }

        function cartItemMatches(item, id) {
            return String(item.id) === String(id) || cartItemProductId(item) === String(id);
        }

        function cartItemQuantity(item) {
            return Math.max(1, Number.parseInt(item.quantity, 10) || 1);
        }

        function cartItemPrice(item) {
            return Number.parseFloat(item.price) || 0;
        }

        function cartCheckoutPayload() {
            return cart.map(item => ({
                id: cartItemProductId(item),
                quantity: cartItemQuantity(item)
            }));
        }

        function toggleCartDrawer() {
            const drawer = document.getElementById('cart-drawer');
            const panel = document.getElementById('cart-drawer-panel');
            
            if (drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                setTimeout(() => {
                    panel.classList.remove('translate-x-full');
                }, 50);
            } else {
                panel.classList.add('translate-x-full');
                setTimeout(() => {
                    drawer.classList.add('hidden');
                }, 400);
            }
        }

        function addToCart(id, name, price, imageUrl) {
            const existingItem = cart.find(item => cartItemMatches(item, id));
            
            if (existingItem) {
                existingItem.quantity = cartItemQuantity(existingItem) + 1;
            } else {
                cart.push({
                    id: id,
                    product_id: id,
                    name: name,
                    price: price,
                    image: imageUrl,
                    quantity: 1
                });
            }

            updateCartUI();
            showInstantToast(`✓ ${name} dimasukkan ke keranjang!`);
        }

        function updateCartQuantity(id, change) {
            const item = cart.find(item => cartItemMatches(item, id));
            if (item) {
                item.quantity = cartItemQuantity(item) + change;
                if (item.quantity <= 0) {
                    cart = cart.filter(i => !cartItemMatches(i, id));
                }
            }
            updateCartUI();
        }

        function removeCartItem(id) {
            cart = cart.filter(item => !cartItemMatches(item, id));
            updateCartUI();
        }

        function updateCartUI() {
            const cartEmptyState = document.getElementById('cart-empty-state');
            const cartListServer = document.getElementById('cart-list');
            const cartListJs = document.getElementById('cart-list-js');
            const counter = document.getElementById('cart-counter');
            const subtotalText = document.getElementById('cart-subtotal');

            // Sembunyikan server-side render list agar digantikan JS render yang interaktif secara real-time
            if (cartListServer) {
                cartListServer.classList.add('hidden');
            }

            let totalItems = 0;
            let totalPrice = 0;

            cart.forEach(item => {
                const quantity = cartItemQuantity(item);
                totalItems += quantity;
                totalPrice += (cartItemPrice(item) * quantity);
            });

            if (counter) {
                counter.innerText = totalItems;
            }

            if (cart.length === 0) {
                if (cartEmptyState) cartEmptyState.classList.remove('hidden');
                if (cartListJs) {
                    cartListJs.classList.add('hidden');
                    cartListJs.innerHTML = '';
                }
                if (subtotalText) subtotalText.innerText = 'Rp 0';
            } else {
                if (cartEmptyState) cartEmptyState.classList.add('hidden');
                
                if (cartListJs) {
                    cartListJs.classList.remove('hidden');
                    cartListJs.innerHTML = cart.map(item => `
                        <div class="flex items-center gap-4 bg-white border border-[#EADBCE] p-3 rounded-2xl transition-all" data-item-id="${item.id}">
                            <img src="${item.image}" alt="${item.name}" class="w-16 h-16 rounded-xl object-cover border border-[#EADBCE]" onerror="this.src='https://placehold.co/100x100/F3ECDF/3E2511?text=Sepatu'">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-xs text-brand-dark truncate">${item.name}</h4>
                                <p class="text-xs font-serif font-bold text-brand-dark mt-1">Rp ${cartItemPrice(item).toLocaleString('id-ID')}</p>
                                
                                <div class="flex items-center gap-2.5 mt-2">
                                    <button onclick="updateCartQuantity('${item.id}', -1)" class="w-6 h-6 rounded-full bg-brand-bg hover:bg-[#EADBCE] text-brand-dark text-xs font-bold flex items-center justify-center">-</button>
                                    <span class="text-xs font-bold">${cartItemQuantity(item)}</span>
                                    <button onclick="updateCartQuantity('${item.id}', 1)" class="w-6 h-6 rounded-full bg-brand-bg hover:bg-[#EADBCE] text-brand-dark text-xs font-bold flex items-center justify-center">+</button>
                                </div>
                            </div>
                            <button onclick="removeCartItem('${item.id}')" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    `).join('');
                }

                if (subtotalText) {
                    subtotalText.innerText = 'Rp ' + totalPrice.toLocaleString('id-ID');
                }
            }
        }

        function showInstantToast(message) {
            const toast = document.getElementById('toast');
            const msgEl = document.getElementById('toast-message');
            
            if (toast && msgEl) {
                msgEl.innerText = message;
                toast.classList.remove('opacity-0', 'pointer-events-none');
                
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'pointer-events-none');
                }, 3000);
            }
        }

        function switchTab(tabName) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.add('hidden'));

            const targetTab = document.getElementById('tab-content-' + tabName);
            if (targetTab) { targetTab.classList.remove('hidden'); }

            const sidebarButtons = document.querySelectorAll('.sidebar-btn');
            sidebarButtons.forEach(button => {
                button.classList.remove('bg-brand-dark', 'text-white', 'shadow-md', 'shadow-brand-dark/15');
                button.classList.add('text-brand-secondary', 'hover:text-brand-dark', 'hover:bg-[#F3ECDF]');
                button.classList.replace('font-semibold', 'font-medium');
            });

            const activeBtn = document.getElementById('btn-tab-' + tabName);
            if (activeBtn) {
                activeBtn.classList.add('bg-brand-dark', 'text-white', 'shadow-md', 'shadow-brand-dark/15');
                activeBtn.classList.remove('text-brand-secondary', 'hover:text-brand-dark', 'hover:bg-[#F3ECDF]');
                activeBtn.classList.replace('font-medium', 'font-semibold');
            }
        }

        function filterCategory(categorySlug) {
            const catButtons = document.querySelectorAll('.category-btn');
            catButtons.forEach(btn => {
                btn.classList.remove('bg-brand-dark', 'text-white', 'font-semibold');
                btn.classList.add('bg-white', 'text-brand-dark', 'font-medium');
            });

            const activeBtn = document.getElementById('btn-cat-' + categorySlug);
            if (activeBtn) {
                activeBtn.classList.add('bg-brand-dark', 'text-white', 'font-semibold');
                activeBtn.classList.remove('bg-white', 'text-brand-dark', 'font-medium');
            }

            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (categorySlug === 'all' || cardCat === categorySlug) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }

        function checkoutCart() {
            if (cart.length === 0) {
                showInstantToast('⚠ Keranjang belanja Anda masih kosong!');
                return;
            }

            const btnText = document.getElementById('btn-checkout-text');
            const spinner = document.getElementById('checkout-spinner');
            const submitBtn = document.getElementById('btn-checkout-submit');

            submitBtn.disabled = true;
            btnText.innerText = 'Memproses...';
            spinner.classList.remove('hidden');

            fetch('/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ cart: cartCheckoutPayload() })
            })
            .then(response => {
                const contentType = response.headers.get('content-type') || '';

                if (!contentType.includes('application/json')) {
                    window.location.href = '/login';
                    throw new Error('Sesi login habis');
                }

                return response.json().then(data => {
                    if (!response.ok) { throw new Error(data.message || 'Checkout gagal'); }
                    return data;
                });
            })
            .then(data => {
                showInstantToast('Keranjang siap diproses!');
                cart = [];
                updateCartUI();
                toggleCartDrawer();
                setTimeout(() => { window.location.href = data.redirect || '/checkout'; }, 700);
            })
            .catch(error => {
                showInstantToast(`Checkout gagal: ${error.message}`);
            })
            .finally(() => {
                submitBtn.disabled = false;
                btnText.innerText = 'Proses Pembayaran (Checkout)';
                spinner.classList.add('hidden');
            });
        }
    </script>
</body>
</html>
