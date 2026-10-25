@extends('layouts.app', ['title' => 'Detail Peramalan - ' . $product->product_name])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <a href="{{ route('forecasts.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-amber-800 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Peramalan</span>
        </a>
        <h1 class="mt-2 font-serif text-3xl font-bold tracking-tight text-stone-900">
            Detail Peramalan & Kebijakan Stok
        </h1>
        <p class="text-xs text-stone-500 mt-1">
            Produk: <strong class="text-stone-800">{{ $product->product_name }}</strong> (SKU: {{ $product->product_code }})
        </p>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Left Column: Metrics & Parameter Controls -->
    <div class="col-span-1 space-y-6">
        <!-- 1. Informasi Produk -->
        <div class="rounded-3xl border border-stone-200/80 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-4 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
                <span>Status Stok Saat Ini</span>
            </h2>
            <dl class="space-y-3 text-xs">
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">Tipe Gudang</dt>
                    <dd class="font-semibold text-stone-900">{{ $product->warehouse_type?->label() ?? '-' }}</dd>
                </div>
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">Satuan Ukur</dt>
                    <dd class="font-semibold text-stone-900">{{ $product->unit_of_measure }}</dd>
                </div>
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">Stok Aktual di Gudang</dt>
                    <dd class="font-bold text-stone-900 text-sm">{{ number_format((float) ($product->inventoryStock?->quantity ?? 0), 0, ',', '.') }} {{ $product->unit }}</dd>
                </div>
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">Safety Stock Terdaftar (Min)</dt>
                    <dd class="font-semibold text-stone-900">{{ $product->minimum_stock }} {{ $product->unit }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Titik Pesan Kembali (ROP)</dt>
                    <dd class="font-bold text-amber-800">{{ $product->reorder_point }} {{ $product->unit }}</dd>
                </div>
            </dl>
        </div>

        <!-- 2. Parameter Safety Stock & ROP -->
        <div class="rounded-3xl border border-stone-200/80 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-2 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <span>Kalkulasi Safety Stock (SS)</span>
            </h2>
            <p class="text-[11px] text-stone-400 mb-4 font-mono">Rumus: SS = Z &times; &sigma;d &times; &radic;L</p>

            <dl class="space-y-3 text-xs">
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">Hasil Safety Stock (SS)</dt>
                    <dd class="font-bold text-emerald-700 text-sm">{{ number_format($safetyStockResult['safety_stock'], 2, ',', '.') }} {{ $product->unit }}</dd>
                </div>
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">Standar Deviasi Harian (&sigma;d)</dt>
                    <dd class="font-semibold text-stone-800 font-mono">{{ number_format($safetyStockResult['std_deviation'], 2, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">Lead Time Supplier (L)</dt>
                    <dd class="font-semibold text-stone-800">{{ $safetyStockResult['lead_time_days'] }} Hari</dd>
                </div>
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">Faktor Layanan (Z-Score)</dt>
                    <dd class="font-semibold text-stone-800 font-mono">{{ $safetyStockResult['z_score'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Rata-rata Permintaan / Hari (d)</dt>
                    <dd class="font-semibold text-stone-800">{{ number_format($safetyStockResult['avg_daily_demand'], 2, ',', '.') }} {{ $product->unit }}</dd>
                </div>
            </dl>
        </div>
        
        <!-- 3. Hasil SES & Form Hitung Ulang -->
        <div class="rounded-3xl border border-stone-200/80 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-2 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
                <span>Hasil Single Exponential Smoothing</span>
            </h2>
            <dl class="space-y-3 text-xs mb-5">
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">Konstanta Pemulusan (&alpha;) Optimal</dt>
                    <dd class="font-bold text-amber-800 font-mono text-sm">{{ $optimalAlpha }}</dd>
                </div>
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">Mean Absolute Error (MAE)</dt>
                    <dd class="font-semibold text-stone-800 font-mono">{{ number_format($sesResult['mae'], 2, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between border-b border-stone-100 pb-2">
                    <dt class="text-stone-500">MAPE (Persentase Error)</dt>
                    <dd class="font-semibold text-stone-800 font-mono">{{ number_format($sesResult['mape'], 2, ',', '.') }}%</dd>
                </div>
                <div class="flex justify-between bg-amber-50/70 p-2.5 rounded-xl border border-amber-200/50">
                    <dt class="text-amber-950 font-semibold">Ramalan Permintaan Bulan Depan</dt>
                    <dd class="font-bold text-amber-900 text-sm">{{ number_format($sesResult['next_forecast'], 2, ',', '.') }} {{ $product->unit }}</dd>
                </div>
            </dl>
            
            <form action="{{ route('forecasts.calculate') }}" method="POST" class="border-t border-stone-100 pt-4 space-y-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <div>
                    <label for="alpha" class="block text-xs font-semibold text-stone-600 mb-1">Custom Alpha (&alpha;) (Opsional)</label>
                    <input type="number" step="0.01" min="0.01" max="0.99" name="alpha" id="alpha" placeholder="Bawaan Optimal: {{ $optimalAlpha }}" 
                           class="w-full rounded-xl border border-stone-300 px-3 py-2 text-xs text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                
                <div>
                    <label for="service_level" class="block text-xs font-semibold text-stone-600 mb-1">Tingkat Layanan (Service Level)</label>
                    <select name="service_level" id="service_level" 
                            class="w-full rounded-xl border border-stone-300 px-3 py-2 text-xs text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="0.95" selected>95% (Z = 1.65 - Standar Parfum)</option>
                        <option value="0.99">99% (Z = 2.33 - Sangat Ketat)</option>
                        <option value="0.975">97.5% (Z = 1.96)</option>
                        <option value="0.90">90% (Z = 1.28)</option>
                        <option value="0.85">85% (Z = 1.04)</option>
                    </select>
                </div>
                
                <button type="submit" 
                        class="w-full rounded-xl bg-gradient-to-r from-stone-900 to-stone-800 px-4 py-2.5 text-xs font-bold text-amber-300 shadow-sm hover:from-stone-800 hover:to-stone-700 hover:text-amber-200 transition">
                    Hitung & Terapkan ke Kebijakan Stok
                </button>
            </form>
        </div>
    </div>
    
    <!-- Right Column: Historical Data & Forecast Table -->
    <div class="col-span-1 lg:col-span-2">
        <div class="overflow-hidden rounded-3xl border border-stone-200/80 bg-white shadow-sm">
            <div class="border-b border-stone-100 bg-stone-50/60 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-lg font-bold text-stone-900">Riwayat Permintaan & Hasil Ramalan Per Periode</h2>
                    <p class="text-xs text-stone-500">Perbandingan data aktual pengeluaran gudang vs proyeksi SES</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-stone-100 bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                        <tr>
                            <th class="py-3.5 px-6">Periode</th>
                            <th class="py-3.5 px-6 text-right">Permintaan Aktual</th>
                            <th class="py-3.5 px-6 text-right">Ramalan (SES)</th>
                            <th class="py-3.5 px-6 text-right">Selisih (Error Absolut)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($forecasts as $forecast)
                            <tr class="hover:bg-stone-50/60 transition {{ is_null($forecast->actual_demand) ? 'bg-amber-50/40 font-semibold' : '' }}">
                                <td class="py-3.5 px-6 text-xs text-stone-900 font-mono">
                                    {{ $forecast->period }}
                                    @if(is_null($forecast->actual_demand))
                                        <span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] text-amber-900 font-bold">Proyeksi Depan</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-right text-xs">
                                    {{ is_null($forecast->actual_demand) ? '-' : number_format($forecast->actual_demand, 2, ',', '.') . ' ' . $product->unit }}
                                </td>
                                <td class="py-3.5 px-6 text-right text-xs font-semibold text-amber-900">
                                    {{ number_format($forecast->forecasted_demand, 2, ',', '.') }} {{ $product->unit }}
                                </td>
                                <td class="py-3.5 px-6 text-right text-xs font-mono text-stone-600">
                                    @if(is_null($forecast->actual_demand))
                                        -
                                    @else
                                        {{ number_format(abs($forecast->actual_demand - $forecast->forecasted_demand), 2, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-xs text-stone-500">
                                    Belum ada data peramalan yang disimpan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
