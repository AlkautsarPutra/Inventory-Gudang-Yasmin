@extends('layouts.app', ['title' => 'Peringatan Stok'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-rose-700">Manajemen Risiko Stok</span>
            </div>
            <h1 class="mt-1 font-serif text-3xl font-bold tracking-tight text-stone-900">
                Peringatan Stok Bahan & Parfum
            </h1>
            <p class="text-xs text-stone-500 mt-1">Daftar bahan baku dan produk jadi yang telah menyentuh batas kritis atau ROP.</p>
        </div>
        
        <form action="{{ route('alerts.check') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-stone-900 to-stone-800 px-4 py-2.5 text-xs font-bold text-amber-300 shadow-sm hover:from-stone-800 hover:to-stone-700 hover:text-amber-200 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Periksa Seluruh Stok Sekarang</span>
            </button>
        </form>
    </div>

    <!-- Alert Table Card -->
    <div class="overflow-hidden rounded-3xl border border-stone-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-100 text-left text-sm">
                <thead class="border-b border-stone-100 bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-4 px-6">Waktu Terdeteksi</th>
                        <th class="py-4 px-6">Produk / Bahan</th>
                        <th class="py-4 px-6">Klasifikasi</th>
                        <th class="py-4 px-6 text-right">Stok Aktual / Batas</th>
                        <th class="py-4 px-6 text-center">Tingkat Urgensi</th>
                        <th class="py-4 px-6 text-center">Status Tindak Lanjut</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($alerts as $alert)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="whitespace-nowrap py-4 px-6 text-xs text-stone-500 font-mono">
                                {{ $alert->created_at->format('d M Y, H:i') }}
                            </td>

                            <td class="py-4 px-6">
                                <div class="font-semibold text-stone-900">{{ $alert->product?->name ?? 'Produk Dihapus' }}</div>
                                <div class="text-xs text-stone-500 font-mono">{{ $alert->product?->sku }}</div>
                            </td>

                            <td class="py-4 px-6">
                                @php $whType = $alert->product?->warehouse_type; @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $whType?->color() === 'emerald' ? 'bg-emerald-100 text-emerald-900' : 'bg-amber-100 text-amber-900' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $whType?->color() === 'emerald' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ $whType?->label() ?? 'Umum' }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap py-4 px-6 text-right">
                                <span class="font-bold text-stone-900 text-sm">
                                    {{ number_format($alert->current_stock, 0, ',', '.') }} {{ $alert->product?->unit }}
                                </span>
                                <span class="block text-xs text-stone-400">
                                    Batas: {{ number_format($alert->threshold, 0, ',', '.') }} {{ $alert->product?->unit }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap py-4 px-6 text-center">
                                @if ($alert->alert_type === 'out_of_stock')
                                    <span class="inline-flex items-center rounded-md bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-800">
                                        Stok Habis (Kosong)
                                    </span>
                                @elseif ($alert->alert_type === 'below_safety')
                                    <span class="inline-flex items-center rounded-md bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                        Di Bawah Safety Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                        Mencapai Reorder Point (ROP)
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap py-4 px-6 text-center">
                                @if ($alert->is_acknowledged)
                                    <div class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-xs">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span>Telah Selesai</span>
                                    </div>
                                    <div class="text-[10px] text-stone-400">oleh {{ $alert->acknowledgedBy?->name ?? 'Sistem' }}</div>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-700 ring-1 ring-rose-600/20">
                                        Belum Ditindak
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap py-4 px-6 text-right">
                                @if (!$alert->is_acknowledged)
                                    @can('record-inventory')
                                        <form action="{{ route('alerts.acknowledge', $alert) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-bold text-stone-950 shadow-sm hover:bg-amber-400 transition">
                                                Tandai Selesai &check;
                                            </button>
                                        </form>
                                    @endcan
                                @else
                                    <span class="text-xs text-stone-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sm text-stone-500">
                                <svg class="mx-auto h-10 w-10 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <p class="mt-2 font-semibold text-stone-800">Tidak ada peringatan stok aktif</p>
                                <p class="text-xs text-stone-400">Semua bahan baku dan produk jadi berada di atas batas aman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($alerts->hasPages())
            <div class="border-t border-stone-100 bg-stone-50/50 px-6 py-4">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
