<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen lg:grid lg:grid-cols-[16rem_1fr]">
        <aside class="bg-slate-950 px-5 py-6 text-slate-200">
            <a href="{{ route('dashboard') }}" class="mb-8 flex items-center gap-3 text-white">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-cyan-400 font-bold text-slate-950">II</span>
                <span class="font-semibold leading-tight">Inventory<br>Intelligence</span>
            </a>
            <nav class="space-y-1 text-sm">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'hover:bg-slate-900' }} block rounded-lg px-3 py-2">Dashboard</a>
                @can('manage-master-data')
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-900' }} block rounded-lg px-3 py-2">Products</a>
                    <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-900' }} block rounded-lg px-3 py-2">Suppliers</a>
                @endcan
                <a href="{{ route('inventory-transactions.index') }}" class="{{ request()->routeIs('inventory-transactions.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-900' }} block rounded-lg px-3 py-2">Inventory Transactions</a>
                @can('access-forecasting')
                    <a href="{{ route('forecasts.index') }}" class="{{ request()->routeIs('forecasts.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-900' }} block rounded-lg px-3 py-2">Peramalan</a>
                @endcan
                @php
                    $unacknowledgedAlertsCount = \App\Models\StockAlert::where('is_acknowledged', false)->count();
                @endphp
                <a href="{{ route('alerts.index') }}" class="{{ request()->routeIs('alerts.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-900' }} flex items-center justify-between rounded-lg px-3 py-2">
                    <span>Peringatan Stok</span>
                    @if ($unacknowledgedAlertsCount > 0)
                        <span class="rounded-full bg-rose-500 px-2 py-0.5 text-xs font-bold text-white">{{ $unacknowledgedAlertsCount }}</span>
                    @endif
                </a>
                @can('manage-users')
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-900' }} block rounded-lg px-3 py-2">Users</a>
                @endcan
            </nav>
            <div class="mt-10 border-t border-slate-800 pt-4 text-sm">
                <p class="font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="mb-3 text-xs text-slate-400">{{ auth()->user()->role->label() }}</p>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="text-slate-300 hover:text-white">Sign out</button>
                </form>
            </div>
        </aside>
        <main class="min-w-0 p-5 sm:p-8">
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <p class="font-semibold">Please fix the following:</p>
                    <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
