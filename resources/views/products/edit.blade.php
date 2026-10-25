@extends('layouts.app', ['title' => 'Ubah Produk - ' . $product->product_name])

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-amber-800 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Katalog Produk</span>
            </a>
            <h1 class="mt-2 font-serif text-3xl font-bold tracking-tight text-stone-900">
                Ubah Data: {{ $product->product_name }}
            </h1>
            <p class="text-xs text-stone-500 mt-1">Perbarui kebijakan stok, lead time, atau spesifikasi bahan.</p>
        </div>

        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan produk ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-xl border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50 transition">
                Nonaktifkan Produk
            </button>
        </form>
    </div>

    <form method="POST" action="{{ route('products.update', $product) }}" class="max-w-4xl rounded-3xl border border-stone-200/80 bg-white p-7 shadow-sm">
        @csrf
        @method('PUT')
        @include('products.form')

        <div class="mt-8 flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
            <a href="{{ route('products.index') }}" 
               class="rounded-xl border border-stone-200 px-5 py-2.5 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition">
                Batal
            </a>
            <button type="submit" 
                    class="rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-2.5 text-xs font-bold text-stone-950 shadow-md shadow-amber-500/20 hover:from-amber-400 hover:to-amber-500 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection
