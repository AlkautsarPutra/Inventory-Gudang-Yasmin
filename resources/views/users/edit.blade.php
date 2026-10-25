@extends('layouts.app', ['title' => 'Edit User'])
@section('content')
    <div class="mb-8"><a href="{{ route('users.index') }}" class="text-sm font-medium text-cyan-700">← Users</a><h1 class="mt-2 text-3xl font-bold">Edit user</h1></div><form method="POST" action="{{ route('users.update', $user) }}" class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6">@csrf @method('PUT') @include('users.form')<div class="mt-7 flex gap-3"><button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Save changes</button><a href="{{ route('users.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600">Cancel</a></div></form>
@endsection
