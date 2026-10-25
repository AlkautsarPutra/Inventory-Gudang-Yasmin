@extends('layouts.app', ['title' => 'Peramalan Permintaan (SES)'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">Intelligence & Analytics</span>
            </div>
            <h1 class="mt-1 font-serif text-3xl font-bold tracking-tight text-stone-900">
                Peramalan Permintaan & Safety Stock
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Kalkulasi otomatis proyeksi kebutuhan stok bulan depan menggunakan Single Exponential Smoothing (SES) dan batas pengaman persediaan.
            </p>
        </div>
        
        <form action="{{ route('forecasts.calculate-all') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-xs font-bold text-stone-950 shadow-md shadow-amber-500/20 hover:from-amber-400 hover:to-amber-500 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Hitung Peramalan Semua Produk</span>
            </button>
        </form>
    </div>

    <!-- Overview Table Card -->
    <div class="overflow-hidden rounded-3xl border border-stone-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-stone-100 bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-4 px-6">Produk / Bahan</th>
                        <th class="py-4 px-6">Klasifikasi Gudang</th>
                        <th class="py-4 px-6 text-right">Stok Aktual</th>
                        <th class="py-4 px-6 text-right">Proyeksi Bulan Depan</th>
                        <th class="py-4 px-6 text-right">MAE (Error Rata-rata)</th>
                        <th class="py-4 px-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($products as $product)
                        @php
                            $forecast = $latestForecasts->get($product->id);
                        @endphp
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-4 px-6">
                                <div class="font-semibold text-stone-900">{{ $product->product_name }}</div>
                                <div class="text-xs text-stone-500 font-mono mt-0.5">{{ $product->product_code }}</div>
                            </td>

                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->isFinishedGoods() ? 'bg-emerald-100 text-emerald-900' : 'bg-amber-100 text-amber-900' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $product->isFinishedGoods() ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ $product->warehouse_type?->label() ?? 'Umum' }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-right font-medium text-stone-800">
                                {{ number_format((float) ($product->inventoryStock?->quantity ?? 0), 0, ',', '.') }} {{ $product->unit_of_measure }}
                            </td>

                            <td class="py-4 px-6 text-right">
                                @if($forecast)
                                    <div class="font-bold text-amber-800 text-sm">
                                        {{ number_format($forecast->forecasted_demand, 2, ',', '.') }} {{ $product->unit_of_measure }}
                                    </div>
                                    <span class="text-[10px] text-stone-400">Periode: {{ $forecast->period }}</span>
                                @else
                                    <span class="text-stone-400 italic text-xs">Belum dihitung</span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-right">
                                @if($forecast)
                                    <span class="font-mono text-xs text-stone-700">
                                        {{ number_format($forecast->mae, 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-stone-400 italic text-xs">-</span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('forecasts.show', $product) }}" 
                                   class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-900 transition">
                                    <span>Analisis Detail</span>
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-sm text-stone-500">
                                Belum ada produk aktif yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
