<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Steven Shoes</title>
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
                        },
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-brand-bg font-sans min-h-screen text-brand-dark antialiased">
    <header class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-16 py-6 flex items-center justify-between border-b border-[#E4D5BE]">
        <a href="/" class="font-serif text-2xl font-bold tracking-wider hover:opacity-80 transition-opacity">
            STEVEN SHOES
        </a>
        <nav class="flex items-center gap-6 text-xs font-semibold uppercase tracking-widest">
            <a href="/" class="text-brand-secondary hover:text-brand-dark transition-colors">Home</a>
            <a href="{{ route('store.products') }}" class="text-brand-dark">Products</a>
            <a href="{{ route('store.location') }}" class="text-brand-secondary hover:text-brand-dark transition-colors">Location</a>
            <a href="{{ route('store.shop') }}" class="border border-brand-dark px-5 py-2.5 rounded-full hover:bg-brand-dark hover:text-white transition-all">Shop</a>
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-16 py-12">
        <div class="mb-8">
            <h1 class="font-serif text-4xl font-medium tracking-tight">Daftar Produk</h1>
            <p class="text-brand-secondary text-sm mt-2">Koleksi sepatu dan sandal Steven Shoes yang sedang tersedia.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($products ?? [] as $product)
                <article class="bg-white border border-[#EADBCE] rounded-3xl p-4 hover:shadow-lg transition-all">
                    <div class="relative rounded-2xl overflow-hidden aspect-square bg-[#F3ECDF] mb-4">
                        <img src="{{ $product->image_url }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='https://placehold.co/400x400/F3ECDF/3E2511?text=Steven+Shoes';">
                    </div>
                    <span class="text-[10px] uppercase font-bold text-brand-secondary tracking-wider block">
                        {{ $product->category_name }}
                    </span>
                    <h2 class="font-serif text-lg font-semibold text-brand-dark mt-1">{{ $product->name }}</h2>
                    <p class="text-xs text-brand-secondary mt-1 line-clamp-2">{{ $product->description }}</p>
                    <p class="font-serif text-lg font-bold text-brand-dark mt-4">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                </article>
            @empty
                <div class="col-span-full py-16 text-center bg-white border border-[#EADBCE] rounded-3xl p-8">
                    <h2 class="font-serif text-xl font-semibold">Katalog Masih Kosong</h2>
                    <p class="text-sm text-brand-secondary mt-1">Admin sedang menata produk terbaik untuk Anda.</p>
                </div>
            @endforelse
        </div>
    </main>
</body>
</html>
