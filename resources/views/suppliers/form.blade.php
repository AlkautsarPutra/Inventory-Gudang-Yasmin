<div class="space-y-6">
    <div>
        <h3 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-2">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[11px] text-amber-900 font-bold">1</span>
            Identitas Supplier & Perusahaan
        </h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="supplier_code">
                    Kode Supplier <span class="text-rose-500">*</span>
                </label>
                <input id="supplier_code" name="supplier_code" value="{{ old('supplier_code', $supplier->supplier_code ?? '') }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" 
                       placeholder="SUP-000001">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="supplier_name">
                    Nama Dagang / Merek Supplier <span class="text-rose-500">*</span>
                </label>
                <input id="supplier_name" name="supplier_name" value="{{ old('supplier_name', $supplier->supplier_name ?? '') }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" 
                       placeholder="Contoh: PT Mane Indonesia">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="company_name">
                    Nama Badan Hukum (PT/CV) <span class="text-rose-500">*</span>
                </label>
                <input id="company_name" name="company_name" value="{{ old('company_name', $supplier->company_name ?? '') }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" 
                       placeholder="PT Mane Indonesia Fragrance & Flavors">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="supplier_category">
                    Kategori Pasokan <span class="text-rose-500">*</span>
                </label>
                <input id="supplier_category" name="supplier_category" value="{{ old('supplier_category', $supplier->supplier_category ?? '') }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" 
                       placeholder="Bibit Parfum / Pelarut & Kimia / Botol Kemasan / Box">
            </div>
        </div>
    </div>

    <div class="border-t border-stone-100 pt-5">
        <h3 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-2">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[11px] text-amber-900 font-bold">2</span>
            Kontak & Penanggung Jawab
        </h3>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="contact_person">
                    Kontak Person / Sales <span class="text-rose-500">*</span>
                </label>
                <input id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="email">
                    Alamat Email
                </label>
                <input id="email" name="email" type="email" value="{{ old('email', $supplier->email ?? '') }}" 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="phone_number">
                    Nomor Telepon
                </label>
                <input id="phone_number" name="phone_number" value="{{ old('phone_number', $supplier->phone_number ?? '') }}" 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="business_type">
                    Tipe Bisnis <span class="text-rose-500">*</span>
                </label>
                <input id="business_type" name="business_type" value="{{ old('business_type', $supplier->business_type ?? 'Local') }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="country">
                    Negara Asal <span class="text-rose-500">*</span>
                </label>
                <input id="country" name="country" value="{{ old('country', $supplier->country ?? 'Indonesia') }}" required 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="status">
                    Status Kerjasama <span class="text-rose-500">*</span>
                </label>
                <select id="status" name="status" 
                        class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected(old('status', isset($supplier) ? $supplier->status->value : 'ACTIVE') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="border-t border-stone-100 pt-5">
        <h3 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-2">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[11px] text-amber-900 font-bold">3</span>
            Alamat Gudang / Kantor Supplier
        </h3>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="city">Kota</label>
                <input id="city" name="city" value="{{ old('city', $supplier->city ?? '') }}" 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="province">Provinsi</label>
                <input id="province" name="province" value="{{ old('province', $supplier->province ?? '') }}" 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="tax_identification_number">NPWP Perusahaan</label>
                <input id="tax_identification_number" name="tax_identification_number" value="{{ old('tax_identification_number', $supplier->tax_identification_number ?? '') }}" 
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="street_address">Alamat Lengkap</label>
                <textarea id="street_address" name="street_address" rows="2" 
                          class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('street_address', $supplier->street_address ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>
