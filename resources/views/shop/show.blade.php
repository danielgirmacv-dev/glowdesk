@extends('layouts.app')

@section('title', $product->name . ' – GlowAddis')

@section('meta')
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 160) }}">
    <link rel="canonical" href="{{ $product->url }}">

    <!-- Open Graph / Facebook / Telegram -->
    <meta property="og:site_name" content="GlowAddis">
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $product->name }} – GlowAddis">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 200) }}">
    <meta property="og:url" content="{{ $product->url }}">
    @if($product->image_url)
        <meta property="og:image" content="{{ $product->image_url }}">
        <meta property="og:image:secure_url" content="{{ $product->image_url }}">
        <meta property="og:image:alt" content="{{ $product->name }}">
    @else
        <meta property="og:image" content="{{ url('/glowaddis-logo.png') }}">
    @endif
    <meta property="product:price:amount" content="{{ $product->price }}">
    <meta property="product:price:currency" content="ETB">
    <meta property="product:availability" content="{{ $product->is_active ? 'in stock' : 'out of stock' }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $product->name }} – GlowAddis">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 200) }}">
    @if($product->image_url)
        <meta name="twitter:image" content="{{ $product->image_url }}">
    @else
        <meta name="twitter:image" content="{{ url('/glowaddis-logo.png') }}">
    @endif

    <!-- Schema.org JSON-LD Structured Data for Search Engines -->
    <script type="application/ld+json">
    {!! json_encode([
        '@' . 'context' => 'https://schema.org/',
        '@' . 'type' => 'Product',
        'name' => $product->name,
        'image' => [$product->image_url ?: url('/glowaddis-logo.png')],
        'description' => strip_tags($product->description),
        'offers' => [
            '@' . 'type' => 'Offer',
            'url' => $product->url,
            'priceCurrency' => 'ETB',
            'price' => number_format($product->price, 2, '.', ''),
            'availability' => $product->is_active ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12" x-data="productPageData()">

    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-6 sm:mb-8 overflow-x-auto whitespace-nowrap">
        <a href="{{ route('shop.index') }}" class="hover:text-rose-600 dark:hover:text-pink-400 transition-colors flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Home
        </a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-rose-600 dark:hover:text-pink-400 transition-colors">Products</a>
        <span>/</span>
        <span class="text-slate-900 dark:text-white font-medium truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Product Detail Container -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- Left Column: Product Image Gallery -->
        <div class="lg:col-span-6 flex flex-col gap-4">
            <div class="relative w-full rounded-3xl overflow-hidden bg-white dark:bg-[#160c12] border border-pink-100 dark:border-white/10 shadow-lg flex items-center justify-center p-6 sm:p-10 min-h-[340px] sm:min-h-[460px]">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-[380px] w-auto max-w-full object-contain transition-transform duration-500 hover:scale-105">
                @else
                    <div class="flex flex-col items-center justify-center text-center p-8">
                        <div class="w-20 h-20 rounded-3xl flex items-center justify-center bg-pink-100 dark:bg-white/10 mb-3">
                            <svg class="w-10 h-10 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-400">GlowAddis Beauty</span>
                    </div>
                @endif

                <!-- Stock Badge Overlay -->
                <div class="absolute top-4 left-4">
                    @if($product->is_active)
                        <span class="bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            In Stock
                        </span>
                    @else
                        <span class="bg-slate-700 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                            Out of Stock
                        </span>
                    @endif
                </div>

                <!-- Price Tag Overlay -->
                <div class="absolute top-4 right-4">
                    <span class="bg-slate-900/90 dark:bg-black/80 backdrop-blur-md text-white text-sm font-extrabold px-3.5 py-1.5 rounded-xl shadow-md border border-white/15">
                        Br {{ number_format($product->price, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Right Column: Product Info & Actions -->
        <div class="lg:col-span-6 flex flex-col">
            <span class="text-xs font-bold text-pink-600 dark:text-pink-400 uppercase tracking-[0.15em] mb-2 px-3 py-1 rounded-lg bg-pink-50 dark:bg-pink-950/40 border border-pink-200 dark:border-pink-800/40 inline-block w-fit">
                GlowAddis Selection
            </span>

            <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-snug mb-3">
                {{ $product->name }}
            </h1>

            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-200/80 dark:border-white/10">
                <div class="text-3xl font-black text-rose-600 dark:text-pink-400">
                    Br {{ number_format($product->price, 2) }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400">
                    Official price • Ready for direct delivery
                </div>
            </div>

            <!-- Description -->
            <div class="mb-8">
                <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Description</h2>
                <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 text-sm sm:text-base leading-relaxed whitespace-pre-line bg-slate-50/80 dark:bg-white/5 p-5 rounded-2xl border border-slate-200/80 dark:border-white/10">
                    {{ $product->description ?: 'No detailed description available for this beauty item.' }}
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                @if($product->is_active)
                    <!-- Add to Cart (Alpine Store Integration) -->
                    <button type="button" @click="$store.cart.add(currentProduct)"
                            class="flex-1 py-3.5 px-6 rounded-xl font-bold flex items-center justify-center gap-2 transition-all active:scale-95 text-sm uppercase tracking-wider border-2"
                            :class="$store.cart.items.some(i => i.id === currentProduct.id) 
                                ? 'bg-emerald-500/15 border-emerald-500/50 text-emerald-700 dark:text-emerald-400 shadow-sm' 
                                : 'bg-white dark:bg-white/10 border-slate-900 dark:border-white/20 text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-white/15 shadow-sm'">
                        <template x-if="$store.cart.items.some(i => i.id === currentProduct.id)">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Added to Cart
                            </span>
                        </template>
                        <template x-if="!$store.cart.items.some(i => i.id === currentProduct.id)">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-5 h-5 stroke-[2.2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Add to Cart
                            </span>
                        </template>
                    </button>

                    <!-- Order Now Button (triggers checkout modal) -->
                    <button type="button" @click="showCheckoutModal = true"
                            class="flex-[1.4] btn-glow text-white text-sm font-bold py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 transition-all shadow-md active:scale-95 uppercase tracking-wider">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Order Now
                    </button>
                @else
                    <button disabled class="w-full text-slate-400 text-sm font-bold py-4 rounded-xl flex items-center justify-center cursor-not-allowed border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-white/5 uppercase tracking-wider">
                        Currently Out of Stock
                    </button>
                @endif
            </div>

            <!-- Auxiliary Actions & Share -->
            <div class="flex items-center gap-3 pt-4 border-t border-slate-200/80 dark:border-white/10">
                <button type="button" @click="shareProduct()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/15 text-slate-700 dark:text-slate-200 text-xs font-semibold flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    <span x-text="copied ? 'Link Copied!' : 'Share Product'"></span>
                </button>
                
                <a href="{{ route('shop.index') }}" class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to All Products
                </a>
            </div>

        </div>
    </div>

    <!-- Related Products SSR Section -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="mt-16 sm:mt-24 pt-10 border-t border-slate-200/80 dark:border-white/10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                You Might Also Like
            </h2>
            <a href="{{ route('shop.index') }}" class="text-xs font-semibold text-rose-600 dark:text-pink-400 hover:underline">
                View catalog →
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($relatedProducts as $rel)
                <a href="{{ $rel->url }}" class="rounded-2xl overflow-hidden card-hover flex flex-col group border border-pink-100 dark:border-white/8 bg-white dark:bg-white/[0.03] shadow-sm">
                    <div class="relative h-44 sm:h-52 overflow-hidden bg-slate-50 dark:bg-white/5 flex items-center justify-center p-4">
                        @if($rel->image_url)
                            <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" loading="lazy" class="object-contain w-full h-full group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-pink-100 dark:bg-white/10">
                                <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-3.5 flex flex-col flex-grow">
                        <h3 class="font-semibold text-xs sm:text-sm text-slate-900 dark:text-white line-clamp-1 mb-1 group-hover:text-rose-600 transition-colors">
                            {{ $rel->name }}
                        </h3>
                        <span class="text-xs font-bold text-rose-600 dark:text-pink-400">
                            Br {{ number_format($rel->price, 2) }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Single Product Checkout Modal -->
    <div x-show="showCheckoutModal" style="display:none;" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
        <div x-show="showCheckoutModal" @click="showCheckoutModal = false"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="showCheckoutModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-lg bg-white dark:bg-[#13131c] text-slate-900 dark:text-white rounded-3xl shadow-2xl flex flex-col z-10 max-h-[90vh] border border-slate-200/80 dark:border-white/10">
             
             <div class="p-6 md:p-8 relative overflow-y-auto custom-scrollbar">
                 <button type="button" @click="showCheckoutModal = false" class="absolute top-4 right-4 md:top-6 md:right-6 w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/10 text-slate-500 hover:text-slate-900 dark:hover:text-white transition border border-slate-200/60 dark:border-white/10 z-[201]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                 
                 <div class="mb-6 pr-10">
                     <h3 class="font-extrabold text-slate-900 dark:text-white text-2xl mb-1">Direct Order</h3>
                     <p class="text-slate-500 dark:text-slate-400 text-sm">You are ordering <strong class="text-rose-600 dark:text-pink-300 font-semibold">{{ $product->name }}</strong>.</p>
                 </div>

                 <form action="{{ route('orders.store') }}" method="POST" class="space-y-4" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                     @csrf
                     
                     <div class="grid grid-cols-[1fr,100px] gap-4">
                         <input type="hidden" name="product_id" value="{{ $product->id }}">
                         <div></div>
                         <div class="col-start-2 place-self-end mt-[-3rem] z-20">
                             <label class="block text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 text-right">Qty</label>
                             <input type="number" name="quantity" value="1" min="1" required class="input-field w-full rounded-xl py-2 px-3 text-center text-sm font-bold">
                         </div>
                     </div>
                     
                     <div>
                         <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Full Name</label>
                         <template x-if="$store.telegram && $store.telegram.isTMA">
                             <div class="mb-4 p-2.5 rounded-xl bg-pink-50 dark:bg-pink-950/30 border border-pink-200 dark:border-pink-800/40 flex items-center gap-2 text-xs text-rose-700 dark:text-pink-300">
                                 <span>Telegram Connected: <strong><span x-text="$store.telegram.user.name"></span></strong> (@<span x-text="$store.telegram.user.username"></span>)</span>
                             </div>
                         </template>
                         <input type="text" name="customer_name" required placeholder="Full Name" class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                     </div>
                     
                     <div>
                         <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Phone Number</label>
                         <input type="text" name="phone" required placeholder="+251..." class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                     </div>
                     
                     <div>
                         <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Delivery Note / Address</label>
                         <input type="text" name="department" placeholder="Building, office, or location..." class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                     </div>

                     <div>
                         <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center justify-between mb-1.5">
                             Telegram Username <span class="text-[9px] text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-1.5 py-0.5 rounded tracking-normal normal-case ml-2 whitespace-nowrap">+ Updates</span>
                         </label>
                         <input type="text" name="telegram_username" placeholder="@username" class="input-field w-full rounded-xl py-2.5 px-4 text-sm">
                     </div>

                     <div class="pt-3">
                         <button type="submit" :disabled="isSubmitting" class="w-full relative group overflow-hidden rounded-xl bg-gradient-to-r from-rose-600 to-pink-500 text-white py-3.5 font-bold transition hover:brightness-105 shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                             <template x-if="!isSubmitting">
                                 <span class="relative z-10 flex items-center justify-center gap-2">
                                     Submit Order (Br {{ number_format($product->price, 2) }})
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                 </span>
                             </template>
                             <template x-if="isSubmitting">
                                 <span class="relative z-10 flex items-center justify-center gap-2">Processing Order…</span>
                             </template>
                         </button>
                     </div>
                 </form>
             </div>
        </div>
    </div>

</div>

<script>
function productPageData() {
    return {
        currentProduct: @json($product),
        showCheckoutModal: false,
        copied: false,
        shareProduct() {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href);
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            } else {
                alert('Product link: ' + window.location.href);
            }
        }
    };
}
</script>
@endsection
