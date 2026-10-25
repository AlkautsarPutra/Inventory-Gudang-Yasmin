<div class="space-y-4">
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="name">
            Nama Lengkap <span class="text-rose-500">*</span>
        </label>
        <input id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required 
               class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
               placeholder="Nama staf atau pengelola">
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="email">
            Alamat Email <span class="text-rose-500">*</span>
        </label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required 
               class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
               placeholder="email@yasmingoldencare.com">
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="role">
            Hak Akses (Role) <span class="text-rose-500">*</span>
        </label>
        <select id="role" name="role" required 
                class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            @foreach($roles as $role)
                <option value="{{ $role->value }}" @selected(old('role', $user->role->value ?? 'viewer') === $role->value)>
                    {{ $role->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="password">
            {{ isset($user) ? 'Kata Sandi Baru (Kosongkan jika tidak ingin mengubah)' : 'Kata Sandi' }} 
            @if(!isset($user)) <span class="text-rose-500">*</span> @endif
        </label>
        <input id="password" name="password" type="password" {{ isset($user) ? '' : 'required' }} 
               class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
               placeholder="Minimal 8 karakter">
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-1" for="password_confirmation">
            Konfirmasi Kata Sandi @if(!isset($user)) <span class="text-rose-500">*</span> @endif
        </label>
        <input id="password_confirmation" name="password_confirmation" type="password" {{ isset($user) ? '' : 'required' }} 
               class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
               placeholder="Ulangi kata sandi">
    </div>
</div>
