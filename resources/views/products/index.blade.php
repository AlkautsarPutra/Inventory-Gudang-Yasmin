@extends('layouts.app', ['title' => 'Katalog Produk & Bahan'])

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">Master Data Gudang</span>
            </div>
            <h1 class="mt-1 font-serif text-3xl font-bold tracking-tight text-stone-900">
                Katalog Produk & Bahan Parfum
            </h1>
            <p class="text-xs text-stone-500 mt-1">Kelola stok bahan baku bibit parfum, pelarut, botol, serta stok parfum siap jual.</p>
        </div>

        @can('manage-master-data')
            <a href="{{ route('products.create') }}" 
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-sm font-bold text-stone-950 shadow-md shadow-amber-500/20 hover:from-amber-400 hover:to-amber-500 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Produk / Bahan</span>
            </a>
        @endcan
    </div>

    <!-- Warehouse Type Tabs -->
    <div class="mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('products.index') }}" 
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition {{ !request()->filled('warehouse_type') ? 'bg-stone-900 text-amber-300 shadow-sm' : 'border border-stone-200 bg-white text-stone-600 hover:bg-stone-50' }}">
            <span>Semua Katalog</span>
        </a>

        <a href="{{ route('products.index', ['warehouse_type' => 'raw_material']) }}" 
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition {{ request('warehouse_type') === 'raw_material' ? 'bg-amber-600 text-white shadow-sm' : 'border border-stone-200 bg-white text-stone-600 hover:bg-stone-50' }}">
            <span class="h-2 w-2 rounded-full {{ request('warehouse_type') === 'raw_material' ? 'bg-amber-200' : 'bg-amber-500' }}"></span>
            <span>Gudang Bahan Mentah (Bibit, Pelarut, Botol)</span>
        </a>

        <a href="{{ route('products.index', ['warehouse_type' => 'finished_goods']) }}" 
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition {{ request('warehouse_type') === 'finished_goods' ? 'bg-emerald-700 text-white shadow-sm' : 'border border-stone-200 bg-white text-stone-600 hover:bg-stone-50' }}">
            <span class="h-2 w-2 rounded-full {{ request('warehouse_type') === 'finished_goods' ? 'bg-emerald-200' : 'bg-emerald-500' }}"></span>
            <span>Gudang Produk Jadi (Parfum EDP, Roll-On)</span>
        </a>
    </div>

    <!-- Product Table Card -->
    <div class="overflow-hidden rounded-3xl border border-stone-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-stone-100 bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-4 px-6">Produk / Bahan</th>
                        <th class="py-4 px-6">Kategori & Brand</th>
                        <th class="py-4 px-6">Klasifikasi Gudang</th>
                        <th class="py-4 px-6 text-right">Stok Aktual</th>
                        <th class="py-4 px-6 text-right">Kebijakan Stok (ROP)</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($products as $product)
                        @php
                            $currentStock = (float) ($product->inventoryStock?->quantity ?? 0);
                            $isLowStock = $product->needsReorder((int) floor($currentStock));
                        @endphp
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-4 px-6">
                                <div class="font-semibold text-stone-900">
                                    {{ $product->product_name }}
                                </div>
                                <div class="text-xs text-stone-500 font-mono mt-0.5">
                                    {{ $product->product_code }}
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                <div class="text-stone-800 font-medium text-xs">{{ $product->category }}</div>
                                <div class="text-xs text-stone-500">{{ $product->brand }}</div>
                            </td>

                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->isRawMaterial() ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-900' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $product->isRawMaterial() ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                    {{ $product->warehouse_type?->label() ?? 'Umum' }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-right">
                                <div class="font-bold text-sm {{ $isLowStock ? 'text-rose-600' : 'text-stone-900' }}">
                                    {{ number_format($currentStock, 0, ',', '.') }} {{ $product->unit }}
                                </div>
                                @if ($isLowStock)
                                    <span class="inline-block rounded bg-rose-50 px-1.5 py-0.5 text-[10px] font-bold text-rose-700">
                                        Perlu Reorder
                                    </span>
                                @else
                                    <span class="text-[10px] text-emerald-700 font-semibold">Tersedia Aman</span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-right text-xs">
                                <div class="text-stone-700 font-semibold">
                                    ROP: {{ number_format($product->reorder_point, 0, ',', '.') }} {{ $product->unit }}
                                </div>
                                <div class="text-stone-400 text-[11px]">
                                    Min (SS): {{ number_format($product->minimum_stock, 0, ',', '.') }}
                                </div>
                            </td>

                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $product->isActive() ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20' : 'bg-stone-100 text-stone-600' }}">
                                    {{ $product->status->label() }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-right">
                                @can('manage-master-data')
                                    <a href="{{ route('products.edit', $product) }}" 
                                       class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 hover:border-amber-400 hover:bg-amber-50/50 hover:text-amber-900 transition">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                        <span>Ubah</span>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <p class="text-sm font-semibold text-stone-700">Tidak ada produk atau bahan ditemukan</p>
                                <p class="text-xs text-stone-400 mt-1">Silakan sesuaikan filter tipe gudang atau tambahkan master produk baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="border-t border-stone-100 bg-stone-50/50 px-6 py-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
