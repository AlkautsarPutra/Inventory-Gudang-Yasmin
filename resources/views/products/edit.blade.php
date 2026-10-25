@extends('layouts.app', ['title' => 'Edit Product'])
@section('content')
    <div class="mb-8"><a href="{{ route('products.index') }}" class="text-sm font-medium text-cyan-700">← Products</a><h1 class="mt-2 text-3xl font-bold">Edit product</h1></div>
    <form method="POST" action="{{ route('products.update', $product) }}" class="max-w-4xl rounded-2xl border border-slate-200 bg-white p-6">@csrf @method('PUT') @include('products.form')<div class="mt-7 flex gap-3"><button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Save changes</button><a href="{{ route('products.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600">Cancel</a></div></form>
@endsection
