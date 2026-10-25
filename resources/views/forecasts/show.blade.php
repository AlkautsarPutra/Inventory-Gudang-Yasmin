@extends('layouts.app')

@section('title', 'Peramalan - ' . $product->product_name)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Detail Peramalan: {{ $product->product_name }}</h1>
        <p class="text-sm text-slate-500">Kode: {{ $product->product_code }}</p>
    </div>
    
    <a href="{{ route('forecasts.index') }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-slate-900">
        &larr; Kembali
    </a>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="col-span-1 space-y-6">
        <!-- Product Info -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Informasi Produk</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Gudang</dt>
                    <dd class="font-medium text-slate-900">{{ $product->warehouse_type?->label() ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Satuan</dt>
                    <dd class="font-medium text-slate-900">{{ $product->unit_of_measure }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Stok Saat Ini</dt>
                    <dd class="font-medium text-slate-900">{{ $product->inventoryStock?->quantity ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Minimum Stok (SS)</dt>
                    <dd class="font-medium text-slate-900">{{ $product->minimum_stock }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Titik Pesan Kembali (ROP)</dt>
                    <dd class="font-medium text-slate-900">{{ $product->reorder_point }}</dd>
                </div>
            </dl>
        </div>

        <!-- Safety Stock Info -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Safety Stock (SS)</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Safety Stock (SS)</dt>
                    <dd class="font-medium text-slate-900">{{ number_format($safetyStockResult['safety_stock'], 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Standar Deviasi (σd)</dt>
                    <dd class="font-medium text-slate-900">{{ number_format($safetyStockResult['std_deviation'], 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Lead Time (L)</dt>
                    <dd class="font-medium text-slate-900">{{ $safetyStockResult['lead_time_days'] }} hari</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Z-Score</dt>
                    <dd class="font-medium text-slate-900">{{ $safetyStockResult['z_score'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Rata-rata Permintaan (d)</dt>
                    <dd class="font-medium text-slate-900">{{ number_format($safetyStockResult['avg_daily_demand'], 2) }} / hari</dd>
                </div>
            </dl>
        </div>
        
        <!-- SES Info -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Hasil SES</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Alpha (α) Optimal</dt>
                    <dd class="font-medium text-slate-900">{{ $optimalAlpha }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">MAE</dt>
                    <dd class="font-medium text-slate-900">{{ number_format($sesResult['mae'], 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">MAPE</dt>
                    <dd class="font-medium text-slate-900">{{ number_format($sesResult['mape'], 2) }}%</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Ramalan Berikutnya</dt>
                    <dd class="font-medium text-slate-900">{{ number_format($sesResult['next_forecast'], 2) }}</dd>
                </div>
            </dl>
            
            <form action="{{ route('forecasts.calculate') }}" method="POST" class="mt-6 border-t border-slate-200 pt-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <div class="mb-3">
                    <label for="alpha" class="mb-1 block text-xs font-medium text-slate-700">Custom Alpha (opsional)</label>
                    <input type="number" step="0.01" min="0.01" max="0.99" name="alpha" id="alpha" placeholder="Bawaan: {{ $optimalAlpha }}" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
                </div>
                
                <div class="mb-4">
                    <label for="service_level" class="mb-1 block text-xs font-medium text-slate-700">Service Level (opsional)</label>
                    <select name="service_level" id="service_level" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
                        <option value="0.95">95% (Z=1.65)</option>
                        <option value="0.99">99% (Z=2.33)</option>
                        <option value="0.975">97.5% (Z=1.96)</option>
                        <option value="0.90">90% (Z=1.28)</option>
                        <option value="0.85">85% (Z=1.04)</option>
                        <option value="0.80">80% (Z=0.84)</option>
                    </select>
                </div>
                
                <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Hitung Ulang
                </button>
            </form>
        </div>
    </div>
    
    <div class="col-span-1 lg:col-span-2">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Data Historis & Ramalan</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-slate-50 text-slate-900">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-medium">Periode</th>
                            <th scope="col" class="px-6 py-4 font-medium text-right">Permintaan Aktual</th>
                            <th scope="col" class="px-6 py-4 font-medium text-right">Ramalan</th>
                            <th scope="col" class="px-6 py-4 font-medium text-right">Selisih (Error)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($forecasts as $forecast)
                            <tr class="hover:bg-slate-50 {{ is_null($forecast->actual_demand) ? 'bg-blue-50/50' : '' }}">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $forecast->period }}</td>
                                <td class="px-6 py-4 text-right">
                                    {{ is_null($forecast->actual_demand) ? '-' : number_format($forecast->actual_demand, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{ number_format($forecast->forecasted_demand, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(is_null($forecast->actual_demand))
                                        -
                                    @else
                                        {{ number_format(abs($forecast->actual_demand - $forecast->forecasted_demand), 2) }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                    Belum ada data peramalan.
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
