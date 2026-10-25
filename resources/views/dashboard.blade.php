@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-cyan-700">Operations overview</p>
            <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
        </div>
        @can('record-inventory')
            <a href="{{ route('inventory-transactions.create') }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Record transaction</a>
        @endcan
    </div>

    @if ($activeAlerts->isNotEmpty())
        <div class="mb-8 rounded-2xl border border-rose-200 bg-rose-50 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-rose-900">Peringatan Kritis (Alerts)</h2>
                    <p class="text-sm text-rose-700">Ada {{ $activeAlerts->count() }} peringatan stok yang perlu ditindaklanjuti.</p>
                </div>
                <a href="{{ route('alerts.index') }}" class="text-sm font-semibold text-rose-700 hover:text-rose-900">Lihat Semua Alerts &rarr;</a>
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($activeAlerts as $alert)
                    <div class="rounded-xl border border-rose-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            @if ($alert->alert_type === 'below_rop')
                                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                    {{ $alert->label() }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                    {{ $alert->label() }}
                                </span>
                            @endif
                            @can('record-inventory')
                                <form action="{{ route('alerts.acknowledge', $alert) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-blue-600 hover:text-blue-800">Tandai Selesai</button>
                                </form>
                            @endcan
                        </div>
                        <p class="mt-2 font-medium text-slate-900">{{ $alert->product?->name ?? 'Produk Dihapus' }}</p>
                        <p class="text-sm text-slate-500">Stok: {{ number_format($alert->current_stock, 0, ',', '.') }} / {{ number_format($alert->threshold, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 relative overflow-hidden">
            <div class="absolute right-0 top-0 -mr-4 -mt-4 opacity-10">
                <svg class="h-24 w-24" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 9h-2V7h-2v5H6v2h2v5h2v-5h2v-2z"></path></svg>
            </div>
            <p class="text-sm font-medium text-amber-800">Gudang Bahan Mentah</p>
            <p class="mt-2 text-3xl font-bold text-amber-950">{{ $rawMaterialCount }} Produk</p>
            <div class="mt-4 flex items-center justify-between text-sm">
                <span class="text-amber-800">Status Aman: {{ $rawMaterialCount - $rawMaterialLowStockCount }}</span>
                @if ($rawMaterialLowStockCount > 0)
                    <span class="rounded-full bg-rose-100 px-2.5 py-0.5 font-medium text-rose-800">Perlu Reorder: {{ $rawMaterialLowStockCount }}</span>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 relative overflow-hidden">
            <div class="absolute right-0 top-0 -mr-4 -mt-4 opacity-10">
                <svg class="h-24 w-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
            </div>
            <p class="text-sm font-medium text-emerald-800">Gudang Produk Jadi</p>
            <p class="mt-2 text-3xl font-bold text-emerald-950">{{ $finishedGoodsCount }} Produk</p>
            <div class="mt-4 flex items-center justify-between text-sm">
                <span class="text-emerald-800">Status Aman: {{ $finishedGoodsCount - $finishedGoodsLowStockCount }}</span>
                @if ($finishedGoodsLowStockCount > 0)
                    <span class="rounded-full bg-rose-100 px-2.5 py-0.5 font-medium text-rose-800">Perlu Reorder: {{ $finishedGoodsLowStockCount }}</span>
                @endif
            </div>
        </div>
        
        <div class="rounded-2xl border border-slate-200 bg-white p-5 flex flex-col justify-center">
            <p class="text-sm text-slate-500">Total Active Suppliers</p>
            <p class="mt-2 text-3xl font-bold">{{ $activeSupplierCount }}</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-semibold">Low Stock Products</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($lowStockProducts->take(8) as $product)
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <div>
                            <span class="font-medium text-slate-900">{{ $product->product_name }}</span>
                            <span class="ml-2 text-slate-500">{{ $product->product_code }}</span>
                            <div class="mt-1">
                                @php $whType = $product->warehouse_type; @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $whType?->color() === 'emerald' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-700/10' }}">
                                    {{ $whType?->label() ?? 'Unknown' }}
                                </span>
                            </div>
                        </div>
                        <span class="font-semibold text-rose-600">{{ number_format($product->inventoryStock?->quantity ?? 0, 0, ',', '.') }} {{ $product->unit }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-sm text-slate-500 text-center">No low stock products.</div>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-semibold">Recent Transactions</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentTransactions as $transaction)
                    <a href="{{ route('inventory-transactions.show', $transaction) }}" class="flex items-center justify-between px-5 py-3 text-sm hover:bg-slate-50">
                        <div>
                            <span class="font-medium text-slate-900">{{ $transaction->product->product_name }}</span>
                            <span class="ml-2 text-slate-500">{{ $transaction->transaction_number }}</span>
                        </div>
                        <span class="font-semibold {{ $transaction->movement_direction->isInbound() ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $transaction->movement_direction->isInbound() ? '+' : '-' }}{{ number_format($transaction->quantity, 0, ',', '.') }}
                        </span>
                    </a>
                @empty
                    <div class="px-5 py-8 text-sm text-slate-500 text-center">No transactions recorded.</div>
                @endforelse
            </div>
        </section>
    </div>

    @if ($upcomingForecasts->isNotEmpty())
        <div class="mt-8 rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-semibold">Preview Peramalan (Bulan Depan)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Produk</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Periode</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Ramalan (Forecast)</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Safety Stock</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach ($upcomingForecasts as $forecast)
                            <tr>
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $forecast->product?->name ?? '-' }}</td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500">{{ $forecast->period_date->format('F Y') }}</td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">{{ number_format($forecast->forecast_demand, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-purple-600">{{ number_format($forecast->safety_stock, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
