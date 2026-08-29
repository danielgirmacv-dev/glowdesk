@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Header -->
    <div class="mb-10">
        <a href="{{ route('admin.products') }}" class="inline-flex items-center gap-1.5 text-slate-500 hover:text-white text-sm transition mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Products
        </a>
        <p class="text-glow-400 text-xs font-semibold uppercase tracking-widest mb-1">Admin Panel</p>
        <h1 class="text-3xl font-extrabold text-white">Add New Product</h1>
        <p class="text-slate-500 text-sm mt-1">Fill in the details to create a new store listing.</p>
    </div>

    <!-- Form Card -->
    <div class="glass-dark rounded-2xl p-8" style="border: 1px solid rgba(217,70,239,0.15); box-shadow: 0 20px 60px rgba(217,70,239,0.05);">

        @if($errors->any())
        <div class="mb-6 glass border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm space-y-1">
            @foreach($errors->all() as $error)
                <p class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    {{ $error }}
                </p>
            @endforeach
        </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Product Name <span class="text-glow-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. USB-C Hub Pro"
                    class="input-field w-full rounded-xl py-3 px-4 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Description <span class="text-glow-500">*</span></label>
                <textarea name="description" rows="3" required placeholder="Brief description of the product…"
                    class="input-field w-full rounded-xl py-3 px-4 text-sm resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Price (Birr) <span class="text-glow-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Br</span>
                        <input type="number" name="price" step="0.01" min="0" required value="{{ old('price') }}" placeholder="0.00"
                            class="input-field w-full rounded-xl py-3 pl-10 pr-4 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Status</label>
                    <select name="is_active" class="input-field w-full rounded-xl py-3 px-4 text-sm appearance-none">
                        <option value="1" class="bg-slate-900">Active</option>
                        <option value="0" class="bg-slate-900">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Upload File <span class="text-slate-600">(browse)</span></label>
                    <input type="file" name="image" accept="image/*"
                        class="input-field w-full rounded-xl py-2.5 px-4 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-glow-500/20 file:text-glow-400 hover:file:bg-glow-500/30">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">OR Image URL <span class="text-slate-600">(link)</span></label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://..."
                        class="input-field w-full rounded-xl py-3 px-4 text-sm">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.products') }}" class="flex-1 glass text-slate-400 hover:text-white text-sm font-semibold py-3 rounded-xl text-center transition-all border border-white/10 hover:border-white/20">
                    Cancel
                </a>
                <button type="submit" class="flex-1 btn-glow text-white text-sm font-bold py-3 rounded-xl flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
