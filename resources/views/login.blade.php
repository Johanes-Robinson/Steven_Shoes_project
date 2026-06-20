<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Steven Club - Steven Shoes</title>
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
                            light: '#FAF6EE'
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
<body class="bg-brand-bg font-sans min-h-screen text-brand-dark overflow-x-hidden antialiased flex items-center justify-center">

    <div class="min-h-screen w-full flex flex-col md:flex-row">
        
        <!-- BAGIAN KIRI: Visual Branding (Hanya muncul di Layar Desktop/Tablet) -->
        <div class="hidden md:flex md:w-1/2 lg:w-3/5 bg-brand-dark relative flex-col justify-between p-12 lg:p-16 text-[#FBF9F6]">
            <!-- Pattern Overlay yang Elegan -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#FAF6EE_1px,transparent_1px)] [background-size:16px_16px]"></div>
            
            <!-- Logo Brand -->
            <div class="z-10">
                <a href="/" class="font-serif text-2xl font-bold tracking-wider hover:opacity-90 transition-opacity">
                    STEVEN SHOES
                </a>
            </div>

            <!-- Pesan Branding Eksklusif -->
            <div class="z-10 max-w-md space-y-4">
                <span class="text-xs uppercase tracking-widest text-brand-accent font-semibold">Welcome Back</span>
                <h1 class="font-serif text-4xl lg:text-5xl leading-tight">Step back into ultimate luxury and comfort.</h1>
                <p class="text-xs text-white/70 leading-relaxed font-light">
                    Masuk kembali ke akun Steven Club Anda untuk melanjutkan perjalanan gaya Anda, melihat daftar keinginan, dan menikmati penawaran eksklusif anggota yang dikuratori khusus untuk Anda.
                </p>
            </div>

            <!-- Footer Branding Singkat -->
            <div class="z-10 text-xs text-white/40 flex items-center gap-4">
                <span>© 2026 Steven Shoes Inc.</span>
                <span>•</span>
                <span>Premium Comfort, Timeless Design</span>
            </div>

            <!-- Background Image Estetik di Kiri (Unsplash Premium Leather Shoes) -->
            <div class="absolute inset-0 w-full h-full object-cover opacity-35 z-0">
                <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=1200&q=80" alt="Aesthetic Shoes" class="w-full h-full object-cover">
            </div>
        </div>

        <!-- BAGIAN KANAN: Form Login -->
        <div class="w-full md:w-1/2 lg:w-2/5 px-6 sm:px-12 lg:px-16 py-12 flex flex-col justify-center bg-[#FAF6EE] relative">
            
            <!-- Back to Home Arrow -->
            <div class="absolute top-8 left-6 sm:left-12 lg:left-16">
                <a href="/" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-secondary hover:text-brand-dark transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            <!-- Form Content -->
            <div class="max-w-md w-full mx-auto space-y-8">
                <div>
                    <h2 class="font-serif text-3xl font-medium tracking-tight">Selamat Datang Kembali</h2>
                    <p class="text-brand-secondary text-sm mt-2">Silakan masuk menggunakan kredensial akun Steven Club Anda.</p>
                </div>

                @if(session('status'))
                    <div class="bg-green-50 border border-green-200 text-green-800 text-xs leading-relaxed rounded-2xl px-4 py-3">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Form Laravel Standard -->
                <form method="POST" action="/login" class="space-y-5">
                    <!-- Token CSRF Laravel -->
                    @csrf

                    <!-- Input Email -->
                    <div class="space-y-1">
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Alamat Email</label>
                        <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="steven@shoes.com" 
                            class="w-full px-4 py-3 bg-white border border-[#E4D5BE] focus:border-brand-dark rounded-xl text-brand-dark placeholder-brand-secondary/40 text-sm focus:outline-none transition-all duration-300">
                        @error('email')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Password -->
                    <div class="space-y-1">
                        <div class="flex justify-between items-center">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Kata Sandi</label>
                            <a href="/password/reset" class="text-xs text-brand-secondary hover:text-brand-dark transition-colors">Lupa sandi?</a>
                        </div>
                        <input type="password" id="password" name="password" required placeholder="Masukkan kata sandi Anda" 
                            class="w-full px-4 py-3 bg-white border border-[#E4D5BE] focus:border-brand-dark rounded-xl text-brand-dark placeholder-brand-secondary/40 text-sm focus:outline-none transition-all duration-300">
                        @error('password')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ingat Saya (Remember Me) -->
                    <div class="flex items-center gap-3 pt-1">
                        <input type="checkbox" id="remember" name="remember" class="accent-brand-dark rounded">
                        <label for="remember" class="text-xs text-brand-secondary select-none cursor-pointer">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <!-- Tombol Login Submit -->
                    <button type="submit" class="w-full bg-brand-dark hover:bg-[#2A190C] text-white font-semibold text-sm py-3.5 px-6 rounded-full transition-all duration-300 transform hover:-translate-y-0.5 mt-2 flex items-center justify-center gap-2 shadow-lg shadow-brand-dark/10">
                        Masuk Sekarang
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

                <!-- Menuju Registrasi -->
                <div class="text-center pt-4">
                    <p class="text-xs text-brand-secondary">
                        Belum bergabung dengan Steven Club? 
                        <a href="/register" class="font-bold text-brand-dark hover:underline underline-offset-4 ml-1 transition-all">
                            Daftar gratis di sini
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer Sederhana di Layar Handphone -->
            <div class="mt-12 text-center text-xs text-brand-secondary/50 md:hidden">
                © 2026 Steven Shoes Inc.
            </div>

        </div>
    </div>

</body>
</html>
