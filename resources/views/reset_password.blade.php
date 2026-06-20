<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - Steven Shoes</title>
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
                            accent: '#D9B78D'
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-brand-bg font-sans min-h-screen text-brand-dark flex items-center justify-center px-6">
    <main class="w-full max-w-md bg-white border border-[#EADBCE] rounded-3xl p-8 shadow-lg">
        <a href="/" class="font-serif text-2xl font-bold tracking-wider">STEVEN SHOES</a>

        <div class="mt-8 mb-6">
            <h1 class="font-serif text-3xl font-medium">Atur Ulang Kata Sandi</h1>
            <p class="text-sm text-brand-secondary mt-2">Masukkan kata sandi baru untuk akun Steven Club Anda.</p>
        </div>

        <form method="POST" action="/password/reset" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="space-y-1">
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Alamat Email</label>
                <input type="email" id="email" name="email" required value="{{ old('email', $email) }}"
                    class="w-full px-4 py-3 bg-brand-bg/40 border border-[#E4D5BE] focus:border-brand-dark rounded-xl text-sm focus:outline-none">
                @error('email')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Kata Sandi Baru</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 bg-brand-bg/40 border border-[#E4D5BE] focus:border-brand-dark rounded-xl text-sm focus:outline-none">
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-brand-secondary">Konfirmasi Kata Sandi</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-3 bg-brand-bg/40 border border-[#E4D5BE] focus:border-brand-dark rounded-xl text-sm focus:outline-none">
            </div>

            <button type="submit" class="w-full bg-brand-dark hover:bg-[#2A190C] text-white font-semibold text-sm py-3.5 rounded-full transition-all">
                Simpan Kata Sandi Baru
            </button>
        </form>
    </main>
</body>
</html>
