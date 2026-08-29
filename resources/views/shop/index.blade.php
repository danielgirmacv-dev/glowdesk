@extends('layouts.app')

@section('title', 'Shop')

@section('content')
{{-- Pre-declare products for Alpine before components initialize --}}
<script>
    window.__glowProducts = @json($products);
</script>
<div x-data="shopManager()" x-init="init()">

    <!-- ===== HERO SLIDESHOW ===== -->
    @if($products->count() > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-6">
        <h2 class="text-3xl font-extrabold text-white flex items-center gap-3">
            <span class="flex items-center gap-2">
                Featured Products
                <span class="relative flex h-6 w-6 mt-1 -ml-1">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-6 w-6 bg-emerald-500 shadow-[0_0_15px_rgba(52,211,153,0.8)]"></span>
                </span>
            </span>
        </h2>
    </div>
    <div class="relative overflow-hidden min-h-[26rem] sm:h-96 py-8 sm:py-0 mb-0" x-data="slideshow()">
        <!-- Slides -->
        <template x-for="(product, index) in slides" :key="index">
            <div
                x-show="current === index"
                x-transition:enter="transition-opacity duration-700"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-700"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 flex items-center">


                <!-- Background product image (vibrant brighter blur) -->
                <template x-if="product.image_url">
                    <img :src="product.image_url"
                         class="absolute inset-0 w-full h-full object-cover opacity-70 blur-3xl scale-150 transform-gpu"
                         :alt="product.name">
                </template>

                <!-- Lightened gradient overlay & backdrop blur - now more transparent for theme blending -->
                <div class="absolute inset-0 bg-gradient-to-r from-black/20 via-transparent to-transparent backdrop-blur-[2px]"></div>

                <!-- Content -->
                <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col-reverse sm:flex-row items-center sm:justify-between gap-6 sm:gap-10 w-full">
                    <!-- Text -->
                    <div class="flex-1 text-center sm:text-left flex flex-col items-center sm:items-start pt-2 sm:pt-0">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white mb-1.5 leading-tight light:font-bold light:tracking-tight" x-text="product.name"></h2>
                        <p class="text-slate-400 text-xs max-w-sm line-clamp-2 mb-4 font-light light:text-slate-600" x-text="product.description"></p>
                        <div class="flex items-center gap-4">
                            <span class="text-xl font-black !text-white glow-gradient px-3 py-1 rounded-lg shadow-lg border border-white/20" x-text="'Br ' + parseFloat(product.price).toFixed(2)"></span>
                            <div class="flex gap-2">
                                <button @click="$store.cart.add(product)"
                                    class="text-white text-[11px] font-bold px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg flex items-center justify-center gap-1.5 transition-all active:scale-95 border"
                                    :class="$store.cart.items.some(i => i.id === product.id) ? 'bg-emerald-500/20 border-emerald-500/50 text-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.2)]' : 'glass border-white/20 hover:bg-white/10'">
                                    <template x-if="$store.cart.items.some(i => i.id === product.id)">
                                        <span><svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> In Cart</span>
                                    </template>
                                    <template x-if="!$store.cart.items.some(i => i.id === product.id)">
                                        <span><svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg> Cart</span>
                                    </template>
                                </button>
                                <button @click="openCheckout('single', product)"
                                    class="bg-[#7c3aed] hover:bg-[#6d28d9] shadow-[0_4px_15px_rgba(124,58,237,0.4)] text-white text-[11px] font-bold px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg flex items-center justify-center gap-1.5 transition-all active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Order Now
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product image on mobile top, desktop right -->
                    <template x-if="product.image_url">
                        <div class="flex-shrink-0 w-48 h-36 sm:w-64 sm:h-48 rounded-2xl overflow-hidden shadow-2xl transition-all duration-500 hover:scale-105" style="box-shadow: 0 0 60px rgba(217,70,239,0.2);">
                            <img :src="product.image_url" :alt="product.name" class="object-cover w-full h-full">
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Dot indicators -->
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
            <template x-for="(_, i) in slides" :key="i">
                <button @click="current = i; resetTimer()"
                    class="h-1.5 rounded-full transition-all duration-300"
                    :class="current === i ? 'w-6 bg-glow-400' : 'w-1.5 bg-white/20 hover:bg-white/40'"></button>
            </template>
        </div>

        <!-- Prev/Next arrows -->
        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 glass hover:bg-white/10 rounded-xl flex items-center justify-center text-slate-400 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 glass hover:bg-white/10 rounded-xl flex items-center justify-center text-slate-400 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
    @endif

    <!-- ===== MAIN CONTENT ===== -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16">

        <!-- Brand Hero Text -->
        <div class="text-center mb-10">
            <h1 class="text-5xl sm:text-7xl font-extrabold tracking-tight leading-tight mb-4">
                <span class="glow-text">GlowDesk</span><br>
                <span class="text-white text-4xl sm:text-5xl font-semibold">Beauty delivered to your desk</span>
            </h1>
            <p class="text-slate-400 text-base max-w-xl mx-auto leading-relaxed">
                Discover and order premium cosmetics and beauty products — effortlessly.
            </p>
        </div>

        <!-- ===== Visual Category Filters ===== -->
        <div class="flex gap-2 sm:gap-3 overflow-x-auto hidden-scrollbar mb-6 px-1">
            <template x-for="category in categories" :key="category.name">
                <button @click="activeCategory = category.name"
                        class="flex-shrink-0 px-4 py-2 md:py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all duration-300"
                        :class="activeCategory === category.name ? 'bg-glow-500/20 text-glow-300 border border-glow-500/40 shadow-[0_0_15px_rgba(217,70,239,0.2)] scale-[1.02]' : 'glass border border-white/5 text-slate-400 hover:text-white hover:bg-white/5 hover:border-white/10'">
                    
                    <span class="text-sm md:text-base leading-none transition-transform duration-300"
                         :class="activeCategory === category.name ? 'scale-110' : ''" x-text="category.icon"></span>
                    
                    <span class="text-[11px] md:text-sm font-semibold whitespace-nowrap" x-text="category.name"></span>
                </button>
            </template>
        </div>

        <!-- ===== Search + Filter Bar ===== -->
        <div class="glass-dark rounded-2xl p-3 mb-8 flex flex-col sm:flex-row gap-3 items-stretch sm:items-center" style="border: 1px solid rgba(255,255,255,0.08);">
            <!-- Search -->
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="Search products…" class="input-field w-full rounded-xl py-2.5 pl-10 pr-4 text-sm">
            </div>

            <!-- Sort buttons (fix: no select dropdown, use buttons instead) -->
            <div class="flex items-center gap-1.5 flex-shrink-0">
                <button @click="sortBy='default'"
                    class="px-3 py-2 rounded-xl text-xs font-semibold transition-all"
                    :class="sortBy==='default' ? 'btn-glow text-white' : 'glass text-slate-400 hover:text-white border border-white/10'">
                    Default
                </button>
                <button @click="sortBy='price_asc'"
                    class="px-3 py-2 rounded-xl text-xs font-semibold transition-all"
                    :class="sortBy==='price_asc' ? 'btn-glow text-white' : 'glass text-slate-400 hover:text-white border border-white/10'">
                    Br Low→High
                </button>
                <button @click="sortBy='price_desc'"
                    class="px-3 py-2 rounded-xl text-xs font-semibold transition-all"
                    :class="sortBy==='price_desc' ? 'btn-glow text-white' : 'glass text-slate-400 hover:text-white border border-white/10'">
                    Br High→Low
                </button>
                <button @click="sortBy='name_asc'"
                    class="px-3 py-2 rounded-xl text-xs font-semibold transition-all"
                    :class="sortBy==='name_asc' ? 'btn-glow text-white' : 'glass text-slate-400 hover:text-white border border-white/10'">
                    A→Z
                </button>
            </div>

            <span class="text-slate-600 text-xs whitespace-nowrap pl-1">
                <span x-text="filteredProducts.length"></span> items
            </span>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
            <template x-for="product in paginatedProducts" :key="product.id">
                <div class="glass rounded-2xl overflow-hidden card-hover flex flex-col group" style="border: 1px solid rgba(255,255,255,0.08);">
                    <div class="relative h-44 overflow-hidden bg-gradient-to-br from-glow-900/40 to-slate-900 cursor-pointer" @click="openModal(product)">
                        <template x-if="product.image_url">
                            <img :src="product.image_url" :alt="product.name" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-700 opacity-80">
                        </template>
                        <template x-if="!product.image_url">
                            <div class="w-full h-full flex items-center justify-center">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(217,70,239,0.3), rgba(124,58,237,0.3))">
                                    <svg class="w-8 h-8 text-glow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                            </div>
                        </template>
                        <div class="absolute top-3 left-3 pb-1">
                            <template x-if="product.is_active">
                                <span class="bg-emerald-500/90 text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide backdrop-blur-sm shadow-lg">In Stock</span>
                            </template>
                            <template x-if="!product.is_active">
                                <span class="bg-red-500/90 text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide backdrop-blur-sm shadow-lg">Out of Stock</span>
                            </template>
                        </div>
                        <div class="absolute top-3 right-3">
                            <span class="glow-gradient !text-white text-[10px] font-black px-2 py-0.5 rounded-md shadow-lg border border-white/10" x-text="'Br ' + parseFloat(product.price).toFixed(2)"></span>
                        </div>
                    </div>
                    <div class="p-3 flex flex-col flex-grow">
                        <h3 class="font-semibold text-sm text-white leading-tight mb-1 line-clamp-2" x-text="product.name"></h3>
                        <p class="text-slate-500 text-[11px] leading-relaxed flex-grow line-clamp-2 mb-3" x-text="product.description"></p>
                        <template x-if="product.is_active">
                            <div class="flex gap-1.5">
                                <button @click="$store.cart.add(product)" title="Add to Cart"
                                    class="flex-1 font-bold py-1.5 rounded-lg flex items-center justify-center gap-1 transition-all duration-300 text-xs relative overflow-visible"
                                    :class="$store.cart.items.some(i => i.id === product.id) ? 'bg-emerald-500/20 border border-emerald-500/50 text-emerald-500' : 'bg-slate-200 hover:bg-slate-300 text-slate-900 border border-slate-300'">
                                    
                                    <template x-if="$store.cart.items.some(i => i.id === product.id)">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <template x-if="$store.cart.items.find(i => i.id === product.id).quantity > 1">
                                                <span x-text="$store.cart.items.find(i => i.id === product.id).quantity" class="absolute -top-2 -right-2 bg-red-500 text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center shadow-lg border border-[#0f0f14]"></span>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <template x-if="!$store.cart.items.some(i => i.id === product.id)">
                                        <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </template>
                                </button>
                                <button @click="openCheckout('single', product)"
                                    class="flex-[2] bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-xs font-semibold py-1.5 rounded-lg flex items-center justify-center transition-all duration-300">
                                    Order Now
                                </button>
                            </div>
                        </template>
                        <template x-if="!product.is_active">
                            <button disabled class="mt-2 w-full text-slate-500 text-xs font-semibold py-1.5 rounded-lg flex items-center justify-center cursor-not-allowed border border-red-500/10 bg-red-500/5">
                                Out of Stock
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <div x-show="filteredProducts.length === 0" class="col-span-full py-24 text-center">
                <p class="text-slate-500 text-sm">No products match your search.</p>
                <button @click="search=''" class="mt-3 text-glow-400 text-xs hover:text-glow-300 transition">Clear search</button>
            </div>
        </div>

        <!-- Pagination -->
        <div x-show="totalPages > 1" class="flex items-center justify-center gap-2 mt-10">
            <button @click="currentPage > 1 && currentPage--" :disabled="currentPage===1"
                class="w-9 h-9 rounded-xl glass border border-white/10 flex items-center justify-center text-slate-400 hover:text-white transition disabled:opacity-30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <template x-for="page in totalPages" :key="page">
                <button @click="currentPage=page"
                    class="w-9 h-9 rounded-xl text-sm font-semibold transition-all"
                    :class="currentPage===page ? 'btn-glow text-white' : 'glass border border-white/10 text-slate-400 hover:text-white'">
                    <span x-text="page"></span>
                </button>
            </template>
            <button @click="currentPage < totalPages && currentPage++" :disabled="currentPage===totalPages"
                class="w-9 h-9 rounded-xl glass border border-white/10 flex items-center justify-center text-slate-400 hover:text-white transition disabled:opacity-30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    <!-- Product Details Modal -->
    <div x-show="showModal" style="display:none;" class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6 overflow-hidden">
        <!-- Backdrop -->
        <div x-show="showModal"
             x-transition:enter="ease-out duration-500" x-transition:enter-start="opacity-0 backdrop-blur-0" x-transition:enter-end="opacity-100 backdrop-blur-xl"
             x-transition:leave="ease-in duration-300" x-transition:leave-start="opacity-100 backdrop-blur-xl" x-transition:leave-end="opacity-0 backdrop-blur-0"
             @click="closeModal()" class="absolute inset-0 bg-black/80"></div>

        <!-- Modal Content -->
        <div x-show="showModal"
             x-transition:enter="ease-out duration-500" x-transition:enter-start="opacity-0 scale-90 translate-y-12 rotate-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0 rotate-0"
             x-transition:leave="ease-in duration-300" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-8"
             class="relative w-full max-w-sm sm:max-w-md glass-dark rounded-[2.5rem] overflow-hidden shadow-[0_32px_128px_rgba(0,0,0,0.8)] flex flex-col max-h-[90vh] border border-white/10"
             style="box-shadow: 0 0 0 1px rgba(255,255,255,0.05), 0 25px 100px rgba(0,0,0,0.5);">
             
             <!-- Close Button -->
             <button @click="closeModal()" class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center rounded-2xl bg-white/5 hover:bg-white/10 text-white/70 hover:text-white transition-all z-[201] backdrop-blur-xl border border-white/10 group">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
             </button>

            <div class="p-6 sm:p-8 overflow-y-auto w-full flex flex-col custom-scrollbar" style="max-height: 85vh;">
                <!-- Product Image Area -->
                <div class="rounded-3xl overflow-hidden mb-6 relative bg-gradient-to-br from-glow-500/10 via-black/40 to-glow-900/10 border border-white/10 flex items-center justify-center w-full min-h-[22vh] group shadow-2xl animate-float">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(217,70,239,0.15)_0%,transparent_70%)]"></div>
                    <template x-if="selectedProduct?.image_url">
                        <img :src="selectedProduct.image_url" class="max-w-[75%] h-auto max-h-[18vh] object-contain drop-shadow-[0_20px_50px_rgba(217,70,239,0.25)] transition-transform duration-700 group-hover:scale-110">
                    </template>
                </div>

                <!-- Labels/Badges row -->
                <div class="flex items-center gap-2 mb-4">
                    <template x-if="selectedProduct?.is_active">
                        <span class="flex items-center gap-1.5 text-[10px] uppercase tracking-widest font-black px-3 py-1.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse-soft"></span>
                            In Stock
                        </span>
                    </template>
                    <span class="text-[10px] uppercase tracking-widest font-black px-3 py-1.5 rounded-full bg-white/5 text-slate-400 border border-white/10" x-text="selectedProduct?.category?.name || 'Beauty'"></span>
                </div>

                <h2 class="text-2xl sm:text-3xl font-serif italic text-white mb-2 tracking-tight" x-text="selectedProduct?.name"></h2>

                <div class="flex items-center gap-4 mb-3">
                    <div class="px-4 py-2 rounded-2xl bg-gradient-to-r from-glow-600 to-glow-500 shadow-lg shadow-glow-500/20 border border-white/10">
                        <span class="text-white font-black text-lg" x-text="'Br ' + parseFloat(selectedProduct?.price ?? 0).toFixed(2)"></span>
                    </div>
                </div>

                <div class="glass-dark rounded-3xl p-5 border border-white/10 bg-white/5 shadow-inner flex-grow mb-6">
                    <h4 class="text-[10px] font-bold text-glow-300 uppercase tracking-[0.2em] mb-3 opacity-70">Description</h4>
                    <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-wrap font-medium" x-text="selectedProduct?.description || 'No description available for this product.'"></p>
                </div>

                <div class="flex gap-3">
                    <template x-if="selectedProduct?.is_active">
                        <div class="flex-1">
                             <button @click="$store.cart.add(selectedProduct)"
                                 class="w-full py-3.5 rounded-2xl font-black flex items-center justify-center gap-2 transition-all duration-500 text-xs relative overflow-visible uppercase tracking-widest group"
                                 :class="$store.cart.items.some(i => i.id === selectedProduct.id) ? 'bg-emerald-500/20 border-2 border-emerald-500/50 text-emerald-400 shadow-[0_0_20px_rgba(16,185,129,0.2)]' : 'bg-white text-black hover:bg-glow-100 border-2 border-white shadow-[0_4px_15px_rgba(255,255,255,0.2)]'">
                                 
                                 <template x-if="$store.cart.items.some(i => i.id === selectedProduct.id)">
                                     <span class="flex items-center gap-2">
                                         <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                         <span>In Cart</span>
                                         <template x-if="$store.cart.items.find(i => i.id === selectedProduct.id).quantity > 1">
                                             <span x-text="$store.cart.items.find(i => i.id === selectedProduct.id).quantity" class="absolute -top-3 -right-3 bg-red-500 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center shadow-2xl border-2 border-[#0f0f14] font-black"></span>
                                         </template>
                                     </span>
                                 </template>
                                 
                                 <template x-if="!$store.cart.items.some(i => i.id === selectedProduct.id)">
                                     <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg> 
                                        Add to Cart
                                     </span>
                                 </template>
                             </button>
                        </div>
                    </template>
                    <template x-if="selectedProduct?.is_active">
                        <button @click="closeModal(); openCheckout('single', selectedProduct)"
                                class="flex-[1.2] bg-gradient-to-r from-glow-600 to-glow-500 hover:brightness-110 text-white text-xs font-black py-3.5 rounded-2xl flex items-center justify-center uppercase tracking-widest transition-all duration-300 shadow-[0_4px_20px_rgba(217,70,239,0.3)] border border-white/20 active:scale-95">
                            Order Now
                        </button>
                    </template>
                    <template x-if="!selectedProduct?.is_active">
                        <button disabled class="w-full text-slate-500 text-xs font-black py-3.5 rounded-2xl flex items-center justify-center cursor-not-allowed border border-red-500/10 bg-red-500/5 uppercase tracking-widest opacity-50">Out of Stock</button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Checkout Modal (Dynamic Form) -->
    <div x-show="showCheckoutModal" style="display:none;" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
        <div x-show="showCheckoutModal" @click="showCheckoutModal = false"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-black/80 backdrop-blur-md"></div>

        <div x-show="showCheckoutModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-lg glass-dark rounded-3xl overflow-hidden shadow-2xl flex flex-col z-10" style="border: 1px solid rgba(124,58,237,0.3);">
             
             <div class="p-6 md:p-8 relative">
                 <button type="button" @click="showCheckoutModal = false" class="absolute top-4 right-4 md:top-6 md:right-6 w-8 h-8 flex items-center justify-center rounded-xl glass hover:bg-white/10 text-slate-400 hover:text-white transition border border-white/5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                 
                 <div class="mb-6 pr-10">
                     <h3 class="font-extrabold text-white text-2xl mb-1">Secure Checkout</h3>
                     <template x-if="checkoutMode === 'cart'">
                         <p class="text-slate-400 text-sm">You are checking out <strong class="text-[#f0abfc] font-semibold" x-text="$store.cart.count + ' items'"></strong> for <strong class="text-white" x-text="'Br ' + $store.cart.total.toFixed(2)"></strong>.</p>
                     </template>
                     <template x-if="checkoutMode === 'single'">
                         <p class="text-slate-400 text-sm">You are ordering <strong class="text-[#f0abfc] font-semibold" x-text="checkoutProduct?.name"></strong>.</p>
                     </template>
                 </div>

                 <form action="{{ route('orders.store') }}" method="POST" class="space-y-4">
                     @csrf
                     
                     <!-- Dynamic payload logic -->
                     <template x-if="checkoutMode === 'cart'">
                         <input type="hidden" name="cart_items" :value="JSON.stringify($store.cart.items)">
                     </template>
                     <template x-if="checkoutMode === 'single'">
                         <div class="grid grid-cols-[1fr,100px] gap-4">
                             <input type="hidden" name="product_id" :value="checkoutProduct?.id">
                             <div class="col-span-1 hidden"></div>
                             
                             <div class="col-start-2 place-self-end mt-[-3rem] z-20">
                                 <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1 text-right">Qty</label>
                                 <input type="number" name="quantity" value="1" min="1" required class="input-field w-full rounded-xl py-2 px-3 text-center text-sm bg-black/40 font-bold border-white/20">
                             </div>
                         </div>
                     </template>
                     
                     <div>
                         <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Full Name</label>
                         <input type="text" name="customer_name" required placeholder="Full Name" class="input-field w-full rounded-xl py-2.5 px-4 text-sm bg-black/20">
                     </div>
                     
                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                         <div>
                             <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Department</label>
                             <input type="text" name="department" placeholder="e.g. Sales, IT" class="input-field w-full rounded-xl py-2.5 px-4 text-sm bg-black/20">
                         </div>
                         <div>
                             <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Phone</label>
                             <input type="text" name="phone" required placeholder="+251..." class="input-field w-full rounded-xl py-2.5 px-4 text-sm bg-black/20">
                         </div>
                     </div>
                     
                     <div>
                         <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center justify-between mb-1.5">
                             Telegram Username <span class="text-[9px] text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-1.5 py-0.5 rounded tracking-normal normal-case ml-2 whitespace-nowrap">+ Updates</span>
                         </label>
                         <input type="text" name="telegram_username" placeholder="@username" class="input-field w-full rounded-xl py-2.5 px-4 text-sm bg-black/20">
                     </div>

                     <div class="pt-4 flex gap-3">
                         <button type="submit" class="w-full relative group overflow-hidden rounded-xl bg-[#7c3aed] text-white py-3.5 font-bold transition hover:bg-[#6d28d9] shadow-[0_0_20px_rgba(124,58,237,0.3)]">
                             <span class="relative z-10 flex items-center justify-center gap-2">
                                 Complete Order
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                             </span>
                         </button>
                     </div>
                 </form>
             </div>
        </div>
    </div>

    <!-- Floating Action Button for Custom Request -->
    <button @click="showContactModal = true; document.body.style.overflow = 'hidden';" class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full btn-glow flex items-center justify-center text-white shadow-[0_10px_40px_rgba(217,70,239,0.5)] hover:scale-110 transition-transform">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    </button>

    <!-- Custom Request Modal -->
    <div x-show="showContactModal" style="display:none;" class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6"
         @open-custom-request.window="showContactModal = true; document.body.style.overflow = 'hidden';">
        <div x-show="showContactModal"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showContactModal = false; document.body.style.overflow = '';" class="absolute inset-0 bg-black/70 backdrop-blur-md"></div>

        <div x-show="showContactModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-lg glass-dark rounded-3xl overflow-hidden shadow-2xl flex flex-col"
             style="border: 1px solid rgba(217,70,239,0.2); box-shadow: 0 25px 100px rgba(217,70,239,0.15);">
             
             <div class="p-6 md:p-8 relative">
                 <button @click="showContactModal = false; document.body.style.overflow = '';" class="absolute top-4 right-4 text-white/50 hover:text-white transition bg-black/20 hover:bg-black/40 rounded-full p-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                 
                 <div class="flex items-center gap-3 mb-2">
                     <div class="w-10 h-10 rounded-xl glow-gradient flex items-center justify-center">
                         <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                     </div>
                     <h2 class="text-2xl font-bold text-white">Custom Request</h2>
                 </div>
                 <p class="text-sm text-slate-400 mb-6 flex-1">Can't find what you're looking for? Let us know!</p>

                 <form action="{{ route('custom-request.store') }}" method="POST" class="space-y-4">
                     @csrf
                     
                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                         <div>
                             <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Your Name</label>
                             <input type="text" name="customer_name" required placeholder="Full Name" class="input-field w-full rounded-xl py-2.5 px-4 text-sm bg-black/20">
                         </div>
                         <div>
                             <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Phone Number</label>
                             <input type="text" name="phone" required placeholder="+251..." class="input-field w-full rounded-xl py-2.5 px-4 text-sm bg-black/20">
                         </div>
                     </div>

                     <div>
                         <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center justify-between mb-1.5">
                             Telegram Username <span class="text-[9px] text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-1.5 py-0.5 rounded tracking-normal normal-case ml-2 whitespace-nowrap">+ Updates</span>
                         </label>
                         <input type="text" name="telegram_username" placeholder="@username" class="input-field w-full rounded-xl py-2.5 px-4 text-sm bg-black/20">
                     </div>

                     <div>
                         <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">What are you looking for?</label>
                         <textarea name="request_message" required rows="3" placeholder="Describe the item, brand, or product you need..." class="input-field w-full rounded-xl py-2.5 px-4 text-sm bg-black/20 resize-none"></textarea>
                     </div>

                     <div class="pt-2">
                         <button type="submit" class="w-full relative group overflow-hidden rounded-xl bg-[#d946ef] text-white py-3.5 font-bold transition hover:bg-[#c026d3] shadow-[0_0_20px_rgba(217,70,239,0.3)]">
                             <span class="relative z-10 flex items-center justify-center gap-2">
                                 Send Request securely
                                 <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                             </span>
                         </button>
                     </div>
                 </form>
             </div>
        </div>
    </div>

