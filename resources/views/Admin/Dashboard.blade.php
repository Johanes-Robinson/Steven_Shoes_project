<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Steven Shoes</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght=0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
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
<body class="bg-[#F8F4EB] font-sans min-h-screen text-brand-dark overflow-x-hidden antialiased flex flex-col md:flex-row">

    <!-- SIDEBAR UTAMA ADMIN -->
    <aside class="w-full md:w-64 lg:w-72 bg-brand-dark text-[#FAF6EE] flex flex-col justify-between p-6 shrink-0 z-20 md:min-h-screen">
        <div>
            <!-- Brand Logo -->
            <div class="flex items-center justify-between pb-8 border-b border-white/10">
                <a href="/admin/dashboard" class="font-serif text-xl font-bold tracking-wider text-white hover:opacity-85 transition-opacity">
                    STEVEN ADMIN
                </a>
                <span class="text-[9px] font-bold uppercase tracking-widest bg-brand-accent text-brand-dark px-2.5 py-1 rounded">
                    PRO PANEL
                </span>
            </div>

            <!-- Profile Admin -->
            <div class="py-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-brand-accent text-brand-dark flex items-center justify-center font-serif text-base font-bold">
                    A
                </div>
                <div>
                    <h4 class="font-semibold text-xs text-white leading-tight">Administrator</h4>
                    <span class="text-[10px] text-white/50">Kelola Toko Sepatu</span>
                </div>
            </div>

            <!-- Menu Navigasi Admin -->
            <nav class="space-y-1.5 pt-4">
                <!-- Tab 1: Ringkasan -->
                <button onclick="switchAdminTab('overview')" id="btn-tab-overview" class="admin-sidebar-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold tracking-wider uppercase transition-all bg-brand-accent text-brand-dark shadow-md">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                    </svg>
                    Ringkasan Toko
                </button>

                <!-- Tab 2: Kelola Produk -->
                <button onclick="switchAdminTab('products')" id="btn-tab-products" class="admin-sidebar-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-medium tracking-wider uppercase text-white/70 hover:text-white hover:bg-white/5 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V3m-8 8v10l8-4" />
                    </svg>
                    Kelola Produk
                </button>

                <!-- Tab 3: Kelola Pesanan -->
                <button onclick="switchAdminTab('orders')" id="btn-tab-orders" class="admin-sidebar-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-medium tracking-wider uppercase text-white/70 hover:text-white hover:bg-white/5 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Kelola Pesanan
                </button>
            </nav>
        </div>

        <!-- Keluar Akun Admin -->
        <div class="pt-6 border-t border-white/10 mt-8 md:mt-0">
            <form action="/logout" method="POST" id="admin-logout-form">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold uppercase tracking-wider text-red-400 hover:bg-red-500/10 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar Admin
                </button>
            </form>
        </div>
    </aside>

    <!-- WORKSPACE UTAMA -->
    <main class="flex-1 p-4 sm:p-8 lg:p-12 overflow-y-auto max-w-7xl w-full mx-auto">
        
        <!-- ================= TAB 1: OVERVIEW ================= -->
        <div id="admin-tab-overview" class="admin-tab-content space-y-8">
            
            <!-- Header Ringkasan -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="font-serif text-3xl font-medium tracking-tight">Ringkasan Aktivitas Toko</h1>
                    <p class="text-brand-secondary text-sm mt-1">Pantau perkembangan bisnis Steven Shoes secara real-time.</p>
                </div>
            </div>

            <!-- KARTU STATISTIK (Dinamis dari Controller Laravel) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Pendapatan Total -->
                <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-green-500/10 text-green-700 flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-brand-secondary">Total Pendapatan</span>
                        <h3 class="font-serif text-xl font-bold mt-1 text-brand-dark">
                            Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>

                <!-- Jumlah Pesanan -->
                <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-700 flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-brand-secondary">Pesanan Masuk</span>
                        <h3 class="font-serif text-xl font-bold mt-1 text-brand-dark">
                            {{ $stats['total_orders'] ?? 0 }} Transaksi
                        </h3>
                    </div>
                </div>

                <!-- Total Produk -->
                <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-700 flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V3m-8 8v10l8-4" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-brand-secondary">Koleksi Produk</span>
                        <h3 class="font-serif text-xl font-bold mt-1 text-brand-dark">
                            {{ $stats['total_products'] ?? 0 }} Sepatu
                        </h3>
                    </div>
                </div>

                <!-- Pelanggan Aktif -->
                <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-700 flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-brand-secondary">Pelanggan</span>
                        <h3 class="font-serif text-xl font-bold mt-1 text-brand-dark">
                            {{ $stats['total_customers'] ?? 0 }} Akun
                        </h3>
                    </div>
                </div>
            </div>

            <!-- PESANAN TERBARU (SISI OVERVIEW) -->
            <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#F8F4EB]">
                    <h3 class="font-serif text-lg font-semibold">Pesanan Terbaru Menunggu Konfirmasi</h3>
                    <button onclick="switchAdminTab('orders')" class="text-xs font-bold text-brand-secondary hover:text-brand-dark transition-colors">
                        Lihat Semua Pesanan →
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-brand-secondary uppercase tracking-wider font-semibold border-b border-[#F8F4EB]">
                                <th class="pb-3 pr-4">Invoice</th>
                                <th class="pb-3 pr-4">Tanggal</th>
                                <th class="pb-3 pr-4">Penerima</th>
                                <th class="pb-3 pr-4">Metode Bayar</th>
                                <th class="pb-3 pr-4">Total</th>
                                <th class="pb-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F8F4EB]">
                            @forelse($recentOrders ?? [] as $order)
                                <tr>
                                    <td class="py-3.5 font-mono font-bold text-brand-dark">#{{ $order->invoice_number }}</td>
                                    <td class="py-3.5 text-brand-secondary">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                    <td class="py-3.5 text-brand-dark font-medium">{{ $order->user->name ?? 'Pembeli' }}</td>
                                    <td class="py-3.5 text-brand-secondary uppercase font-semibold">{{ $order->payment_method }}</td>
                                    <td class="py-3.5 font-semibold text-brand-dark">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                    <td class="py-3.5 text-right">
                                        <span class="px-2.5 py-1 rounded-full text-[9px] uppercase font-bold tracking-wider 
                                            @if($order->status == 'selesai') bg-green-500/10 text-green-700
                                            @elseif($order->status == 'diproses') bg-blue-500/10 text-blue-700
                                            @else bg-amber-500/10 text-amber-700 @endif">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-brand-secondary">
                                        Tidak ada pesanan terbaru saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ================= TAB 2: KELOLA PRODUK ================= -->
        <div id="admin-tab-products" class="admin-tab-content hidden space-y-6">
            
            <!-- Header Kelola Produk -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="font-serif text-3xl font-medium tracking-tight">Katalog Produk Steven</h1>
                    <p class="text-brand-secondary text-sm mt-1">Tambahkan produk baru, ubah harga, atau kelola ketersediaan stok sepatu.</p>
                </div>
                
                <!-- Tombol Tambah Produk (Membuka Modal) -->
                <button onclick="toggleProductModal(true)" class="bg-brand-dark hover:bg-brand-dark/95 text-white font-semibold text-xs tracking-wider uppercase px-5 py-3 rounded-xl flex items-center gap-2 transition-all shadow-md">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Sepatu Baru
                </button>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl p-4 text-xs leading-relaxed">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-2xl p-4 text-xs leading-relaxed">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tabel Daftar Produk -->
            <div class="bg-white border border-[#EADBCE] rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#FAF6EE] text-brand-secondary border-b border-[#EADBCE]">
                            <tr class="uppercase tracking-wider font-semibold">
                                <th class="p-4 pr-6 pl-6">Foto</th>
                                <th class="p-4 pr-6">Nama Sepatu</th>
                                <th class="p-4 pr-6">Kategori</th>
                                <th class="p-4 pr-6">Harga</th>
                                <th class="p-4 pr-6 text-center">Status Ketersediaan</th>
                                <th class="p-4 pr-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F8F4EB]">
                            @forelse($products ?? [] as $product)
                                <tr class="hover:bg-brand-bg/10 transition-colors">
                                    <!-- Foto Pembuka -->
                                    <td class="p-4 pl-6">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-xl object-cover border border-[#EADBCE]" onerror="this.onerror=null; this.src='https://placehold.co/100x100/FAF6EE/3E2511?text=Sepatu';">
                                    </td>
                                    <!-- Nama & Detail Singkat -->
                                    <td class="p-4">
                                        <div class="font-serif font-bold text-sm text-brand-dark">{{ $product->name }}</div>
                                        <div class="text-[10px] text-brand-secondary mt-0.5 max-w-xs truncate">{{ $product->description }}</div>
                                    </td>
                                    <!-- Kategori -->
                                    <td class="p-4">
                                        <span class="text-[10px] uppercase font-bold text-[#A0815D] bg-[#EFE7D8] px-2.5 py-1 rounded">
                                            {{ $product->category_name }}
                                        </span>
                                    </td>
                                    <!-- Harga -->
                                    <td class="p-4 font-serif font-bold text-sm text-brand-dark">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </td>
                                    <!-- Status Ketersediaan (Sold Out / In Stock) -->
                                    <td class="p-4">
                                        <form action="/admin/products/{{ $product->id }}/availability" method="POST" class="flex items-center justify-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="is_available" class="bg-brand-bg border border-[#EADBCE] text-[10px] font-bold rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-brand-dark cursor-pointer text-brand-dark">
                                                <option value="1" {{ $product->is_available ? 'selected' : '' }}>Tersedia</option>
                                                <option value="0" {{ ! $product->is_available ? 'selected' : '' }}>Habis</option>
                                            </select>
                                            <button type="submit" class="bg-brand-dark text-white hover:bg-[#2A190C] text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg transition-colors">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                    <!-- Aksi Pengeditan/Hapus -->
                                    <td class="p-4 pr-6 text-right space-x-2">
                                        <form action="/admin/products/{{ $product->id }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition-colors" title="Hapus Produk">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-brand-secondary">
                                        Belum ada produk yang diupload. Klik tombol "Tambah Sepatu Baru" untuk mengisi katalog.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ================= TAB 3: KELOLA PESANAN ================= -->
        <div id="admin-tab-orders" class="admin-tab-content hidden space-y-6">
            
            <!-- Header Kelola Pesanan -->
            <div>
                <h1 class="font-serif text-3xl font-medium tracking-tight">Daftar Pesanan Masuk</h1>
                <p class="text-brand-secondary text-sm mt-1">Lacak status transaksi pembayaran, ubah status kiriman, atau batalkan pesanan di sini.</p>
            </div>

            <!-- Tabel Daftar Pesanan -->
            <div class="bg-white border border-[#EADBCE] rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#FAF6EE] text-brand-secondary border-b border-[#EADBCE]">
                            <tr class="uppercase tracking-wider font-semibold">
                                <th class="p-4 pr-6 pl-6">Invoice</th>
                                <th class="p-4 pr-6">Tanggal</th>
                                <th class="p-4 pr-6">Penerima & Kurir</th>
                                <th class="p-4 pr-6">Metode & Total Bayar</th>
                                <th class="p-4 pr-6">Status Pesanan</th>
                                <th class="p-4 pr-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F8F4EB]">
                            @forelse($orders ?? [] as $order)
                                @php
                                    $customer = $order->user;
                                    $customerName = $customer?->name ?? 'Customer';
                                    $orderStatus = str_replace('_', ' ', $order->status);
                                    $whatsappUrl = $customer?->whatsappUrl(
                                        "Halo {$customerName}, kami dari Steven Shoes ingin menginformasikan pesanan #{$order->invoice_number} dengan status {$orderStatus}."
                                    );
                                @endphp
                                <tr class="hover:bg-brand-bg/10 transition-colors">
                                    <!-- Invoice Number (UUID string id as key) -->
                                    <td class="p-4 pl-6">
                                        <span class="font-mono font-bold text-sm text-brand-dark block">#{{ $order->invoice_number }}</span>
                                        <span class="text-[9px] text-brand-secondary/60 block mt-0.5">ID: {{ $order->id }}</span>
                                    </td>
                                    <!-- Tanggal Order -->
                                    <td class="p-4 text-brand-secondary">
                                        {{ $order->created_at->format('d M Y') }}
                                        <span class="block text-[10px] mt-0.5 text-brand-secondary/60">{{ $order->created_at->format('H:i') }} WIB</span>
                                    </td>
                                    <!-- Penerima & Kurir -->
                                    <td class="p-4">
                                        <div class="font-bold text-brand-dark">{{ $customerName }}</div>
                                        @if($customer?->phone)
                                            <div class="text-[10px] text-brand-secondary mt-0.5">WA: <span class="font-semibold">{{ $customer->phone }}</span></div>
                                        @endif
                                        <div class="text-[10px] text-brand-secondary mt-0.5">Kurir: <span class="font-semibold uppercase">{{ $order->shipping_courier ?? 'JNE' }}</span></div>
                                    </td>
                                    <!-- Metode & Total -->
                                    <td class="p-4">
                                        <div class="font-bold text-brand-dark">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                        <div class="text-[10px] text-brand-secondary uppercase mt-0.5">Metode: <span class="font-semibold">{{ $order->payment_method }}</span></div>
                                    </td>
                                    <!-- Status Pesanan -->
                                    <td class="p-4">
                                        <span class="px-3 py-1 rounded-full text-[9px] uppercase font-bold tracking-wider 
                                            @if($order->status == 'selesai') bg-green-500/10 text-green-700
                                            @elseif($order->status == 'diproses') bg-blue-500/10 text-blue-700
                                            @elseif($order->status == 'batal') bg-red-500/10 text-red-700
                                            @else bg-amber-500/10 text-amber-700 @endif">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <!-- Aksi Pembaruan Status -->
                                    <td class="p-4 pr-6 text-center">
                                        <div class="inline-flex flex-col xl:flex-row items-center justify-center gap-2">
                                            @if($whatsappUrl)
                                                <a href="{{ $whatsappUrl }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    title="Hubungi {{ $customerName }} via WhatsApp"
                                                    class="inline-flex items-center justify-center gap-1.5 bg-green-500/10 text-green-700 hover:bg-green-500/15 border border-green-600/10 text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.125 1.125 0 0 0-1.173.417l-.97 1.293a1.125 1.125 0 0 1-1.21.38 12.035 12.035 0 0 1-7.143-7.143 1.125 1.125 0 0 1 .38-1.21l1.293-.97a1.125 1.125 0 0 0 .417-1.173L6.963 3.102A1.125 1.125 0 0 0 5.872 2.25H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                                    </svg>
                                                    Hubungi WA
                                                </a>
                                            @else
                                                <span title="Customer belum mengisi nomor handphone"
                                                    class="inline-flex items-center justify-center gap-1.5 bg-brand-bg text-brand-secondary/60 border border-[#EADBCE] text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg whitespace-nowrap cursor-not-allowed">
                                                    WA kosong
                                                </span>
                                            @endif

                                            <form action="/admin/orders/{{ $order->id }}/status" method="POST" class="inline-flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                
                                                <!-- Pilihan Perubahan Status -->
                                                <select name="status" class="bg-brand-bg border border-[#EADBCE] text-[10px] font-bold rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-brand-dark cursor-pointer text-brand-dark">
                                                    <option value="menunggu_pembayaran" {{ $order->status == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Bayar</option>
                                                    <option value="diproses" {{ $order->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                                    <option value="dikirim" {{ $order->status == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                                    <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                    <option value="batal" {{ $order->status == 'batal' ? 'selected' : '' }}>Batal</option>
                                                </select>

                                                <button type="submit" class="bg-brand-dark text-white hover:bg-[#2A190C] text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg transition-colors">
                                                    Update
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-brand-secondary">
                                        Belum ada pesanan masuk dari pembeli.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>

    <!-- MODAL POPUP: TAMBAH SEPATU BARU (MODAL ADMIN) -->
    <div id="product-modal" class="fixed inset-0 bg-brand-dark/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white border border-[#EADBCE] rounded-3xl w-full max-w-xl p-6 sm:p-8 space-y-6 shadow-2xl relative">
            
            <!-- Tombol Close Modal -->
            <button onclick="toggleProductModal(false)" class="absolute top-5 right-5 text-brand-secondary hover:text-brand-dark p-1 rounded-full hover:bg-[#FAF6EE] transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Judul Modal -->
            <div>
                <h3 class="font-serif text-2xl font-semibold text-brand-dark">Upload Sepatu Baru</h3>
                <p class="text-xs text-brand-secondary mt-1">Masukkan data lengkap sepatu untuk ditambahkan langsung ke katalog pembeli.</p>
            </div>

            <!-- Form Penambahan Produk Laravel Standard -->
            <form action="/admin/products" method="POST" enctype="multipart/form-data" class="space-y-4" id="product-upload-form">
                @csrf

                <!-- Nama Produk -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-secondary">Nama Sepatu</label>
                    <input type="text" name="name" required placeholder="Steven Premium Oxford Leather" 
                        class="w-full px-4 py-3 bg-brand-bg/40 border border-[#EADBCE] focus:border-brand-dark rounded-xl text-brand-dark placeholder-brand-secondary/40 text-xs focus:outline-none transition-all">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Pilihan Kategori -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-secondary">Kategori</label>
                        <select name="category" required class="w-full px-4 py-3 bg-brand-bg/40 border border-[#EADBCE] focus:border-brand-dark rounded-xl text-brand-dark text-xs focus:outline-none transition-all cursor-pointer">
                            <option value="sepatu-sneakers-wanita">Sepatu Sneakers Wanita</option>
                            <option value="sepatu-sneakers-pria">Sepatu Sneakers Pria</option>
                            <option value="sandal-wanita">Sandal Wanita</option>
                            <option value="sepatu-lari-pria">Sepatu Lari Pria</option>
                            <option value="sandal-pria">Sandal Pria</option>
                            <option value="sepatu-lari-wanita">Sepatu Lari Wanita</option>
                            <option value="sandal-baim-viral">Sandal Baim Viral</option>
                            <option value="sandal-baim-pria">Sandal Baim Pria</option>
                            <option value="sandal-anak2">Sandal Anak2</option>
                            <option value="sandal-crocs-original">Sandal Crocs Original</option>
                        </select>
                    </div>

                    <!-- Harga Produk -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-secondary">Harga (Rupiah)</label>
                        <input type="number" name="price" required placeholder="899000" 
                            class="w-full px-4 py-3 bg-brand-bg/40 border border-[#EADBCE] focus:border-brand-dark rounded-xl text-brand-dark placeholder-brand-secondary/40 text-xs focus:outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Status Ketersediaan (Pengganti Angka Stok) -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-secondary">Status Ketersediaan</label>
                        <select name="is_available" required class="w-full px-4 py-3 bg-brand-bg/40 border border-[#EADBCE] focus:border-brand-dark rounded-xl text-brand-dark text-xs focus:outline-none transition-all cursor-pointer">
                            <option value="1">Tersedia (In Stock)</option>
                            <option value="0">Habis (Sold Out)</option>
                        </select>
                    </div>

                    <!-- File Foto Sepatu -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-secondary">Foto Sepatu</label>
                        <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                        <input type="file" name="image" accept="image/*" required id="product-image-input"
                            class="w-full px-4 py-2.5 bg-brand-bg/40 border border-[#EADBCE] focus:border-brand-dark rounded-xl text-brand-dark file:mr-4 file:rounded-lg file:border-0 file:bg-brand-dark file:px-3 file:py-2 file:text-[10px] file:font-bold file:uppercase file:tracking-wider file:text-white text-xs focus:outline-none transition-all">
                        <p class="text-[10px] text-brand-secondary mt-1">Maksimal 2 MB. Gunakan foto JPG, PNG, atau WebP.</p>
                    </div>
                </div>

                <!-- Deskripsi Sepatu -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-secondary">Deskripsi Produk</label>
                    <textarea name="description" rows="3" required placeholder="Uraikan detail bahan, kelebihan kenyamanan, dan ukuran yang sesuai untuk sepatu ini..." 
                        class="w-full px-4 py-3 bg-brand-bg/40 border border-[#EADBCE] focus:border-brand-dark rounded-xl text-brand-dark placeholder-brand-secondary/40 text-xs focus:outline-none transition-all"></textarea>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" onclick="toggleProductModal(false)" class="bg-brand-bg hover:bg-[#EADBCE] text-brand-dark text-xs font-semibold px-6 py-3 rounded-xl transition-all">
                        Batalkan
                    </button>
                    <button type="submit" class="bg-brand-dark hover:bg-[#2A190C] text-white text-xs font-semibold px-6 py-3 rounded-xl transition-all shadow-md shadow-brand-dark/10">
                        Upload Produk
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- SCRIPT INTERAKTIF PREVIEW & NAVIGASI ADMIN -->
    <script>
        // Manajemen Berpindah Tab Admin
        function switchAdminTab(tabName) {
            // Sembunyikan semua konten tab
            const tabContents = document.querySelectorAll('.admin-tab-content');
            tabContents.forEach(content => content.classList.add('hidden'));

            // Tampilkan tab yang diinginkan
            const targetTab = document.getElementById('admin-tab-' + tabName);
            if (targetTab) {
                targetTab.classList.remove('hidden');
            }

            // Atur gaya aktif/nonaktif pada tombol sidebar
            const sidebarButtons = document.querySelectorAll('.admin-sidebar-btn');
            sidebarButtons.forEach(button => {
                button.classList.remove('bg-brand-accent', 'text-brand-dark', 'shadow-md', 'font-semibold');
                button.classList.add('text-white/70', 'hover:text-white', 'hover:bg-white/5', 'font-medium');
            });

            const activeBtn = document.getElementById('btn-tab-' + tabName);
            if (activeBtn) {
                activeBtn.classList.add('bg-brand-accent', 'text-brand-dark', 'shadow-md', 'font-semibold');
                activeBtn.classList.remove('text-white/70', 'hover:text-white', 'hover:bg-white/5', 'font-medium');
            }
        }

        // Buka/Tutup Modal Pengunggahan Produk
        function toggleProductModal(show) {
            const modal = document.getElementById('product-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        const productUploadForm = document.getElementById('product-upload-form');
        const productImageInput = document.getElementById('product-image-input');
        const maxProductImageSize = 2 * 1024 * 1024;

        if (productUploadForm && productImageInput) {
            productUploadForm.addEventListener('submit', (event) => {
                const file = productImageInput.files[0];

                if (file && file.size > maxProductImageSize) {
                    event.preventDefault();
                    alert('Ukuran foto maksimal 2 MB. Silakan kompres atau pilih gambar yang lebih kecil.');
                }
            });
        }
    </script>

</body>
</html>
