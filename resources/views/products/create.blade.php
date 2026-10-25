@extends('layouts.app', ['title' => 'Add Product'])
@section('content')
    <div class="mb-8"><a href="{{ route('products.index') }}" class="text-sm font-medium text-cyan-700">← Products</a><h1 class="mt-2 text-3xl font-bold">Add product</h1></div>
    <form method="POST" action="{{ route('products.store') }}" class="max-w-4xl rounded-2xl border border-slate-200 bg-white p-6">@csrf @include('products.form')<div class="mt-7 flex gap-3"><button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Save product</button><a href="{{ route('products.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600">Cancel</a></div></form>
@endsection
