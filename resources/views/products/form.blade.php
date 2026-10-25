<div class="grid gap-5 md:grid-cols-2">
    <label class="block text-sm font-medium">Product code<input name="product_code" value="{{ old('product_code', $product->product_code ?? '') }}" required maxlength="50" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Product name<input name="product_name" value="{{ old('product_name', $product->product_name ?? '') }}" required maxlength="150" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Category<input name="category" value="{{ old('category', $product->category ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Brand<input name="brand" value="{{ old('brand', $product->brand ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Unit<input name="unit" value="{{ old('unit', $product->unit ?? '') }}" required maxlength="30" class="mt-1 w-full rounded-lg border-slate-300" placeholder="PCS"></label>
    <label class="block text-sm font-medium">Tipe Gudang<select name="warehouse_type" required class="mt-1 w-full rounded-lg border-slate-300">@foreach(\App\Enums\WarehouseType::options() as $value => $label)<option value="{{ $value }}" @selected(old('warehouse_type', isset($product) && $product->warehouse_type ? $product->warehouse_type->value : 'raw_material') === $value)>{{ $label }}</option>@endforeach</select></label>
    <label class="block text-sm font-medium">Status<select name="status" required class="mt-1 w-full rounded-lg border-slate-300">@foreach($statuses as $status)<option value="{{ $status->value }}" @selected(old('status', isset($product) ? $product->status->value : 'ACTIVE') === $status->value)>{{ $status->label() }}</option>@endforeach</select></label>
    <label class="block text-sm font-medium">Minimum stock<input name="minimum_stock" type="number" min="0" value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Maximum stock<input name="maximum_stock" type="number" min="0" value="{{ old('maximum_stock', $product->maximum_stock ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Reorder point<input name="reorder_point" type="number" min="0" value="{{ old('reorder_point', $product->reorder_point ?? 0) }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <div>
        <label for="lead_time_days" class="mb-1 block text-sm font-medium">Lead Time (Hari)</label>
        <input type="number" id="lead_time_days" name="lead_time_days" min="1" max="365"
               value="{{ old('lead_time_days', $product->lead_time_days ?? 7) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
        <p class="mt-1 text-xs text-slate-500">Rata-rata waktu pengiriman dari supplier/maklon</p>
    </div>
    <label class="block text-sm font-medium md:col-span-2">Description<textarea name="description" rows="4" class="mt-1 w-full rounded-lg border-slate-300">{{ old('description', $product->description ?? '') }}</textarea></label>
</div>
