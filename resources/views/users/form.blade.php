<div class="space-y-5">
    <label class="block text-sm font-medium">Name<input name="name" value="{{ old('name', $user->name ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Email<input name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Role<select name="role" required class="mt-1 w-full rounded-lg border-slate-300">@foreach($roles as $role)<option value="{{ $role->value }}" @selected(old('role', $user->role->value ?? 'warehouse_staff') === $role->value)>{{ $role->label() }}</option>@endforeach</select></label>
    <label class="block text-sm font-medium">{{ isset($user) ? 'New password (leave blank to keep current password)' : 'Password' }}<input name="password" type="password" {{ isset($user) ? '' : 'required' }} class="mt-1 w-full rounded-lg border-slate-300"></label>
    <label class="block text-sm font-medium">Confirm password<input name="password_confirmation" type="password" {{ isset($user) ? '' : 'required' }} class="mt-1 w-full rounded-lg border-slate-300"></label>
</div>
