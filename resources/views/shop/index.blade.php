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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 mb-5">
        <div class="flex flex-col">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Featured Products
            </h2>
            <div class="section-heading-accent"></div>
        </div>
    </div>
    <div class="relative overflow-hidden min-h-[26rem] sm:h-96 py-8 sm:py-0 mb-0 max-w-7xl mx-auto rounded-3xl hero-slideshow shadow-lg" x-data="slideshow()">
        <!-- Slides -->
        <template x-for="(product, index) in slides" :key="index">
            <div
                x-show="current === index"
                x-transition:enter="transition-opacity duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 flex items-center">

                <!-- Content -->
                <div class="relative z-10 max-w-7xl mx-auto px-6 sm:px-10 flex flex-col-reverse sm:flex-row items-center sm:justify-between gap-6 sm:gap-10 w-full">
                    <!-- Text -->
                    <div class="flex-1 text-center sm:text-left flex flex-col items-center sm:items-start pt-2 sm:pt-0">
                        <span class="text-[11px] font-bold text-purple-300 uppercase tracking-[0.15em] mb-2 px-2.5 py-1 rounded-md bg-white/10 backdrop-blur-sm inline-block w-fit" x-text="product.category?.name || 'Skincare'"></span>
                        <h2 class="text-2xl sm:text-4xl font-extrabold text-white mb-2 leading-tight tracking-tight" x-text="product.name"></h2>
                        <p class="text-white/65 text-xs sm:text-sm max-w-md line-clamp-2 mb-5 font-normal leading-relaxed" x-text="product.description"></p>
                        <div class="flex items-center gap-4">
                            <span class="text-xl font-bold text-white bg-white/15 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/20" x-text="'Br ' + parseFloat(product.price).toFixed(2)"></span>
                            <div class="flex gap-2">
                                <button @click="$store.cart.add(product)"
                                    class="text-[12px] font-bold px-3.5 py-2 rounded-xl flex items-center justify-center gap-1.5 transition-all active:scale-95 border backdrop-blur-sm"
                                    :class="$store.cart.items.some(i => i.id === product.id) ? 'bg-emerald-500/20 border-emerald-400/40 text-emerald-300' : 'bg-white/10 border-white/25 text-white hover:bg-white/20'">
                                    <template x-if="$store.cart.items.some(i => i.id === product.id)">
                                        <span><svg class="w-3.5 h-3.5 inline text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> In Cart</span>
                                    </template>
                                    <template x-if="!$store.cart.items.some(i => i.id === product.id)">
                                        <span><svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg> Cart</span>
                                    </template>
                                </button>
                                <button @click="openCheckout('single', product)"
                                    class="bg-white hover:bg-white/90 shadow-md text-slate-900 text-[12px] font-bold px-4 py-2 rounded-xl flex items-center justify-center gap-1.5 transition-all active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Order Now
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product image on mobile top, desktop right -->
                    <template x-if="product.image_url">
                        <div class="flex-shrink-0 w-48 h-40 sm:w-72 sm:h-56 rounded-2xl overflow-hidden bg-white/10 backdrop-blur-sm border border-white/15 shadow-lg transition-transform duration-300 hover:scale-[1.03]">
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
                    :class="current === i ? 'w-7 bg-white' : 'w-1.5 bg-white/35 hover:bg-white/55'"></button>
            </template>
        </div>

        <!-- Prev/Next arrows -->
        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-xl border border-white/15 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white/80 hover:text-white transition shadow-sm flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-xl border border-white/15 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white/80 hover:text-white transition shadow-sm flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
    @endif

    <!-- ===== MAIN CONTENT ===== -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16">

        <!-- Brand Hero Text -->
        <div class="text-center mb-10">
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight leading-tight mb-3 text-slate-900 dark:text-white">
                <span class="glow-text">GlowAddis</span><br>
                <span class="text-slate-900 dark:text-white text-3xl sm:text-5xl font-semibold">Beauty delivered to your desk</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
                Discover and order premium cosmetics and beauty products — effortlessly.
            </p>
        </div>

        <!-- ===== Visual Category Filters ===== -->
        <div class="flex gap-2 sm:gap-3 overflow-x-auto hidden-scrollbar mb-6 px-1">
            <template x-for="category in categories" :key="category.name">
                <button @click="activeCategory = category.name"
                        class="flex-shrink-0 px-4 py-2 md:py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all duration-200 text-sm font-semibold"
                        :class="activeCategory === category.name ? 'chip-active' : 'bg-white dark:bg-white/5 border border-purple-200/80 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:text-purple-700 dark:hover:text-white hover:bg-purple-50/60 dark:hover:bg-white/10 shadow-xs'">
                    
                    <span class="text-sm md:text-base leading-none" x-text="category.icon"></span>
                    <span class="text-[11px] md:text-sm whitespace-nowrap" x-text="category.name"></span>
                </button>
            </template>
        </div>

        <!-- ===== Search + Filter Bar ===== -->
        <div class="sort-glass p-2.5 mb-8 flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
            <!-- Search -->
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="Search products…" class="input-field w-full rounded-xl py-2 pl-10 pr-4 text-sm">
            </div>

            <!-- Sort buttons -->
            <div class="flex items-center gap-1.5 flex-shrink-0">
                <button @click="sortBy='default'"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all"
                    :class="sortBy==='default' ? 'chip-active' : 'bg-white dark:bg-white/5 border border-purple-200/80 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:text-purple-700 dark:hover:text-white'">
                    Default
                </button>
                <button @click="sortBy='price_asc'"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all"
                    :class="sortBy==='price_asc' ? 'chip-active' : 'bg-white dark:bg-white/5 border border-purple-200/80 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:text-purple-700 dark:hover:text-white'">
                    Br Low→High
                </button>
                <button @click="sortBy='price_desc'"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all"
                    :class="sortBy==='price_desc' ? 'chip-active' : 'bg-white dark:bg-white/5 border border-purple-200/80 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:text-purple-700 dark:hover:text-white'">
                    Br High→Low
                </button>
                <button @click="sortBy='name_asc'"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all"
                    :class="sortBy==='name_asc' ? 'chip-active' : 'bg-white dark:bg-white/5 border border-purple-200/80 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:text-purple-700 dark:hover:text-white'">
                    A→Z
                </button>
            </div>

            <span class="text-slate-500 dark:text-slate-400 text-xs whitespace-nowrap px-2">
                <span x-text="filteredProducts.length"></span> items
            </span>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            <template x-for="product in paginatedProducts" :key="product.id">
                <div class="rounded-2xl overflow-hidden card-hover flex flex-col group border border-purple-100 dark:border-white/8 bg-white dark:bg-white/[0.03] shadow-[0_2px_12px_rgba(109,40,217,0.08)] dark:shadow-none">

                    <div class="relative h-52 sm:h-56 overflow-hidden bg-slate-100 dark:bg-white/5 cursor-pointer flex items-center justify-center" @click="openModal(product)">
                        <template x-if="product.image_url">
                            <img :src="product.image_url" :alt="product.name" class="object-cover w-full h-full group-hover:scale-[1.08] transition-transform duration-500 ease-out">
                        </template>
                        <template x-if="!product.image_url">
                            <div class="w-full h-full flex items-center justify-center">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-purple-100 dark:bg-white/10">
                                    <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                            </div>
                        </template>
                        <div class="absolute top-2.5 left-2.5">
                            <template x-if="product.is_active">
                                <span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wide shadow-xs">In Stock</span>
                            </template>
                            <template x-if="!product.is_active">
                                <span class="bg-slate-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wide shadow-xs">Out of Stock</span>
                            </template>
                        </div>
                        <div class="absolute top-2.5 right-2.5">
                            <span class="bg-slate-900/85 dark:bg-black/70 backdrop-blur-sm text-white text-[11px] font-bold px-2.5 py-1 rounded-lg shadow-sm" x-text="'Br ' + parseFloat(product.price).toFixed(2)"></span>
                        </div>
                    </div>
                    <div class="p-3.5 flex flex-col flex-grow">
                        <h3 class="font-semibold text-sm text-slate-900 dark:text-white leading-tight mb-1 line-clamp-2" x-text="product.name"></h3>
                        <p class="text-slate-500 dark:text-slate-400 text-[11px] leading-relaxed flex-grow line-clamp-2 mb-3 font-normal" x-text="product.description"></p>
                        <template x-if="product.is_active">
                            <div class="flex gap-1.5">
                                <button @click="$store.cart.add(product)" title="Add to Cart"
                                    class="flex-1 font-bold py-1.5 rounded-xl flex items-center justify-center gap-1 transition-all text-xs relative"
                                    :class="$store.cart.items.some(i => i.id === product.id) ? 'bg-emerald-500/15 border border-emerald-500/40 text-emerald-700 dark:text-emerald-400' : 'bg-white dark:bg-white/5 border border-slate-200 dark:border-white/15 text-slate-800 dark:text-white hover:bg-slate-50 dark:hover:bg-white/10 shadow-xs'">
                                    
                                    <template x-if="$store.cart.items.some(i => i.id === product.id)">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <template x-if="$store.cart.items.find(i => i.id === product.id).quantity > 1">
                                                <span x-text="$store.cart.items.find(i => i.id === product.id).quantity" class="absolute -top-1.5 -right-1.5 bg-purple-600 text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center shadow-xs"></span>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <template x-if="!$store.cart.items.some(i => i.id === product.id)">
                                        <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </template>
                                </button>
                                <button @click="openCheckout('single', product)"
                                    class="flex-[2] btn-glow text-white text-xs font-semibold py-2 rounded-xl flex items-center justify-center transition-all">
                                    Order Now
                                </button>
                            </div>
                        </template>
                        <template x-if="!product.is_active">
                            <button disabled class="mt-2 w-full text-slate-400 text-xs font-semibold py-1.5 rounded-xl flex items-center justify-center cursor-not-allowed border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-white/5">
                                Out of Stock
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <div x-show="filteredProducts.length === 0" class="col-span-full py-20 text-center">
                <p class="text-slate-500 dark:text-slate-400 text-sm">No products match your search.</p>
                <button @click="search=''" class="mt-2 text-purple-600 dark:text-purple-400 text-xs font-semibold hover:underline">Clear search</button>
            </div>
        </div>

        <!-- Pagination -->
        <div x-show="totalPages > 1" class="flex items-center justify-center gap-2 mt-10">
            <button @click="currentPage > 1 && currentPage--" :disabled="currentPage===1"
                class="w-9 h-9 rounded-xl glass border border-slate-200/80 dark:border-white/10 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition disabled:opacity-30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <template x-for="page in totalPages" :key="page">
                <button @click="currentPage=page"
                    class="w-9 h-9 rounded-xl text-sm font-semibold transition-all"
                    :class="currentPage===page ? 'btn-glow text-white' : 'glass border border-slate-200/80 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                    <span x-text="page"></span>
                </button>
            </template>
            <button @click="currentPage < totalPages && currentPage++" :disabled="currentPage===totalPages"
                class="w-9 h-9 rounded-xl glass border border-slate-200/80 dark:border-white/10 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition disabled:opacity-30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    <!-- Product Details Modal -->
    <div x-show="showModal" style="display:none;" class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6 overflow-hidden">
        <!-- Backdrop -->
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="closeModal()" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <!-- Modal Content -->
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-6" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-6"
             class="relative w-full max-w-sm sm:max-w-md bg-white dark:bg-[#13131c] text-slate-900 dark:text-white rounded-3xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh] border border-slate-200/80 dark:border-white/10">
             
             <!-- Close Button -->
             <button @click="closeModal()" class="absolute top-5 right-5 w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/10 text-slate-500 hover:text-slate-900 dark:hover:text-white transition-all z-[201] border border-slate-200/60 dark:border-white/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
             </button>

            <div class="p-6 sm:p-7 overflow-y-auto w-full flex flex-col custom-scrollbar" style="max-height: 85vh;">
                <!-- Product Image Area -->
                <div class="rounded-2xl overflow-hidden mb-5 relative bg-slate-50 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 flex items-center justify-center w-full min-h-[20vh] shadow-xs">
                    <template x-if="selectedProduct?.image_url">
                        <img :src="selectedProduct.image_url" class="max-w-[75%] h-auto max-h-[18vh] object-contain transition-transform duration-300 hover:scale-105">
                    </template>
                </div>

                <!-- Labels/Badges row -->
                <div class="flex items-center gap-2 mb-3">
                    <template x-if="selectedProduct?.is_active">
                        <span class="flex items-center gap-1.5 text-[10px] uppercase tracking-wider font-bold px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            In Stock
                        </span>
                    </template>
                    <span class="text-[10px] uppercase tracking-wider font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/10" x-text="selectedProduct?.category?.name || 'Beauty'"></span>
                </div>

                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight" x-text="selectedProduct?.name"></h2>

                <div class="flex items-center gap-4 mb-4">
                    <div class="px-3.5 py-1.5 rounded-xl bg-purple-600 text-white font-bold text-base shadow-xs">
                        <span x-text="'Br ' + parseFloat(selectedProduct?.price ?? 0).toFixed(2)"></span>
                    </div>
                </div>

                <div class="rounded-2xl p-4 border border-slate-200/80 dark:border-white/10 bg-slate-50 dark:bg-white/5 flex-grow mb-5">
                    <h4 class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Description</h4>
                    <p class="text-slate-700 dark:text-slate-300 text-xs sm:text-sm leading-relaxed whitespace-pre-wrap font-normal" x-text="selectedProduct?.description || 'No description available for this product.'"></p>
                </div>

                <div class="flex gap-2.5">
                    <template x-if="selectedProduct?.is_active">
                        <div class="flex-1">
                             <button @click="$store.cart.add(selectedProduct)"
                                 class="w-full py-3 rounded-xl font-bold flex items-center justify-center gap-2 transition-all text-xs uppercase tracking-wider"
                                 :class="$store.cart.items.some(i => i.id === selectedProduct.id) ? 'bg-emerald-500/15 border-2 border-emerald-500/50 text-emerald-700 dark:text-emerald-400' : 'bg-slate-900 dark:bg-white/10 hover:bg-slate-800 dark:hover:bg-white/15 text-white border-2 border-slate-900 dark:border-white/20 shadow-xs'">
                                 
                                 <template x-if="$store.cart.items.some(i => i.id === selectedProduct.id)">
                                     <span class="flex items-center gap-1.5">
                                         <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                         <span>In Cart</span>
                                     </span>
                                 </template>
                                 
                                 <template x-if="!$store.cart.items.some(i => i.id === selectedProduct.id)">
                                     <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg> 
                                        Add to Cart
                                     </span>
                                 </template>
                             </button>
                        </div>
                    </template>
                    <template x-if="selectedProduct?.is_active">
                        <button @click="closeModal(); openCheckout('single', selectedProduct)"
                                class="flex-[1.2] btn-glow text-white text-xs font-bold py-3 rounded-xl flex items-center justify-center uppercase tracking-wider transition-all shadow-xs active:scale-95">
                            Order Now
                        </button>
                    </template>
                    <template x-if="!selectedProduct?.is_active">
                        <button disabled class="w-full text-slate-400 text-xs font-bold py-3 rounded-xl flex items-center justify-center cursor-not-allowed border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-white/5 uppercase tracking-wider">Out of Stock</button>
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
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="showCheckoutModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-lg bg-white dark:bg-[#13131c] text-slate-900 dark:text-white rounded-3xl overflow-hidden shadow-2xl flex flex-col z-10 border border-slate-200/80 dark:border-white/10">
             
             <div class="p-6 md:p-8 relative">
                 <button type="button" @click="showCheckoutModal = false" class="absolute top-4 right-4 md:top-6 md:right-6 w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/10 text-slate-500 hover:text-slate-900 dark:hover:text-white transition border border-slate-200/60 dark:border-white/10"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                 
                 <div class="mb-6 pr-10">
                     <h3 class="font-extrabold text-slate-900 dark:text-white text-2xl mb-1">Secure Checkout</h3>
                     <template x-if="checkoutMode === 'cart'">
                         <p class="text-slate-500 dark:text-slate-400 text-sm">You are checking out <strong class="text-purple-600 dark:text-[#f0abfc] font-semibold" x-text="$store.cart.count + ' items'"></strong> for <strong class="text-slate-900 dark:text-white" x-text="'Br ' + $store.cart.total.toFixed(2)"></strong>.</p>
                     </template>
                     <template x-if="checkoutMode === 'single'">
                         <p class="text-slate-500 dark:text-slate-400 text-sm">You are ordering <strong class="text-purple-600 dark:text-[#f0abfc] font-semibold" x-text="checkoutProduct?.name"></strong>.</p>
                     </template>
                 </div>

                 <form action="{{ route('orders.store') }}" method="POST" class="space-y-4" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
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
                                 <label class="block text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 text-right">Qty</label>
                                 <input type="number" name="quantity" value="1" min="1" required class="input-field w-full rounded-xl py-2 px-3 text-center text-sm font-bold">
                             </div>
                         </div>
                     </template>
                     
                     <div>
                         <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Full Name</label>
                         <template x-if="$store.telegram.isTMA">
                          <div class="mb-4 p-2.5 rounded-xl bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800/40 flex items-center gap-2 text-xs text-purple-700 dark:text-purple-300">
                              <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2-.08-.06-.19-.04-.27-.02-.12.02-1.96 1.25-5.54 3.67-.52.36-1 .54-1.42.53-.47-.01-1.37-.26-2.03-.48-.82-.27-1.47-.42-1.42-.88.03-.24.37-.49 1.02-.75 4-1.74 6.68-2.88 8.04-3.44 3.83-1.58 4.62-1.85 5.14-1.86.11 0 .37.03.54.17.14.12.18.28.2.45-.02.07-.01.24-.04.38z"/></svg>
                              <span>Telegram Connected: <strong><span x-text="$store.telegram.user.name"></span></strong> (@<span x-text="$store.telegram.user.username"></span>)</span>
                          </div>
                      </template>
                      <input type="text" name="customer_name" required x-model="customerName" placeholder="Full Name" class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                     </div>
                     
                     <div>
                         <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Phone Number</label>
                         <input type="text" name="phone" required placeholder="+251..." class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                     </div>
                     
                     <div>
                         <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Delivery Note</label>
                         <input type="text" name="department" placeholder="" class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                     </div>

                     <div>
                         <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center justify-between mb-1.5">
                             Telegram Username <span class="text-[9px] text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-1.5 py-0.5 rounded tracking-normal normal-case ml-2 whitespace-nowrap">+ Updates</span>
                         </label>
                         <input type="text" name="telegram_username" x-model="telegramUsername" placeholder="@username" class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                     </div>

                     <div class="pt-3 flex gap-3">
                         <button type="submit" :disabled="isSubmitting" class="w-full relative group overflow-hidden rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3.5 font-bold transition hover:brightness-105 shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                             <template x-if="!isSubmitting">
                                 <span class="relative z-10 flex items-center justify-center gap-2">
                                     Complete Order
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                 </span>
                             </template>
                             <template x-if="isSubmitting">
                                 <span class="relative z-10 flex items-center justify-center gap-2">
                                     <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                         <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                         <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                     </svg>
                                     Processing Order…
                                 </span>
                             </template>
                         </button>
                     </div>
                 </form>
             </div>
        </div>
    </div>

    <!-- Floating Action Button for Custom Order -->
    <button @click="openContactModal()" 
            class="fixed bottom-6 left-6 z-50 px-5 py-3 rounded-full flex items-center gap-2.5 text-white shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all group font-semibold text-xs sm:text-sm bg-slate-900/80 dark:bg-white/10 backdrop-blur-xl border border-white/15 hover:bg-slate-900 dark:hover:bg-white/20" 
            title="Custom Order">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
        </svg>
        <span class="tracking-wide">Custom Order</span>
    </button>

    <!-- Custom Order Modal -->
    <div x-show="showContactModal" style="display:none;" class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6"
         @open-custom-request.window="openContactModal()">
        <div x-show="showContactModal"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showContactModal = false; document.body.style.overflow = '';" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="showContactModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-lg bg-white dark:bg-[#13131c] text-slate-900 dark:text-white rounded-3xl overflow-hidden shadow-2xl flex flex-col border border-slate-200/80 dark:border-white/10">
             
             <div class="p-6 md:p-8 relative">
                 <button @click="showContactModal = false; document.body.style.overflow = '';" class="absolute top-4 right-4 text-slate-500 hover:text-slate-900 dark:hover:text-white transition bg-slate-100 dark:bg-white/10 rounded-full p-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                 
                 <div class="flex items-center gap-3 mb-2">
                     <div class="w-10 h-10 rounded-xl glow-gradient flex items-center justify-center shadow-xs">
                         <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                     </div>
                     <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Custom Order</h2>
                 </div>
                 <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 flex-1">Can't find what you're looking for? Place a custom order!</p>

                 <form action="{{ route('custom-request.store') }}" method="POST" class="space-y-4">
                     @csrf
                     
                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                         <div>
                             <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Your Name</label>
                             <input type="text" name="customer_name" required x-model="customerName" placeholder="Full Name" class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                         </div>
                         <div>
                             <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Phone Number</label>
                             <input type="text" name="phone" required placeholder="+251..." class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                         </div>
                     </div>

                     <div>
                         <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center justify-between mb-1.5">
                             Telegram Username <span class="text-[9px] text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-1.5 py-0.5 rounded tracking-normal normal-case ml-2 whitespace-nowrap">+ Updates</span>
                         </label>
                         <input type="text" name="telegram_username" x-model="telegramUsername" placeholder="@username" class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                     </div>

                     <div>
                         <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">What are you looking for?</label>
                         <textarea name="request_message" required rows="3" placeholder="Describe the item, brand, or product you need..." class="input-field w-full rounded-xl py-2.5 px-4 text-sm resize-none"></textarea>
                     </div>

                     <div class="pt-2">
                         <button type="submit" class="w-full relative group overflow-hidden rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:brightness-105 text-white py-3.5 font-bold transition shadow-sm">
                             <span class="relative z-10 flex items-center justify-center gap-2">
                                 Submit Custom Order
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
        openCheckout(mode, product) {
            window.dispatchEvent(new CustomEvent('open-single-checkout', { detail: { mode, product } }));
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
        customerName: '',
        telegramUsername: '',
        
        categories: [
            { name: 'All', icon: '✨' },
            { name: 'Moisturizer', icon: '🧴' },
            { name: 'Cleanser', icon: '🧼' },
            { name: 'Exfoliant', icon: '🫧' },
            { name: 'Sunscreen', icon: '☀️' },
            { name: 'Serum', icon: '💧' }
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

        openContactModal() {
            if (window.Alpine && Alpine.store('telegram') && Alpine.store('telegram').user) {
                const tgUser = Alpine.store('telegram').user;
                if (!this.customerName && tgUser.name) this.customerName = tgUser.name;
                if (!this.telegramUsername && tgUser.username) this.telegramUsername = tgUser.username;
            }
            this.showContactModal = true;
            document.body.style.overflow = 'hidden';
        },

        openCheckout(mode = 'cart', product = null) {
            this.checkoutMode = mode;
            this.checkoutProduct = product;
            if (window.Alpine && Alpine.store('telegram') && Alpine.store('telegram').user) {
                const tgUser = Alpine.store('telegram').user;
                if (!this.customerName && tgUser.name) this.customerName = tgUser.name;
                if (!this.telegramUsername && tgUser.username) this.telegramUsername = tgUser.username;
            }
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
            window.addEventListener('open-single-checkout', e => {
                this.openCheckout(e.detail.mode, e.detail.product);
            });
            this.$watch('showCheckoutModal', val => {
                if(!val && !this.showModal && !this.showContactModal) document.body.style.overflow = '';
            });
        }
    }
}
</script>
@endsection
