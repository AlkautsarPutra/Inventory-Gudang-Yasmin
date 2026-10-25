@extends('layouts.app', ['title' => 'Dashboard Operasional'])

@section('content')
    <!-- Dashboard Header -->
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-900 border border-amber-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Live Warehouse Status
                </span>
                <span class="text-xs text-stone-500">Toko Parfum Yasmin Golden Care</span>
            </div>
            <h1 class="mt-1 font-serif text-3xl font-bold tracking-tight text-stone-900">
                Ringkasan Inventori & Produksi
            </h1>
        </div>

        <div class="flex items-center gap-3">
            @can('record-inventory')
                <a href="{{ route('inventory-transactions.create') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-sm font-bold text-stone-950 shadow-md shadow-amber-500/20 hover:from-amber-400 hover:to-amber-500 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Catat Transaksi Stok</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Active Critical Alerts Banner (If Any) -->
    @if ($activeAlerts->isNotEmpty())
        <div class="mb-8 rounded-3xl border border-rose-200 bg-rose-50/80 p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-rose-200/80 pb-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-600 text-white shadow-md shadow-rose-600/20">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-rose-950">Peringatan Stok Mendesak (Stock Alerts)</h2>
                        <p class="text-xs text-rose-700">Ditemukan {{ $activeAlerts->count() }} item bahan/parfum yang perlu dipesan atau diisi ulang segera.</p>
                    </div>
                </div>
                <a href="{{ route('alerts.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-rose-800 hover:text-rose-950 hover:underline">
                    <span>Lihat Semua Peringatan</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($activeAlerts as $alert)
                    <div class="flex flex-col justify-between rounded-2xl border border-rose-200/90 bg-white p-4 shadow-sm transition hover:shadow">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                @if ($alert->alert_type === 'below_rop')
                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                        {{ $alert->label() }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-800 ring-1 ring-inset ring-rose-600/20">
                                        {{ $alert->label() }}
                                    </span>
                                @endif

                                @can('record-inventory')
                                    <form action="{{ route('alerts.acknowledge', $alert) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-700 hover:bg-stone-200 transition">
                                            Selesai &check;
                                        </button>
                                    </form>
                                @endcan
                            </div>

                            <p class="mt-3 font-semibold text-stone-900 text-sm line-clamp-1">
                                {{ $alert->product?->name ?? 'Produk Dihapus' }}
                            </p>
                            <p class="text-xs text-stone-500 font-mono mt-0.5">{{ $alert->product?->sku }}</p>
                        </div>

                        <div class="mt-3 border-t border-stone-100 pt-2 flex items-center justify-between text-xs">
                            <span class="text-stone-500">Stok Saat Ini:</span>
                            <span class="font-bold text-rose-600">
                                {{ number_format($alert->current_stock, 0, ',', '.') }} {{ $alert->product?->unit }}
                                <span class="font-normal text-stone-400">/ Min {{ number_format($alert->threshold, 0, ',', '.') }}</span>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4 Main Metric Cards -->
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- 1. Gudang Bahan Mentah (Raw Material) -->
        <div class="rounded-3xl border border-amber-200/80 bg-gradient-to-br from-amber-50/80 via-white to-amber-50/30 p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold tracking-wider uppercase text-amber-800">Bahan Baku & Botol</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500 text-stone-950 shadow-sm shadow-amber-500/20">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.942A4.5 4.5 0 0 1 15.908 17H8.092a4.5 4.5 0 0 1-2.322-.758L4.2 15.3m15.6 0v2.7A2.25 2.25 0 0 1 17.55 20.25H6.45A2.25 2.25 0 0 1 4.2 18V15.3" />
                    </svg>
                </div>
            </div>
            <p class="mt-4 font-serif text-3xl font-bold text-stone-900">{{ $rawMaterialCount }}</p>
            <p class="text-xs text-stone-500 mt-0.5">Bibit, Pelarut & Kemasan</p>

            <div class="mt-4 flex items-center justify-between border-t border-amber-200/60 pt-3 text-xs">
                <span class="text-stone-600">Aman: <strong class="text-stone-900">{{ $rawMaterialCount - $rawMaterialLowStockCount }}</strong></span>
                @if ($rawMaterialLowStockCount > 0)
                    <span class="rounded-full bg-rose-100 px-2 py-0.5 font-bold text-rose-800">
                        Perlu Reorder: {{ $rawMaterialLowStockCount }}
                    </span>
                @else
                    <span class="text-emerald-700 font-semibold">&check; Stok Optimal</span>
                @endif
            </div>
        </div>

        <!-- 2. Gudang Produk Jadi (Finished Goods) -->
        <div class="rounded-3xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/30 p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold tracking-wider uppercase text-emerald-800">Parfum Jadi (EDP)</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-sm shadow-emerald-600/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 3h6v3H9z"/>
                        <path d="M10 6v3h4V6"/>
                        <path d="M5 10a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V10z"/>
                        <circle cx="12" cy="15" r="2"/>
                    </svg>
                </div>
            </div>
            <p class="mt-4 font-serif text-3xl font-bold text-stone-900">{{ $finishedGoodsCount }}</p>
            <p class="text-xs text-stone-500 mt-0.5">Eau de Parfum & Roll-On</p>

            <div class="mt-4 flex items-center justify-between border-t border-emerald-200/60 pt-3 text-xs">
                <span class="text-stone-600">Aman: <strong class="text-stone-900">{{ $finishedGoodsCount - $finishedGoodsLowStockCount }}</strong></span>
                @if ($finishedGoodsLowStockCount > 0)
                    <span class="rounded-full bg-rose-100 px-2 py-0.5 font-bold text-rose-800">
                        Perlu Produksi: {{ $finishedGoodsLowStockCount }}
                    </span>
                @else
                    <span class="text-emerald-700 font-semibold">&check; Siap Display</span>
                @endif
            </div>
        </div>

        <!-- 3. Mitra Supplier Resmi -->
        <div class="rounded-3xl border border-stone-200/80 bg-white p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider uppercase text-stone-500">Mitra Supplier</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100 text-stone-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <p class="mt-4 font-serif text-3xl font-bold text-stone-900">{{ $activeSupplierCount }}</p>
                <p class="text-xs text-stone-500 mt-0.5">Fragrance House & Kemasan</p>
            </div>
            <div class="mt-4 border-t border-stone-100 pt-3 text-xs text-stone-600 flex items-center justify-between">
                <span>Status Supplier:</span>
                <span class="font-bold text-emerald-700">100% Aktif</span>
            </div>
        </div>

        <!-- 4. Peringatan Stok Terbuka -->
        <div class="rounded-3xl border border-stone-200/80 bg-white p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider uppercase text-stone-500">Peringatan Aktif</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $activeAlerts->count() > 0 ? 'bg-rose-100 text-rose-600' : 'bg-stone-100 text-stone-500' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                </div>
                <p class="mt-4 font-serif text-3xl font-bold {{ $activeAlerts->count() > 0 ? 'text-rose-600' : 'text-stone-900' }}">
                    {{ $activeAlerts->count() }}
                </p>
                <p class="text-xs text-stone-500 mt-0.5">Perlu Penanganan</p>
            </div>
            <div class="mt-4 border-t border-stone-100 pt-3 text-xs text-stone-600 flex items-center justify-between">
                <span>Tindakan:</span>
                <a href="{{ route('alerts.index') }}" class="font-bold text-amber-700 hover:underline">
                    Periksa &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid: Low Stock Table + Recent Transactions -->
    <div class="mt-8 grid gap-8 xl:grid-cols-2">
        <!-- Low Stock Products -->
        <section class="rounded-3xl border border-stone-200/80 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-stone-100">
                <div>
                    <h2 class="font-serif text-lg font-bold text-stone-900">Perlu Pemesanan / Produksi Ulang</h2>
                    <p class="text-xs text-stone-500">Produk yang berada di bawah Titik Pesan Kembali (ROP)</p>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                    Katalog Lengkap &rarr;
                </a>
            </div>

            <div class="mt-4 divide-y divide-stone-100">
                @forelse ($lowStockProducts->take(6) as $product)
                    <div class="py-3.5 flex items-center justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="truncate font-semibold text-stone-900 text-sm">
                                    {{ $product->product_name }}
                                </span>
                                <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold {{ $product->isRawMaterial() ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-900' }}">
                                    {{ $product->isRawMaterial() ? 'Bahan Mentah' : 'Produk Jadi' }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-500 font-mono mt-0.5">
                                {{ $product->product_code }} &middot; {{ $product->brand }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="font-bold text-rose-600 text-sm">
                                {{ number_format($product->inventoryStock?->quantity ?? 0, 0, ',', '.') }} {{ $product->unit }}
                            </div>
                            <div class="text-[11px] text-stone-400">
                                ROP: {{ $product->reorder_point }} {{ $product->unit }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-stone-900">Semua persediaan dalam kondisi aman</p>
                        <p class="text-xs text-stone-500">Tidak ada bahan baku atau parfum yang menyentuh batas minimum stok.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Recent Ledger Transactions -->
        <section class="rounded-3xl border border-stone-200/80 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-stone-100">
                <div>
                    <h2 class="font-serif text-lg font-bold text-stone-900">Aktivitas Mutasi Terkini</h2>
                    <p class="text-xs text-stone-500">Catatan penerimaan dan pengeluaran gudang terakhir</p>
                </div>
                <a href="{{ route('inventory-transactions.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                    Buku Ledger &rarr;
                </a>
            </div>

            <div class="mt-4 divide-y divide-stone-100">
                @forelse ($recentTransactions as $transaction)
                    <a href="{{ route('inventory-transactions.show', $transaction) }}" 
                       class="group -mx-2 flex items-center justify-between rounded-xl p-2.5 transition hover:bg-stone-50">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-stone-900 group-hover:text-amber-700 transition">
                                {{ $transaction->product->product_name }}
                            </p>
                            <p class="text-xs text-stone-500 mt-0.5">
                                <span class="font-mono">{{ $transaction->transaction_number }}</span> &middot; 
                                {{ $transaction->transaction_date->format('d M Y') }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center gap-1 font-bold text-sm {{ $transaction->movement_direction->isInbound() ? 'text-emerald-700' : 'text-stone-800' }}">
                                {{ $transaction->movement_direction->isInbound() ? '+' : '-' }}{{ number_format($transaction->quantity, 0, ',', '.') }} {{ $transaction->unit }}
                            </span>
                            <span class="block text-[10px] text-stone-400 uppercase tracking-wide">
                                {{ $transaction->transaction_type->value }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="py-12 text-center text-xs text-stone-500">
                        Belum ada riwayat transaksi mutasi yang tercatat.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Upcoming Forecasts Preview (SES) -->
    @if ($upcomingForecasts->isNotEmpty())
        <div class="mt-8 rounded-3xl border border-stone-200/80 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-stone-100">
                <div>
                    <h2 class="font-serif text-lg font-bold text-stone-900">Peramalan Permintaan Bulan Depan (SES)</h2>
                    <p class="text-xs text-stone-500">Proyeksi kebutuhan stok menggunakan metode Single Exponential Smoothing</p>
                </div>
                @can('access-forecasting')
                    <a href="{{ route('forecasts.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                        Kelola Peramalan &rarr;
                    </a>
                @endcan
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-left text-sm">
                    <thead>
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-stone-500">
                            <th class="py-3 px-4">Nama Produk / Bahan</th>
                            <th class="py-3 px-4">Target Periode</th>
                            <th class="py-3 px-4 text-right">Ramalan Kebutuhan</th>
                            <th class="py-3 px-4 text-right">Rekomendasi Safety Stock</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($upcomingForecasts as $forecast)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 px-4 font-semibold text-stone-900">
                                    {{ $forecast->product?->name ?? '-' }}
                                    <span class="block text-xs font-normal text-stone-500 font-mono">{{ $forecast->product?->sku }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-stone-600 font-medium">
                                    {{ $forecast->period_date->format('F Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-amber-700">
                                    {{ number_format($forecast->forecast_demand, 2, ',', '.') }} {{ $forecast->product?->unit_of_measure }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-semibold text-stone-800">
                                    {{ number_format($forecast->safety_stock, 0, ',', '.') }} {{ $forecast->product?->unit_of_measure }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @can('access-forecasting')
                                        <a href="{{ route('forecasts.show', $forecast->product_id) }}" class="text-xs font-bold text-amber-700 hover:underline">
                                            Detail &rarr;
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
