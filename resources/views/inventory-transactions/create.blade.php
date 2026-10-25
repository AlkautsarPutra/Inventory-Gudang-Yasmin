@extends('layouts.app', ['title' => 'Catat Transaksi Mutasi Stok'])

@section('content')
    <div class="mb-6">
        <a href="{{ route('inventory-transactions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-amber-800 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Riwayat Mutasi</span>
        </a>
        <h1 class="mt-2 font-serif text-3xl font-bold tracking-tight text-stone-900">
            Catat Transaksi Mutasi Stok
        </h1>
        <p class="text-xs text-stone-500 mt-1">
            Setiap catatan transaksi mutasi bersifat permanen (*immutable*) dan langsung memperbarui saldo stok fisik produk.
        </p>
    </div>

    <form method="POST" action="{{ route('inventory-transactions.store') }}" class="max-w-4xl rounded-3xl border border-stone-200/80 bg-white p-7 shadow-sm">
        @csrf
        <div class="space-y-6">
            <!-- Informasi Transaksi -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[11px] text-amber-900 font-bold">1</span>
                    Parameter Mutasi
                </h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="transaction_date">
                            Tanggal & Waktu Mutasi <span class="text-rose-500">*</span>
                        </label>
                        <input id="transaction_date" name="transaction_date" type="datetime-local" 
                               value="{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}" max="{{ now()->format('Y-m-d\TH:i') }}" required 
                               class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="transaction_type">
                            Jenis Transaksi <span class="text-rose-500">*</span>
                        </label>
                        <select id="transaction_type" name="transaction_type" required 
                                class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @foreach($types as $type)
                                <option value="{{ $type->value }}" @selected(old('transaction_type') === $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Produk & Jumlah -->
            <div class="border-t border-stone-100 pt-5">
                <h3 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[11px] text-amber-900 font-bold">2</span>
                    Produk & Kuantitas
                </h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="product_id">
                            Pilih Bahan / Produk Parfum <span class="text-rose-500">*</span>
                        </label>
                        <select id="product_id" name="product_id" required 
                                class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-unit="{{ $product->unit }}" @selected(old('product_id') == $product->id)>
                                    [{{ $product->warehouse_type?->label() }}] {{ $product->product_code }} — {{ $product->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="unit">
                            Satuan
                        </label>
                        <input id="unit" name="unit" value="{{ old('unit') }}" readonly required 
                               class="w-full rounded-xl border border-stone-200 bg-stone-100 px-3.5 py-2.5 text-sm text-stone-600 font-semibold" placeholder="Otomatis">
                    </div>

                    <div id="quantity_field" class="sm:col-span-3">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="quantity">
                            Jumlah Kuantitas Masuk / Keluar <span class="text-rose-500">*</span>
                        </label>
                        <input id="quantity" name="quantity" type="number" min="0.001" step="0.001" value="{{ old('quantity') }}" 
                               class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                               placeholder="Contoh: 10 / 2.500">
                    </div>

                    <div id="physical_quantity_field" class="hidden sm:col-span-3">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="physical_quantity">
                            Hasil Stok Opname Fisik Aktual <span class="text-rose-500">*</span>
                        </label>
                        <input id="physical_quantity" name="physical_quantity" type="number" min="0" step="0.001" value="{{ old('physical_quantity') }}" 
                               class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                               placeholder="Masukkan total hitungan fisik di rak gudang">
                        <span class="mt-1 block text-xs text-stone-500">Sistem akan menghitung selisih penyesuaian otomatis.</span>
                    </div>
                </div>
            </div>

            <!-- Supplier & Dokumen Referensi -->
            <div class="border-t border-stone-100 pt-5">
                <h3 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[11px] text-amber-900 font-bold">3</span>
                    Informasi Supplier & Dokumen Referensi
                </h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="supplier_id">
                            Mitra Supplier <span class="text-stone-400 font-normal">(Wajib untuk Penerimaan)</span>
                        </label>
                        <select id="supplier_id" name="supplier_id" 
                                class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <option value="">-- Tanpa Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                    {{ $supplier->supplier_code }} — {{ $supplier->getDisplayName() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="reference_type">
                            Tipe Referensi
                        </label>
                        <input id="reference_type" name="reference_type" value="{{ old('reference_type') }}" maxlength="30" 
                               class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" 
                               placeholder="PO / SURAT_JALAN / WO">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="reference_number">
                            Nomor Dokumen / Nota
                        </label>
                        <input id="reference_number" name="reference_number" value="{{ old('reference_number') }}" maxlength="100" 
                               class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" 
                               placeholder="PO-2026-001 / SJ-088">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="notes">
                            Catatan Operasional <span id="adjustment_note" class="hidden text-rose-600 font-normal">(Wajib untuk Penyesuaian Stok Opname)</span>
                        </label>
                        <textarea id="notes" name="notes" rows="3" maxlength="5000" 
                                  class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" 
                                  placeholder="Catatan batch penerimaan, nomor faktur, atau keterangan peracikan parfum...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end gap-3 border-t border-stone-100 pt-5">
            <a href="{{ route('inventory-transactions.index') }}" 
               class="rounded-xl border border-stone-200 px-5 py-2.5 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition">
                Batal
            </a>
            <button type="submit" 
                    class="rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-2.5 text-xs font-bold text-stone-950 shadow-md shadow-amber-500/20 hover:from-amber-400 hover:to-amber-500 transition">
                Posting Transaksi ke Buku Ledger
            </button>
        </div>
    </form>

    <script>
        const product = document.getElementById('product_id');
        const unit = document.getElementById('unit');
        const type = document.getElementById('transaction_type');
        const updateForm = () => {
            unit.value = product.options[product.selectedIndex]?.dataset.unit || '';
            const adjustment = type.value === 'stock_adjustment';
            document.getElementById('quantity_field').classList.toggle('hidden', adjustment);
            document.getElementById('physical_quantity_field').classList.toggle('hidden', !adjustment);
            document.getElementById('adjustment_note').classList.toggle('hidden', !adjustment);
        };
        product.addEventListener('change', updateForm);
        type.addEventListener('change', updateForm);
        updateForm();
    </script>
@endsection
