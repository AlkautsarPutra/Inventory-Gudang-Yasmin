@extends('layouts.app', ['title' => 'Add User'])
@section('content')
    <div class="mb-8"><a href="{{ route('users.index') }}" class="text-sm font-medium text-cyan-700">← Users</a><h1 class="mt-2 text-3xl font-bold">Add user</h1></div><form method="POST" action="{{ route('users.store') }}" class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6">@csrf @include('users.form')<div class="mt-7 flex gap-3"><button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Save user</button><a href="{{ route('users.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600">Cancel</a></div></form>
@endsection
