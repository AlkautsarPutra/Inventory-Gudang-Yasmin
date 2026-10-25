@extends('layouts.app', ['title' => 'Riwayat Mutasi Stok'])

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">Audit & Ledger Fisik</span>
            </div>
            <h1 class="mt-1 font-serif text-3xl font-bold tracking-tight text-stone-900">
                Riwayat Transaksi Mutasi Stok
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Buku besar (*immutable ledger*) pergerakan bahan parfum masuk, keluar, dan penyesuaian stok opname.
            </p>
        </div>

        @can('record-inventory')
            <a href="{{ route('inventory-transactions.create') }}" 
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-sm font-bold text-stone-950 shadow-md shadow-amber-500/20 hover:from-amber-400 hover:to-amber-500 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Catat Transaksi Baru</span>
            </a>
        @endcan
    </div>

    <!-- Ledger Table Card -->
    <div class="overflow-hidden rounded-3xl border border-stone-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-stone-100 bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-4 px-6">Waktu / No. Mutasi</th>
                        <th class="py-4 px-6">Produk / Bahan</th>
                        <th class="py-4 px-6">Jenis Mutasi</th>
                        <th class="py-4 px-6 text-right">Jumlah Pergerakan</th>
                        <th class="py-4 px-6">Petugas (Operator)</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-4 px-6">
                                <a href="{{ route('inventory-transactions.show', $transaction) }}" 
                                   class="font-mono text-xs font-bold text-amber-800 hover:text-amber-950 hover:underline">
                                    {{ $transaction->transaction_number }}
                                </a>
                                <p class="text-xs text-stone-400 mt-0.5">
                                    {{ $transaction->transaction_date->format('d M Y, H:i') }}
                                </p>
                            </td>

                            <td class="py-4 px-6">
                                <p class="font-semibold text-stone-900 text-sm">
                                    {{ $transaction->product->product_name }}
                                </p>
                                <p class="text-xs text-stone-400 font-mono mt-0.5">
                                    {{ $transaction->product->product_code }}
                                </p>
                            </td>

                            <td class="py-4 px-6">
                                <span class="inline-flex items-center rounded-lg bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-700">
                                    {{ $transaction->transaction_type->label() }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-right font-mono">
                                <span class="font-bold text-sm {{ $transaction->movement_direction->isInbound() ? 'text-emerald-700' : 'text-stone-900' }}">
                                    {{ $transaction->movement_direction->isInbound() ? '+' : '-' }}{{ number_format((float) $transaction->quantity, 3, ',', '.') }} {{ $transaction->unit }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-stone-600 text-xs">
                                {{ $transaction->createdBy?->name ?? 'Sistem' }}
                            </td>

                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('inventory-transactions.show', $transaction) }}" 
                                   class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-900 transition">
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-sm text-stone-500">
                                Belum ada riwayat mutasi stok yang tercatat di gudang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="border-t border-stone-100 bg-stone-50/50 px-6 py-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection
