@extends('layouts.app', ['title' => 'Kelola Pengguna'])

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">Hak Akses & Otoritas</span>
            </div>
            <h1 class="mt-1 font-serif text-3xl font-bold tracking-tight text-stone-900">
                Kelola Akun Pengguna
            </h1>
            <p class="text-xs text-stone-500 mt-1">Daftar akun staf gudang, pengelola operasional, dan viewer.</p>
        </div>

        @can('manage-users')
            <a href="{{ route('users.create') }}" 
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-sm font-bold text-stone-950 shadow-md shadow-amber-500/20 hover:from-amber-400 hover:to-amber-500 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Pengguna Baru</span>
            </a>
        @endcan
    </div>

    <!-- Users Table Card -->
    <div class="overflow-hidden rounded-3xl border border-stone-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-stone-100 bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-4 px-6">Nama Pengguna</th>
                        <th class="py-4 px-6">Alamat Email</th>
                        <th class="py-4 px-6">Peran / Otoritas</th>
                        <th class="py-4 px-6 text-center">Status Verifikasi</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-tr from-stone-900 to-stone-700 text-xs font-bold text-amber-300">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="font-semibold text-stone-900">{{ $user->name }}</div>
                                </div>
                            </td>

                            <td class="py-4 px-6 text-xs text-stone-600 font-mono">
                                {{ $user->email }}
                            </td>

                            <td class="py-4 px-6">
                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold {{ $user->role === \App\Enums\UserRole::ADMIN ? 'bg-amber-100 text-amber-900' : 'bg-stone-100 text-stone-700' }}">
                                    {{ $user->role->label() }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-center">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="text-xs text-stone-400">Belum</span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('users.edit', $user) }}" 
                                   class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-900 transition">
                                    <span>Ubah</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-sm text-stone-500">
                                Belum ada data pengguna yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-stone-100 bg-stone-50/50 px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
