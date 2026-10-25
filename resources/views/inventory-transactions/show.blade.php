@extends('layouts.app', ['title' => 'Mutasi ' . $transaction->transaction_number])

@section('content')
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('inventory-transactions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-amber-800 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Riwayat Mutasi</span>
            </a>
            <div class="flex items-center gap-3 mt-2">
                <h1 class="font-serif text-3xl font-bold tracking-tight text-stone-900 font-mono">
                    {{ $transaction->transaction_number }}
                </h1>
                <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700 border border-stone-200">
                    Buku Ledger Permanen
                </span>
            </div>
            <p class="text-xs text-stone-500 mt-1">
                Dicatat pada: {{ $transaction->transaction_date->format('d M Y, H:i') }}
            </p>
        </div>

        @can('reverse-inventory')
            @if($transaction->transaction_type !== \App\Enums\InventoryTransactionType::REVERSAL && $transaction->reversal === null)
                <a href="#reversal" 
                   class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-800 hover:bg-rose-100 transition">
                    Batalkan / Balikkan Transaksi (Reversal)
                </a>
            @endif
        @endcan
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Main Details -->
        <div class="rounded-3xl border border-stone-200/80 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-4 pb-2 border-b border-stone-100">
                Rincian Mutasi Barang
            </h2>

            <dl class="grid gap-5 sm:grid-cols-2 text-xs">
                <div>
                    <dt class="text-stone-400 font-semibold uppercase tracking-wider">Produk / Bahan</dt>
                    <dd class="mt-1 font-semibold text-stone-900 text-sm">
                        {{ $transaction->product->product_name }}
                        <span class="block text-xs font-mono font-normal text-stone-500 mt-0.5">{{ $transaction->product->product_code }}</span>
                    </dd>
                </div>

                <div>
                    <dt class="text-stone-400 font-semibold uppercase tracking-wider">Jumlah Mutasi</dt>
                    <dd class="mt-1 text-xl font-bold font-mono {{ $transaction->movement_direction->isInbound() ? 'text-emerald-700' : 'text-stone-900' }}">
                        {{ $transaction->movement_direction->isInbound() ? '+' : '-' }}{{ number_format((float) $transaction->quantity, 3, ',', '.') }} {{ $transaction->unit }}
                    </dd>
                </div>

                <div>
                    <dt class="text-stone-400 font-semibold uppercase tracking-wider">Jenis Transaksi</dt>
                    <dd class="mt-1 font-semibold text-stone-800 text-sm">
                        {{ $transaction->transaction_type->label() }}
                    </dd>
                </div>

                <div>
                    <dt class="text-stone-400 font-semibold uppercase tracking-wider">Mitra Supplier</dt>
                    <dd class="mt-1 font-medium text-stone-800">
                        {{ $transaction->supplier?->getDisplayName() ?? '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-stone-400 font-semibold uppercase tracking-wider">Dokumen Referensi</dt>
                    <dd class="mt-1 font-medium text-stone-800">
                        {{ $transaction->reference_type ? $transaction->reference_type . ' · ' . $transaction->reference_number : '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-stone-400 font-semibold uppercase tracking-wider">Operator Pembuat</dt>
                    <dd class="mt-1 font-medium text-stone-800">
                        {{ $transaction->createdBy?->name ?? 'Sistem' }}
                    </dd>
                </div>

                <div class="sm:col-span-2 border-t border-stone-100 pt-3">
                    <dt class="text-stone-400 font-semibold uppercase tracking-wider">Catatan Tambahan</dt>
                    <dd class="mt-1 whitespace-pre-wrap text-stone-700 font-normal leading-relaxed">
                        {{ $transaction->notes ?: 'Tidak ada catatan tambahan.' }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Ledger Integrity Info -->
        <div class="rounded-3xl border border-amber-200/80 bg-amber-50/50 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 border border-amber-300">
                    Integritas Audit Ledger
                </span>
                <p class="mt-3 text-xs text-stone-600 leading-relaxed">
                    Sistem ini menerapkan standar *immutable double-entry ledger*. Transaksi yang sudah terposting tidak dapat diedit atau dihapus untuk menjamin akuntabilitas audit persediaan.
                </p>

                @if($transaction->reversalOf)
                    <div class="mt-4 rounded-xl border border-amber-200 bg-white p-3 text-xs">
                        <span class="text-stone-500 block">Merupakan pembatalan atas:</span>
                        <a class="font-bold text-amber-800 hover:underline font-mono" href="{{ route('inventory-transactions.show', $transaction->reversalOf) }}">
                            {{ $transaction->reversalOf->transaction_number }}
                        </a>
                    </div>
                @endif

                @if($transaction->reversal)
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs">
                        <span class="text-rose-600 font-semibold block">Telah dibatalkan oleh:</span>
                        <a class="font-bold text-rose-800 hover:underline font-mono" href="{{ route('inventory-transactions.show', $transaction->reversal) }}">
                            {{ $transaction->reversal->transaction_number }}
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-6 border-t border-amber-200/60 pt-4 text-[11px] text-stone-500">
                ID Ledger Internal: #{{ $transaction->id }}
            </div>
        </div>
    </div>

    <!-- Reversal Form -->
    @can('reverse-inventory')
        @if($transaction->transaction_type !== \App\Enums\InventoryTransactionType::REVERSAL && $transaction->reversal === null)
            <div id="reversal" class="mt-8 max-w-3xl rounded-3xl border border-rose-200 bg-rose-50/90 p-6 shadow-sm">
                <h2 class="font-serif text-lg font-bold text-rose-950">Batalkan Transaksi Ini (Create Reversal)</h2>
                <p class="mt-1 text-xs text-rose-700">
                    Tindakan ini akan membuat transaksi kebalikan (*counter-balancing entry*) untuk mengembalikan saldo stok ke kondisi sebelum transaksi ini dibuat.
                </p>
                <form method="POST" action="{{ route('inventory-transactions.reverse', $transaction) }}" class="mt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-rose-950 mb-1" for="notes">
                            Alasan Pembatalan <span class="text-rose-600">*</span>
                        </label>
                        <textarea id="notes" name="notes" rows="3" required 
                                  class="w-full rounded-xl border border-rose-300 bg-white px-3.5 py-2 text-xs text-stone-900 shadow-sm focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500"
                                  placeholder="Contoh: Salah input jumlah kuantitas, atau retur nota supplier..."></textarea>
                    </div>
                    <button type="submit" 
                            class="mt-3 rounded-xl bg-rose-700 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-rose-800 transition">
                        Konfirmasi Pembatalan Transaksi
                    </button>
                </form>
            </div>
        @endif
    @endcan
@endsection
