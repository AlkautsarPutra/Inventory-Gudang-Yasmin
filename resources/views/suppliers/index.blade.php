@extends('layouts.app', ['title' => 'Mitra Supplier'])

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">Rantai Pasok Parfum</span>
            </div>
            <h1 class="mt-1 font-serif text-3xl font-bold tracking-tight text-stone-900">
                Mitra Supplier & Vendor
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Daftar produsen bibit parfum internasional, distributor pelarut kimia kosmetik, pabrik botol kaca & kemasan.
            </p>
        </div>

        @can('manage-master-data')
            <a href="{{ route('suppliers.create') }}" 
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-sm font-bold text-stone-950 shadow-md shadow-amber-500/20 hover:from-amber-400 hover:to-amber-500 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Mitra Supplier</span>
            </a>
        @endcan
    </div>

    <!-- Supplier Table Card -->
    <div class="overflow-hidden rounded-3xl border border-stone-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-stone-100 bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-4 px-6">Nama Supplier & Perusahaan</th>
                        <th class="py-4 px-6">Spesialisasi Kategori</th>
                        <th class="py-4 px-6">Kontak & Lokasi</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-4 px-6">
                                <div class="font-semibold text-stone-900">{{ $supplier->getDisplayName() }}</div>
                                <div class="text-xs text-stone-500 font-mono mt-0.5">{{ $supplier->supplier_code }}</div>
                            </td>

                            <td class="py-4 px-6">
                                <span class="inline-flex items-center rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-900 border border-amber-200">
                                    {{ $supplier->supplier_category }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-xs text-stone-600">
                                <div>{{ $supplier->email ?: '—' }}</div>
                                <div class="text-stone-400 mt-0.5">{{ $supplier->phone_number ?: ($supplier->city ? $supplier->city . ', ' . $supplier->province : '—') }}</div>
                            </td>

                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $supplier->canBeUsed() ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20' : 'bg-stone-100 text-stone-600' }}">
                                    {{ $supplier->status->label() }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-right">
                                @can('manage-master-data')
                                    <a href="{{ route('suppliers.edit', $supplier) }}" 
                                       class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-900 transition">
                                        <span>Ubah</span>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-sm text-stone-500">
                                Belum ada data mitra supplier yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($suppliers->hasPages())
            <div class="border-t border-stone-100 bg-stone-50/50 px-6 py-4">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
@endsection
