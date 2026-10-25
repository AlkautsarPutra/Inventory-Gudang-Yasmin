@extends('layouts.app', ['title' => 'Add Supplier'])
@section('content')
    <div class="mb-8"><a href="{{ route('suppliers.index') }}" class="text-sm font-medium text-cyan-700">← Suppliers</a><h1 class="mt-2 text-3xl font-bold">Add supplier</h1></div><form method="POST" action="{{ route('suppliers.store') }}" class="max-w-5xl rounded-2xl border border-slate-200 bg-white p-6">@csrf @include('suppliers.form')<div class="mt-7 flex gap-3"><button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Save supplier</button><a href="{{ route('suppliers.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600">Cancel</a></div></form>
@endsection
