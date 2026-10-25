<div class="grid gap-5 md:grid-cols-2">
    <label class="block text-sm font-medium">Supplier code<input name="supplier_code" value="{{ old('supplier_code', $supplier->supplier_code ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Supplier name<input name="supplier_name" value="{{ old('supplier_name', $supplier->supplier_name ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Company name<input name="company_name" value="{{ old('company_name', $supplier->company_name ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Contact person<input name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Email<input name="email" type="email" value="{{ old('email', $supplier->email ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Phone number<input name="phone_number" value="{{ old('phone_number', $supplier->phone_number ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Mobile number<input name="mobile_number" value="{{ old('mobile_number', $supplier->mobile_number ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Website<input name="website" type="url" value="{{ old('website', $supplier->website ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Supplier category<input name="supplier_category" value="{{ old('supplier_category', $supplier->supplier_category ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Business type<input name="business_type" value="{{ old('business_type', $supplier->business_type ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Country<input name="country" value="{{ old('country', $supplier->country ?? 'Indonesia') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Status<select name="status" class="mt-1 w-full rounded-lg border-slate-300">@foreach($statuses as $status)<option value="{{ $status->value }}" @selected(old('status', isset($supplier) ? $supplier->status->value : 'ACTIVE') === $status->value)>{{ $status->label() }}</option>@endforeach</select></label>
    <label class="block text-sm font-medium">City<input name="city" value="{{ old('city', $supplier->city ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Province<input name="province" value="{{ old('province', $supplier->province ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Postal code<input name="postal_code" value="{{ old('postal_code', $supplier->postal_code ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Tax ID<input name="tax_identification_number" value="{{ old('tax_identification_number', $supplier->tax_identification_number ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium md:col-span-2">Street address<textarea name="street_address" rows="3" class="mt-1 w-full rounded-lg border-slate-300">{{ old('street_address', $supplier->street_address ?? '') }}</textarea></label>
</div>
