<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasi Steven Shoes</title>
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
                            card: '#FFFDF9',
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
        <nav class="hidden sm:flex items-center gap-6 text-xs font-semibold uppercase tracking-widest">
            <a href="/" class="text-brand-secondary hover:text-brand-dark transition-colors">Home</a>
            <a href="{{ route('store.products') }}" class="text-brand-secondary hover:text-brand-dark transition-colors">Products</a>
            <a href="{{ route('store.location') }}" class="text-brand-dark">Location</a>
            <a href="{{ route('store.shop') }}" class="border border-brand-dark px-5 py-2.5 rounded-full hover:bg-brand-dark hover:text-white transition-all">Shop</a>
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-16 py-12">
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            <div class="lg:col-span-5 space-y-8">
                <div>
                    <span class="inline-flex items-center bg-[#EFE7D8] border border-[#E4D5BE] px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-widest text-brand-secondary">
                        Store Location
                    </span>
                    <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight mt-5">Steven Shoes Mangga Dua</h1>
                    <p class="text-brand-secondary text-sm sm:text-base mt-4 leading-relaxed">
                        Temukan koleksi Steven Shoes langsung di ITC Mangga Dua Jakarta.
                    </p>
                </div>

                <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest font-bold text-brand-secondary">Alamat</p>
                        <h2 class="font-serif text-2xl font-semibold mt-2">{{ $location['name'] }}</h2>
                        <p class="text-sm text-brand-secondary mt-2 leading-relaxed">{{ $location['address'] }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-[#FAF6EE]">
                        <div class="bg-brand-bg/60 border border-[#EADBCE] rounded-2xl p-4">
                            <p class="text-[10px] uppercase tracking-widest font-bold text-brand-secondary">Gedung</p>
                            <p class="text-sm font-semibold mt-1">ITC Mangga Dua</p>
                        </div>
                        <div class="bg-brand-bg/60 border border-[#EADBCE] rounded-2xl p-4">
                            <p class="text-[10px] uppercase tracking-widest font-bold text-brand-secondary">Area</p>
                            <p class="text-sm font-semibold mt-1">Lantai Dasar</p>
                        </div>
                    </div>

                    <a href="{{ $location['maps_url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex w-full sm:w-auto items-center justify-center gap-2 bg-brand-dark hover:bg-[#2A190C] text-white text-xs font-semibold uppercase tracking-wider px-6 py-3.5 rounded-full transition-all">
                        Buka Google Maps
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-white border border-[#EADBCE] rounded-3xl overflow-hidden shadow-sm">
                    <iframe
                        src="{{ $location['embed_url'] }}"
                        title="Peta {{ $location['name'] }}"
                        class="w-full h-[420px] sm:h-[520px]"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
