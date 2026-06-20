<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Konfirmasi Dikirim - Steven Shoes</title>
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
    @php
        $type = $type ?? session('verification_type', 'account');
        $email = $email ?? session('email');
        $isPasswordReset = $type === 'reset';
    @endphp

    <!-- Header Navigation -->
    <header class="max-w-4xl mx-auto w-full px-6 py-6 flex items-center justify-between border-b border-[#E4D5BE]">
        <a href="/" class="font-serif text-xl font-bold tracking-wider text-brand-dark hover:opacity-85 transition-opacity">
            STEVEN SHOES
        </a>
        <span class="text-xs font-semibold uppercase tracking-wider text-amber-700 bg-amber-50 px-3 py-1 rounded-full">
            Email Verifikasi
        </span>
    </header>

    <!-- Main Container -->
    <main class="max-w-xl mx-auto w-full px-6 py-12 flex-1 flex flex-col justify-center">
        
        <div class="bg-white border border-[#EADBCE] rounded-3xl p-6 sm:p-10 shadow-lg text-center space-y-8">
            
            <!-- Icon Email Sent -->
            <div class="mx-auto w-16 h-16 bg-[#3E2511]/5 rounded-full flex items-center justify-center text-[#D9B78D]">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                </svg>
            </div>

            <!-- Title & Info Pengiriman -->
            <div class="space-y-3">
                <h1 class="font-serif text-3xl font-medium tracking-tight">Konfirmasi Email Dikirim</h1>
                <p class="text-brand-secondary text-sm max-w-sm mx-auto leading-relaxed">
                    @if($isPasswordReset)
                        Kami telah mengirimkan tautan pengaturan ulang kata sandi ke alamat email Anda:
                    @else
                        Kami telah mengirimkan tautan verifikasi akun ke alamat email Anda:
                    @endif
                </p>
                <div class="inline-block bg-brand-bg px-4 py-2 rounded-xl border border-[#E4D5BE] mt-2">
                    <span class="text-xs font-bold tracking-wider text-brand-dark">
                        {{ $email ?? 'email-anda@domain.com' }}
                    </span>
                </div>
            </div>

            <!-- Detail Instruksi Penerimaan -->
            <div class="bg-[#FAF6EE] border border-[#EADBCE] rounded-2xl p-6 text-left space-y-4">
                <p class="text-xs text-brand-secondary uppercase tracking-widest font-semibold text-center border-b border-[#EADBCE] pb-2">Langkah Selanjutnya</p>
                
                <ul class="list-disc text-xs text-brand-secondary space-y-2.5 pl-4 leading-relaxed">
                    <li>Buka kotak masuk atau folder <span class="font-semibold text-brand-dark">Spam/Junk</span> pada email Anda.</li>
                    <li>Cari email masuk dari <span class="font-semibold text-brand-dark">Steven Shoes</span>.</li>
                    @if($isPasswordReset)
                        <li>Klik tautan atau tombol <span class="font-semibold text-brand-dark">"Atur Ulang Kata Sandi"</span> di dalam email tersebut.</li>
                        <li>Masa berlaku tautan pemulihan ini terbatas selama <span class="font-semibold text-brand-dark">60 menit</span> dari sekarang.</li>
                    @else
                        <li>Klik tautan atau tombol <span class="font-semibold text-brand-dark">"Verifikasi Email"</span> di dalam email tersebut.</li>
                        <li>Setelah email terverifikasi, silakan masuk melalui halaman login untuk mulai berbelanja.</li>
                    @endif
                </ul>
            </div>

            <!-- Tombol Navigasi Bawah -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-[#FAF6EE]">
                @if(! $isPasswordReset && $email)
                    <form action="/email/verification-notification" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <button type="submit" class="w-full border border-brand-dark hover:bg-brand-dark/5 text-brand-dark text-xs font-semibold py-3.5 rounded-xl transition-all text-center">
                            Kirim Ulang Verifikasi
                        </button>
                    </form>
                @endif
                <a href="/login" class="flex-1 bg-brand-dark hover:bg-brand-dark/95 text-white text-xs font-semibold py-3.5 rounded-xl transition-all text-center">
                    Masuk (Login) Kembali
                </a>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="max-w-4xl mx-auto w-full px-6 py-6 text-center text-[10px] text-brand-secondary">
        &copy; 2026 STEVEN SHOES. All rights reserved. Hubungi admin kami jika Anda tidak menerima email konfirmasi.
    </footer>

</body>
</html>
