<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Toko Parfum Yasmin Golden Care</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0E0D0C] text-stone-200 antialiased font-sans relative flex items-center justify-center p-4 overflow-x-hidden">
    <!-- Ambient Perfume Glow -->
    <div class="pointer-events-none absolute -top-40 -left-40 h-96 w-96 rounded-full bg-amber-600/15 blur-[120px]"></div>
    <div class="pointer-events-none absolute -bottom-40 -right-40 h-96 w-96 rounded-full bg-amber-500/10 blur-[130px]"></div>

    <main class="w-full max-w-md relative z-10">
        <!-- Logo & Brand Card -->
        <div class="text-center mb-8">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-tr from-amber-400 via-amber-500 to-amber-700 shadow-xl shadow-amber-500/25 ring-2 ring-amber-300/40 mb-4">
                <svg class="h-9 w-9 text-stone-950" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 3h6v3H9z"/>
                    <path d="M10 6v3h4V6"/>
                    <path d="M5 10a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V10z"/>
                    <circle cx="12" cy="15" r="2"/>
                </svg>
            </div>
            <h1 class="font-serif text-2xl font-bold tracking-tight text-white sm:text-3xl">
                Yasmin Golden Care
            </h1>
            <p class="mt-1 text-xs font-semibold tracking-widest text-amber-400 uppercase">
                Inventory Intelligence & Warehouse Platform
            </p>
        </div>

        <!-- Login Card -->
        <div class="rounded-3xl border border-stone-800/80 bg-stone-900/90 p-8 shadow-2xl backdrop-blur-xl">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-white">Selamat Datang Kembali</h2>
                <p class="mt-1 text-xs text-stone-400">Masuk untuk mengelola stok bahan baku dan parfum jadi.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-rose-900/50 bg-rose-950/40 p-3.5 text-xs font-medium text-rose-300">
                    <svg class="h-4 w-4 shrink-0 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-400 mb-1.5" for="email">
                        Alamat Email
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email', 'admin@yasmingoldencare.com') }}" required autofocus
                           class="w-full rounded-xl border border-stone-700 bg-stone-950/60 px-4 py-2.5 text-sm text-white placeholder-stone-500 shadow-inner transition focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                           placeholder="nama@yasmingoldencare.com">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-400" for="password">
                            Kata Sandi
                        </label>
                    </div>
                    <input id="password" name="password" type="password" required value="Password123!"
                           class="w-full rounded-xl border border-stone-700 bg-stone-950/60 px-4 py-2.5 text-sm text-white placeholder-stone-500 shadow-inner transition focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                           placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                </div>

                <div class="flex items-center justify-between pt-1 text-xs">
                    <label class="flex items-center gap-2 text-stone-400 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="rounded border-stone-700 bg-stone-950 text-amber-500 focus:ring-amber-400 focus:ring-offset-stone-900">
                        <span>Ingat sesi saya</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full mt-2 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 px-5 py-3 text-sm font-bold text-stone-950 shadow-lg shadow-amber-500/20 transition hover:from-amber-300 hover:to-amber-500 hover:shadow-amber-500/30 active:scale-[0.99]">
                    Masuk ke Sistem Gudang
                </button>
            </form>

            <!-- Quick Account Switcher for Demo -->
            <div class="mt-6 border-t border-stone-800 pt-5">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-amber-400/90 mb-2.5">
                    Akun Cepat (Quick Fill Demo):
                </p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button" 
                            onclick="document.getElementById('email').value='admin@yasmingoldencare.com'; document.getElementById('password').value='Password123!';"
                            class="rounded-lg border border-stone-800 bg-stone-950/40 p-2.5 text-left transition hover:border-amber-500/40 hover:bg-stone-800/40">
                        <span class="block font-semibold text-amber-300">Admin Utama</span>
                        <span class="block text-[10px] text-stone-400">admin@yasmingoldencare.com</span>
                    </button>
                    <button type="button" 
                            onclick="document.getElementById('email').value='viewer@yasmingoldencare.com'; document.getElementById('password').value='password';"
                            class="rounded-lg border border-stone-800 bg-stone-950/40 p-2.5 text-left transition hover:border-amber-500/40 hover:bg-stone-800/40">
                        <span class="block font-semibold text-stone-300">Viewer Staff</span>
                        <span class="block text-[10px] text-stone-400">viewer@yasmingoldencare.com</span>
                    </button>
                </div>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-stone-500">
            &copy; 2026 Toko Parfum Yasmin Golden Care. Hak cipta dilindungi.
        </p>
    </main>
</body>
</html>
