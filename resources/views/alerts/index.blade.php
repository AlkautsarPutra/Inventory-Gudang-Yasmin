@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Peringatan Stok</h1>
            <p class="mt-1 text-sm text-slate-500">Daftar produk dengan stok menipis atau habis.</p>
        </div>
        
        <form action="{{ route('alerts.check') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Periksa Stok Sekarang
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-6">Tanggal / Waktu</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Produk</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Stok & Batas</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Jenis Peringatan</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Status</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($alerts as $alert)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-slate-500 sm:pl-6">
                                {{ $alert->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <div class="font-medium text-slate-900">{{ $alert->product?->sku ?? '-' }}</div>
                                <div class="text-slate-500">{{ $alert->product?->name ?? 'Produk Dihapus' }}</div>
                                <div class="mt-1">
                                    @php
                                        $whType = $alert->product?->warehouse_type;
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $whType?->color() === 'emerald' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-700/10' }}">
                                        {{ $whType?->label() ?? 'Umum' }}
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                <span class="font-semibold text-slate-900">{{ number_format($alert->current_stock, 0, ',', '.') }}</span>
                                <span class="text-xs text-slate-400">/ {{ number_format($alert->threshold, 0, ',', '.') }}</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                @if ($alert->alert_type === 'below_rop')
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                        {{ $alert->label() }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                        {{ $alert->label() }}
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                @if ($alert->is_acknowledged)
                                    <span class="text-emerald-600 font-medium">Selesai</span>
                                    <div class="text-xs text-slate-500">oleh {{ $alert->acknowledgedBy?->name ?? 'Sistem' }}</div>
                                @else
                                    <span class="text-rose-600 font-medium">Belum Ditindak</span>
                                @endif
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                @if (!$alert->is_acknowledged)
                                    @can('record-inventory')
                                        <form action="{{ route('alerts.acknowledge', $alert) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                Tandai Selesai
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500">
                                Tidak ada peringatan stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($alerts->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 sm:px-6">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