</div>

<script>
/* ===== Slideshow component ===== */
function slideshow() {
    return {
        slides: (window.__glowProducts || []).slice(0, 6), // show up to 6 in slideshow
        current: 0,
        timer: null,
        next() { this.current = (this.current + 1) % this.slides.length; this.resetTimer(); },
        prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length; this.resetTimer(); },
        resetTimer() {
            clearInterval(this.timer);
            this.timer = setInterval(() => this.next(), 4000);
        },
        openModal(product) {
            // Bubble up to parent shopManager
            this.$dispatch('open-modal', product);
        },
        init() { this.timer = setInterval(() => this.next(), 4000); }
    }
}

/* ===== Shop manager component ===== */
function shopManager() {
    const allProducts = window.__glowProducts || [];
    const perPage = 20;

    return {
        products: allProducts,
        search: '',
        sortBy: 'default',
        currentPage: 1,
        showModal: false,
        showCheckoutModal: false,
        checkoutMode: 'cart', // 'cart' or 'single'
        checkoutProduct: null,
        showContactModal: false,
        selectedProduct: null,
        
        categories: [
            { name: 'All', icon: '✨' },
            { name: 'Moisturizer', icon: '🧴' },
            { name: 'Cleanser', icon: '🧼' },
            { name: 'Exfoliant', icon: '🫧' },
            { name: 'Sunscreen', icon: '☀️' }
        ],
        activeCategory: 'All',

        get filteredProducts() {
            let list = [...this.products];
            
            if (this.activeCategory !== 'All') {
                const cat = this.activeCategory.toLowerCase();
                list = list.filter(p => 
                    p.name.toLowerCase().includes(cat) || 
                    (p.description && p.description.toLowerCase().includes(cat))
                );
            }

            const q = this.search.toLowerCase().trim();
            if (q) list = list.filter(p => p.name.toLowerCase().includes(q) || (p.description && p.description.toLowerCase().includes(q)));
            if (this.sortBy === 'price_asc')  list.sort((a, b) => a.price - b.price);
            if (this.sortBy === 'price_desc') list.sort((a, b) => b.price - a.price);
            if (this.sortBy === 'name_asc')   list.sort((a, b) => a.name.localeCompare(b.name));
            return list;
        },

        get totalPages() { return Math.max(1, Math.ceil(this.filteredProducts.length / perPage)); },

        get paginatedProducts() {
            const start = (this.currentPage - 1) * perPage;
            return this.filteredProducts.slice(start, start + perPage);
        },

        openModal(product) {
            this.selectedProduct = product;
            this.showModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.showModal = false;
            document.body.style.overflow = '';
        },

        openCheckout(mode = 'cart', product = null) {
            this.checkoutMode = mode;
            this.checkoutProduct = product;
            this.showCheckoutModal = true;
            document.body.style.overflow = 'hidden';
        },

        init() {
            this.$watch('search', () => { this.currentPage = 1; });
            this.$watch('sortBy', () => { this.currentPage = 1; });
            this.$watch('activeCategory', () => { this.currentPage = 1; });
            // Listen for slideshow open-modal event
            window.addEventListener('open-modal', e => {
                this.selectedProduct = e.detail;
                this.showModal = true;
                document.body.style.overflow = 'hidden';
            });
            window.addEventListener('open-cart-checkout', e => {
                this.openCheckout('cart');
            });
            this.$watch('showCheckoutModal', val => {
                if(!val && !this.showModal && !this.showContactModal) document.body.style.overflow = '';
            });
        }
    }
}
</script>
@endsection
