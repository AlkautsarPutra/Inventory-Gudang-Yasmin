@extends('layouts.app')

@section('title', 'Peramalan Permintaan')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Peramalan Permintaan</h1>
        <p class="text-sm text-slate-500">Single Exponential Smoothing</p>
    </div>
    
    <form action="{{ route('forecasts.calculate-all') }}" method="POST">
        @csrf
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            Hitung Semua
        </button>
    </form>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="border-b border-slate-200 bg-slate-50 text-slate-900">
                <tr>
                    <th scope="col" class="px-6 py-4 font-medium">Kode Produk</th>
                    <th scope="col" class="px-6 py-4 font-medium">Nama Produk</th>
                    <th scope="col" class="px-6 py-4 font-medium">Tipe Gudang</th>
                    <th scope="col" class="px-6 py-4 font-medium">Stok Saat Ini</th>
                    <th scope="col" class="px-6 py-4 font-medium">Ramalan Bulan Depan</th>
                    <th scope="col" class="px-6 py-4 font-medium">MAE</th>
                    <th scope="col" class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($products as $product)
                    @php
                        $forecast = $latestForecasts->get($product->id);
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $product->product_code }}</td>
                        <td class="px-6 py-4">{{ $product->product_name }}</td>
                        <td class="px-6 py-4">
                            @if($product->warehouse_type)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $product->isFinishedGoods() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $product->warehouse_type->label() }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $product->inventoryStock?->quantity ?? 0 }} {{ $product->unit_of_measure }}</td>
                        <td class="px-6 py-4">
                            @if($forecast)
                                {{ number_format($forecast->forecasted_demand, 2) }} {{ $product->unit_of_measure }}
                            @else
                                <span class="text-slate-400 italic">Belum dihitung</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($forecast)
                                {{ number_format($forecast->mae, 2) }}
                            @else
                                <span class="text-slate-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('forecasts.show', $product) }}" class="text-blue-600 hover:text-blue-900 hover:underline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                            Belum ada produk aktif yang tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
