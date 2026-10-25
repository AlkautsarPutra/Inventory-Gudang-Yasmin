<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Informasi Manajemen Gudang & Peramalan Toko Parfum Yasmin Golden Care">
    <title>{{ $title ?? 'Gudang Yasmin Golden Care' }} · Parfumerie & Inventory Intelligence</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#FBF9F5] text-stone-800 antialiased font-sans">
    <div class="min-h-screen lg:grid lg:grid-cols-[17.5rem_1fr]" x-data="{ mobileMenuOpen: false }">
        <!-- Sidebar Navigation -->
        <aside class="flex flex-col justify-between border-r border-[#262320] bg-[#141211] text-stone-300">
            <div class="p-6">
                <!-- Brand Header -->
                <a href="{{ route('dashboard') }}" class="group mb-8 flex items-center gap-3.5 transition">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-amber-300 via-amber-500 to-amber-700 text-stone-950 shadow-md shadow-amber-500/20 ring-1 ring-amber-300/40">
                        <svg class="h-6 w-6 transition-transform group-hover:scale-105" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 3h6v3H9z"/>
                            <path d="M10 6v3h4V6"/>
                            <path d="M5 10a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V10z"/>
                            <circle cx="12" cy="15" r="2"/>
                        </svg>
                    </div>
                    <div>
                        <span class="block font-serif text-[1.05rem] font-bold tracking-tight text-white group-hover:text-amber-300 transition-colors">
                            Yasmin Golden Care
                        </span>
                        <span class="block text-[10px] font-semibold tracking-widest text-amber-400/90 uppercase">
                            Parfum Warehouse
                        </span>
                    </div>
                </a>

                <!-- Navigation Section -->
                <div class="mb-3 px-3 text-[11px] font-semibold tracking-wider text-stone-500 uppercase">
                    Menu Operasional
                </div>
                <nav class="space-y-1.5 text-sm font-medium">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       class="{{ request()->routeIs('dashboard') ? 'bg-amber-500/15 text-amber-300 border-l-2 border-amber-400 shadow-sm' : 'text-stone-400 hover:bg-stone-800/50 hover:text-stone-100' }} flex items-center gap-3 rounded-lg px-3.5 py-2.5 transition">
                        <svg class="h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-amber-400' : 'text-stone-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Produk Parfum & Bahan -->
                    @can('manage-master-data')
                        <a href="{{ route('products.index') }}" 
                           class="{{ request()->routeIs('products.*') ? 'bg-amber-500/15 text-amber-300 border-l-2 border-amber-400 shadow-sm' : 'text-stone-400 hover:bg-stone-800/50 hover:text-stone-100' }} flex items-center gap-3 rounded-lg px-3.5 py-2.5 transition">
                            <svg class="h-5 w-5 {{ request()->routeIs('products.*') ? 'text-amber-400' : 'text-stone-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                            </svg>
                            <span>Katalog Produk & Bahan</span>
                        </a>

                        <a href="{{ route('suppliers.index') }}" 
                           class="{{ request()->routeIs('suppliers.*') ? 'bg-amber-500/15 text-amber-300 border-l-2 border-amber-400 shadow-sm' : 'text-stone-400 hover:bg-stone-800/50 hover:text-stone-100' }} flex items-center gap-3 rounded-lg px-3.5 py-2.5 transition">
                            <svg class="h-5 w-5 {{ request()->routeIs('suppliers.*') ? 'text-amber-400' : 'text-stone-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                            </svg>
                            <span>Mitra Supplier</span>
                        </a>
                    @endcan

                    <!-- Transaksi Mutasi Stok -->
                    <a href="{{ route('inventory-transactions.index') }}" 
                       class="{{ request()->routeIs('inventory-transactions.*') ? 'bg-amber-500/15 text-amber-300 border-l-2 border-amber-400 shadow-sm' : 'text-stone-400 hover:bg-stone-800/50 hover:text-stone-100' }} flex items-center gap-3 rounded-lg px-3.5 py-2.5 transition">
                        <svg class="h-5 w-5 {{ request()->routeIs('inventory-transactions.*') ? 'text-amber-400' : 'text-stone-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                        <span>Riwayat Mutasi Stok</span>
                    </a>

                    <!-- Forecasting SES -->
                    @can('access-forecasting')
                        <a href="{{ route('forecasts.index') }}" 
                           class="{{ request()->routeIs('forecasts.*') ? 'bg-amber-500/15 text-amber-300 border-l-2 border-amber-400 shadow-sm' : 'text-stone-400 hover:bg-stone-800/50 hover:text-stone-100' }} flex items-center gap-3 rounded-lg px-3.5 py-2.5 transition">
                            <svg class="h-5 w-5 {{ request()->routeIs('forecasts.*') ? 'text-amber-400' : 'text-stone-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                            </svg>
                            <span>Peramalan Permintaan</span>
                        </a>
                    @endcan

                    <!-- Peringatan Stok -->
                    @php
                        $unacknowledgedAlertsCount = \App\Models\StockAlert::where('is_acknowledged', false)->count();
                    @endphp
                    <a href="{{ route('alerts.index') }}" 
                       class="{{ request()->routeIs('alerts.*') ? 'bg-amber-500/15 text-amber-300 border-l-2 border-amber-400 shadow-sm' : 'text-stone-400 hover:bg-stone-800/50 hover:text-stone-100' }} flex items-center justify-between rounded-lg px-3.5 py-2.5 transition">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 {{ request()->routeIs('alerts.*') ? 'text-amber-400' : 'text-stone-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            <span>Peringatan Stok</span>
                        </div>
                        @if ($unacknowledgedAlertsCount > 0)
                            <span class="inline-flex items-center rounded-full bg-rose-500/20 px-2.5 py-0.5 text-xs font-bold text-rose-400 ring-1 ring-rose-500/30 animate-pulse">
                                {{ $unacknowledgedAlertsCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Kelola Pengguna -->
                    @can('manage-users')
                        <div class="pt-4 mb-2 px-3 text-[11px] font-semibold tracking-wider text-stone-500 uppercase">
                            Administrasi
                        </div>
                        <a href="{{ route('users.index') }}" 
                           class="{{ request()->routeIs('users.*') ? 'bg-amber-500/15 text-amber-300 border-l-2 border-amber-400 shadow-sm' : 'text-stone-400 hover:bg-stone-800/50 hover:text-stone-100' }} flex items-center gap-3 rounded-lg px-3.5 py-2.5 transition">
                            <svg class="h-5 w-5 {{ request()->routeIs('users.*') ? 'text-amber-400' : 'text-stone-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            <span>Kelola Pengguna</span>
                        </a>
                    @endcan
                </nav>
            </div>

            <!-- Profile Footer -->
            <div class="border-t border-[#262320] bg-[#0E0D0C] p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-tr from-amber-600 to-amber-400 text-xs font-bold text-stone-950">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                            <span class="inline-block text-[11px] text-amber-400 font-medium">
                                {{ auth()->user()->role->label() }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar / Sign Out" class="rounded-lg p-1.5 text-stone-400 hover:bg-stone-800 hover:text-white transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col min-w-0">
            <!-- Header Topbar -->
            <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b border-stone-200/80 bg-white/80 px-6 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-xs font-medium text-stone-500">
                        <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span class="text-stone-700 font-semibold">Toko Parfum Yasmin Golden Care</span>
                        <span>&middot;</span>
                        <span class="hidden sm:inline">Gudang Bahan & Produk Jadi</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @can('record-inventory')
                        <a href="{{ route('inventory-transactions.create') }}" 
                           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-stone-900 to-stone-800 px-4 py-2 text-xs font-semibold text-amber-300 shadow-sm hover:from-stone-800 hover:to-stone-700 hover:text-amber-200 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>Catat Mutasi Stok</span>
                        </a>
                    @endcan
                </div>
            </header>

            <!-- Page Body -->
            <main class="min-w-0 flex-1 p-6 sm:p-8">
                <!-- Notifications Flash -->
                @if (session('success'))
                    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-sm font-medium text-emerald-900 shadow-sm">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <div class="flex-1">{{ session('success') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-900 shadow-sm">
                        <div class="flex items-center gap-2 font-semibold text-rose-800">
                            <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                            <span>Mohon perbaiki kesalahan berikut:</span>
                        </div>
                        <ul class="mt-2 ml-7 list-disc space-y-1 text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
