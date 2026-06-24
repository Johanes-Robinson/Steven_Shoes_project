<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Steven Shoes - Step into comfort, walk in style</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#FAF6EE',       /* Warna background krem lembut sesuai gambar */
                            dark: '#3E2511',     /* Warna teks & button utama */
                            secondary: '#6E5D4F' /* Warna sub-teks */
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
    <!-- Google Fonts untuk font Serif & Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
</head>
<body class="bg-brand-bg font-sans min-h-screen text-brand-dark overflow-x-hidden antialiased">

    <!-- Navigation Header -->
    <header class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-16 py-6 flex items-center justify-between z-50 relative">
        <!-- Kiri: Logo / Brand -->
        <a href="/" class="font-serif text-2xl font-bold tracking-wider text-brand-dark hover:opacity-80 transition-opacity">
            STEVEN SHOES
        </a>

        <!-- Tengah: Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-10 font-medium text-sm tracking-widest uppercase">
            <a href="/" class="text-brand-dark hover:opacity-80 transition-opacity relative after:absolute after:bottom-[-6px] after:left-0 after:w-full after:h-[1.5px] after:bg-brand-dark">
                Home
            </a>
            <a href="{{ route('store.products') }}" class="text-brand-secondary hover:text-brand-dark transition-colors duration-300">
                Products
            </a>
            <a href="{{ route('store.location') }}" class="text-brand-secondary hover:text-brand-dark transition-colors duration-300">
                Location
            </a>
        </nav>

        <!-- Kanan: Desktop Login Button -->
        <div class="hidden md:block">
            <a href="/login" class="inline-flex items-center gap-2 text-xs font-semibold tracking-widest uppercase border border-brand-dark px-6 py-2.5 rounded-full hover:bg-brand-dark hover:text-white transition-all duration-300">
                Login
            </a>
        </div>

        <!-- Mobile Menu Hamburger Button -->
        <button id="menu-toggle" class="md:hidden text-brand-dark focus:outline-none p-2" aria-label="Toggle Menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>
    </header>

    <!-- Mobile Drawer Menu (Hidden by default) -->
    <div id="mobile-menu" class="hidden fixed inset-0 bg-brand-bg z-50 flex flex-col justify-center items-center gap-8 text-2xl font-serif">
        <!-- Close Button -->
        <button id="menu-close" class="absolute top-6 right-6 text-brand-dark p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <a href="/" class="text-brand-dark font-semibold">Home</a>
        <a href="{{ route('store.products') }}" class="text-brand-secondary hover:text-brand-dark transition-colors">Products</a>
        <a href="{{ route('store.location') }}" class="text-brand-secondary hover:text-brand-dark transition-colors">Location</a>
        
        <!-- Login Button di Mobile -->
        <a href="/login" class="mt-4 inline-flex items-center gap-2 text-sm font-sans font-semibold tracking-widest uppercase border border-brand-dark px-8 py-3 rounded-full bg-brand-dark text-white hover:bg-transparent hover:text-brand-dark transition-all duration-300">
            Login
        </a>
    </div>

    <!-- Main Hero Section -->
    <main class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-16 pt-6 pb-20 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative">
        
        <!-- Efek Radial Glow di Background (Soft Aura seperti di gambar image_ae9f84.jpg) -->
        <div class="absolute top-1/2 left-1/3 -translate-y-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-[#F1E6D2] blur-[120px] rounded-full -z-10 opacity-70"></div>

        <!-- Kiri: Konten Teks -->
        <div class="lg:col-span-6 space-y-8 z-10">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 bg-[#EFE7D8] border border-[#E4D5BE] px-3 py-1 rounded-full text-xs font-medium tracking-wide">
                <span class="w-1.5 h-1.5 rounded-full bg-[#A0815D]"></span>
                New Collection · 2026
            </div>

            <!-- Heading Utama -->
            <h1 class="font-serif text-5xl sm:text-6xl lg:text-[70px] leading-[1.1] font-medium tracking-tight">
                Step into comfort, walk in style.
            </h1>

            <!-- Deskripsi -->
            <p class="text-brand-secondary text-base sm:text-lg max-w-xl leading-relaxed">
                Sepatu & sandal brand kekinian untuk setiap langkahmu. Dibuat dengan bahan premium dan kenyamanan sehari-hari.
            </p>

            <!-- Statistik -->
            <div class="pt-8 border-t border-[#EADBCE] grid grid-cols-3 gap-6 max-w-md">
                <div>
                    <h3 class="font-serif text-3xl font-medium">7+</h3>
                    <p class="text-xs text-brand-secondary mt-1">Years trusted</p>
                </div>
                <div>
                    <h3 class="font-serif text-3xl font-medium">50k</h3>
                    <p class="text-xs text-brand-secondary mt-1">Happy feet</p>
                </div>
                <div>
                    <h3 class="font-serif text-3xl font-medium flex items-center gap-1">
                        4.9<span class="text-lg text-brand-dark">★</span>
                    </h3>
                    <p class="text-xs text-brand-secondary mt-1">Rating</p>
                </div>
            </div>
        </div>

        <!-- Kanan: Slider Gambar Dinamis (Max 10) -->
        <div class="lg:col-span-6 flex justify-center lg:justify-end">
            @php
                $slides = array_slice($images ?? [
                    'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=1000&q=80',
                ], 0, 10);
            @endphp

            <div class="relative w-full max-w-[540px] aspect-[4/3] sm:aspect-[1.35/1] rounded-[40px] overflow-hidden bg-[#D9B78D] shadow-xl shadow-amber-900/5 group">
                
                <!-- Container untuk slides -->
                <div class="w-full h-full relative">
                    @foreach($slides as $index => $slideUrl)
                        <div class="hero-slide absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}">
                            <img src="{{ $slideUrl }}" alt="Steven Shoes Collection {{ $index + 1 }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>

                <!-- Indikator Dots (Hanya muncul jika gambar lebih dari 1) -->
                @if(count($slides) > 1)
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20 bg-black/15 backdrop-blur-sm px-4 py-2 rounded-full">
                        @foreach($slides as $index => $slideUrl)
                            <button class="hero-dot w-2 h-2 rounded-full bg-white/40 transition-all duration-300 {{ $index === 0 ? 'bg-white w-6' : '' }}" aria-label="Go to slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </main>

    <!-- Script Slider Otomatis & Mobile Menu Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mobile Menu Logic
            const menuToggle = document.getElementById('menu-toggle');
            const menuClose = document.getElementById('menu-close');
            const mobileMenu = document.getElementById('mobile-menu');

            if (menuToggle && menuClose && mobileMenu) {
                menuToggle.addEventListener('click', () => {
                    mobileMenu.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden'); // Batasi scroll di background
                });

                menuClose.addEventListener('click', () => {
                    mobileMenu.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            }

            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.hero-dot');
            let currentIndex = 0;
            const totalSlides = slides.length;
            const intervalTime = 4000; // Berpindah setiap 4 detik (4000 milidetik)
            let slideInterval;

            if (totalSlides <= 1) return;

            function goToSlide(index) {
                // Sembunyikan slide aktif saat ini
                slides[currentIndex].classList.replace('opacity-100', 'opacity-0');
                slides[currentIndex].classList.replace('z-10', 'z-0');
                slides[currentIndex].classList.add('pointer-events-none');
                
                if (dots.length > 0) {
                    dots[currentIndex].classList.replace('bg-white', 'bg-white/40');
                    dots[currentIndex].classList.replace('w-6', 'w-2');
                }

                // Perbarui index aktif
                currentIndex = index;

                // Tampilkan slide baru
                slides[currentIndex].classList.replace('opacity-0', 'opacity-100');
                slides[currentIndex].classList.replace('z-0', 'z-10');
                slides[currentIndex].classList.remove('pointer-events-none');
                
                if (dots.length > 0) {
                    dots[currentIndex].classList.replace('bg-white/40', 'bg-white');
                    dots[currentIndex].classList.replace('w-2', 'w-6');
                }
            }

            function nextSlide() {
                let next = (currentIndex + 1) % totalSlides;
                goToSlide(next);
            }

            // Jalankan interval autoplay
            function startAutoplay() {
                slideInterval = setInterval(nextSlide, intervalTime);
            }

            function resetAutoplay() {
                clearInterval(slideInterval);
                startAutoplay();
            }

            // Inisialisasi autoplay
            startAutoplay();

            // Interaksi klik pada dot indikator
            dots.forEach((dot, idx) => {
                dot.addEventListener('click', () => {
                    goToSlide(idx);
                    resetAutoplay();
                });
            });
        });
    </script>
</body>
</html>
