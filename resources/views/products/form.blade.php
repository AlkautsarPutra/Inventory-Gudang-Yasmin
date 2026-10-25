<div class="space-y-6">
    <!-- Bagian 1: Identitas & Informasi -->
    <div>
        <h3 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-2">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[11px] text-amber-900 font-bold">1</span>
            Identitas Bahan / Produk Parfum
        </h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="product_code">
                    Kode Produk (SKU) <span class="text-rose-500">*</span>
                </label>
                <input id="product_code" name="product_code" value="{{ old('product_code', $product->product_code ?? '') }}" required maxlength="50" 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                       placeholder="Contoh: PRD-000001">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="product_name">
                    Nama Bahan / Varian Parfum <span class="text-rose-500">*</span>
                </label>
                <input id="product_name" name="product_name" value="{{ old('product_name', $product->product_name ?? '') }}" required maxlength="150" 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                       placeholder="Contoh: Bibit Parfum Baccarat Rouge 540 / EDP 50ml">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="category">
                    Kategori <span class="text-rose-500">*</span>
                </label>
                <input id="category" name="category" value="{{ old('category', $product->category ?? '') }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                       placeholder="Bibit Parfum / Pelarut / Botol / Eau De Parfum">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="brand">
                    Brand / Produsen <span class="text-rose-500">*</span>
                </label>
                <input id="brand" name="brand" value="{{ old('brand', $product->brand ?? '') }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                       placeholder="Yasmin Golden Care / Mane / Firmenich / Brataco">
            </div>
        </div>
    </div>

    <!-- Bagian 2: Klasifikasi Gudang & Satuan -->
    <div class="border-t border-stone-100 pt-5">
        <h3 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-2">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[11px] text-amber-900 font-bold">2</span>
            Klasifikasi Gudang & Satuan Ukur
        </h3>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="warehouse_type">
                    Tipe Gudang <span class="text-rose-500">*</span>
                </label>
                <select id="warehouse_type" name="warehouse_type" required 
                        class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    @foreach(\App\Enums\WarehouseType::options() as $value => $label)
                        <option value="{{ $value }}" @selected(old('warehouse_type', isset($product) && $product->warehouse_type ? $product->warehouse_type->value : 'raw_material') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="unit">
                    Satuan Ukur <span class="text-rose-500">*</span>
                </label>
                <input id="unit" name="unit" value="{{ old('unit', $product->unit ?? '') }}" required maxlength="30" 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                       placeholder="LTR / PCS / KG / SET">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="status">
                    Status Produk <span class="text-rose-500">*</span>
                </label>
                <select id="status" name="status" required 
                        class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected(old('status', isset($product) ? $product->status->value : 'ACTIVE') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Bagian 3: Kebijakan Inventori -->
    <div class="border-t border-stone-100 pt-5">
        <h3 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-2">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[11px] text-amber-900 font-bold">3</span>
            Batas Stok & Waktu Pengiriman (Lead Time)
        </h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="minimum_stock">
                    Safety Stock (Min) <span class="text-rose-500">*</span>
                </label>
                <input id="minimum_stock" name="minimum_stock" type="number" min="0" value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <p class="text-[10px] text-stone-400 mt-1">Batas pengaman darurat</p>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="reorder_point">
                    Reorder Point (ROP) <span class="text-rose-500">*</span>
                </label>
                <input id="reorder_point" name="reorder_point" type="number" min="0" value="{{ old('reorder_point', $product->reorder_point ?? 0) }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <p class="text-[10px] text-stone-400 mt-1">Titik mulai pesan ke supplier</p>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="maximum_stock">
                    Maksimum Stok
                </label>
                <input id="maximum_stock" name="maximum_stock" type="number" min="0" value="{{ old('maximum_stock', $product->maximum_stock ?? '') }}" 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <p class="text-[10px] text-stone-400 mt-1">Kapasitas rak penyimpanan</p>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="lead_time_days">
                    Lead Time (Hari) <span class="text-rose-500">*</span>
                </label>
                <input id="lead_time_days" name="lead_time_days" type="number" min="1" max="365" value="{{ old('lead_time_days', $product->lead_time_days ?? 7) }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <p class="text-[10px] text-stone-400 mt-1">Lama pengiriman dari supplier</p>
            </div>
        </div>
    </div>

    <!-- Bagian 4: Deskripsi -->
    <div class="border-t border-stone-100 pt-5">
        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="description">
            Keterangan Karakter Aroma / Spesifikasi
        </label>
        <textarea id="description" name="description" rows="3" 
                  class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                  placeholder="Catatan formulasi aroma, top/middle/base notes, atau spesifikasi kemasan...">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
</div>
