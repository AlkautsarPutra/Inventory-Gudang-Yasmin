<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · Inventory Intelligence</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-slate-950 p-5">
    <main class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
        <div class="mb-8"><span class="inline-grid h-10 w-10 place-items-center rounded-xl bg-cyan-400 font-bold">II</span><h1 class="mt-4 text-2xl font-bold">Welcome back</h1><p class="mt-1 text-sm text-slate-500">Sign in to Inventory Intelligence.</p></div>
        @if ($errors->any())<div class="mb-5 rounded-lg bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf
            <label class="block text-sm font-medium">Email<input name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"></label>
            <label class="block text-sm font-medium">Password<input name="password" type="password" required class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"></label>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-cyan-600">Remember me</label>
            <button class="w-full rounded-lg bg-slate-950 px-4 py-2.5 font-semibold text-white hover:bg-slate-800">Sign in</button>
        </form>

        <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-600">
            <p class="font-semibold text-slate-700 mb-1.5">Default / Demo Accounts:</p>
            <div class="space-y-1">
                <div><span class="font-medium text-slate-800">Admin:</span> admin@yasmingoldencare.com / Password123!</div>
                <div><span class="font-medium text-slate-800">Admin Alternatif:</span> admin@mail.com / admin123</div>
                <div><span class="font-medium text-slate-800">Viewer:</span> viewer@yasmingoldencare.com / password</div>
            </div>
        </div>
    </main>
</body>
</html>
