@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-10">
        <div>
            <p class="text-purple-600 dark:text-glow-400 text-xs font-semibold uppercase tracking-widest mb-1">Admin Panel</p>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Products</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $products->count() }} product{{ $products->count() !== 1 ? 's' : '' }} in store</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <button onclick="deleteAllProducts()" class="px-5 py-2.5 rounded-xl text-sm font-bold text-red-600 dark:text-red-400 flex items-center gap-2 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 transition border border-red-200 dark:border-red-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete All
            </button>
            <button onclick="document.getElementById('importModal').style.display='flex'" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-700 dark:text-white flex items-center gap-2 bg-white dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition border border-slate-200/80 dark:border-white/10 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Import
            </button>
            <a href="{{ route('admin.products.create') }}" class="btn-glow px-5 py-2.5 rounded-xl text-sm font-bold text-white flex items-center gap-2 w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Product
            </a>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
        @forelse($products as $product)
        <div class="bg-white dark:bg-[#14141e] rounded-2xl overflow-hidden card-hover flex flex-col border border-purple-100 dark:border-white/8 shadow-[0_2px_12px_rgba(109,40,217,0.08)] dark:shadow-none" data-product-id="{{ $product->id }}">
            <div class="h-36 bg-gradient-to-br from-glow-900/30 to-slate-900 relative overflow-hidden">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-full opacity-70">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                @endif
                <div class="absolute top-2 right-2">
                    @if($product->is_active)
                        <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-400/10 border border-emerald-300 dark:border-emerald-400/20 px-2 py-0.5 rounded-full">Active</span>
                    @else
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-500/10 border border-slate-200 dark:border-slate-500/20 px-2 py-0.5 rounded-full">Inactive</span>
                    @endif
                </div>
            </div>
            <div class="p-4 flex flex-col flex-grow">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white leading-tight">{{ $product->name }}</h3>
                <p class="text-slate-600 dark:text-slate-400 text-xs mt-1 line-clamp-2 flex-grow">{{ $product->description }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-lg font-extrabold text-purple-700 dark:glow-text">Br {{ number_format($product->price, 2) }}</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="p-2 bg-purple-50 dark:bg-slate-800/50 hover:bg-purple-100 dark:hover:bg-glow-500/20 text-purple-700 dark:text-glow-400 rounded-lg transition-all border border-purple-200 dark:border-slate-700/50" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </a>
                        <button onclick="deleteProduct({{ $product->id }})" class="p-2 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 rounded-lg transition-all border border-red-200 dark:border-red-500/20" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center glass-dark rounded-2xl border border-slate-200/80 dark:border-white/10 shadow-sm">
            <p class="text-slate-700 dark:text-slate-400 font-medium">No products yet</p>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Click "Add Product" to create your first listing.</p>
        </div>
        @endforelse
    </div>

    {{-- Minimal Text Pagination --}}
    @if($products->hasPages())
    <div class="flex flex-col items-center justify-center mt-10 gap-2">
        <p class="text-center text-slate-600 dark:text-slate-400 text-sm font-semibold">
            Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
        </p>
        <div class="flex gap-6 mt-2 text-sm font-bold">
            @if($products->onFirstPage())
                <span class="text-slate-400 dark:text-slate-600 cursor-not-allowed">← Previous</span>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="text-purple-600 dark:text-glow-400 hover:text-purple-900 dark:hover:text-white transition">← Previous</a>
            @endif

            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="text-purple-600 dark:text-glow-400 hover:text-purple-900 dark:hover:text-white transition">Next →</a>
            @else
                <span class="text-slate-400 dark:text-slate-600 cursor-not-allowed">Next →</span>
            @endif
        </div>
    </div>
    @else
    <p class="text-center text-slate-600 dark:text-slate-400 text-sm font-semibold mt-10">
        Total: {{ $products->total() }} products
    </p>
    @endif

</div>

    <!-- Import Modal -->
    <div id="importModal" style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#0f172a] rounded-2xl p-6 w-full max-w-md border border-slate-200 dark:border-white/10 shadow-2xl relative">
            <button onclick="document.getElementById('importModal').style.display='none'" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 dark:text-white/50 dark:hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Bulk Import Products</h3>
            <p class="text-slate-600 dark:text-slate-400 text-xs mb-6 leading-relaxed">
                Upload an Excel or CSV file containing your products.<br>
                First row must be headers: <strong>Name, Description, Price, ImageURL</strong> (optional).
            </p>
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="bg-slate-50 dark:bg-white/5 w-full p-6 rounded-xl border border-slate-300 dark:border-white/10 text-center relative cursor-pointer hover:bg-purple-50/50 dark:hover:bg-white/10 transition border-dashed">
                    <input type="file" name="csv_file" required accept=".csv, .xlsx, .xls" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <svg class="w-8 h-8 text-purple-600 dark:text-glow-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-slate-900 dark:text-white text-sm font-semibold">Click or upload file</p>
                    <p class="text-slate-500 text-xs mt-1">.csv, .xlsx, .xls</p>
                </div>
                <button type="submit" class="w-full btn-glow py-3 rounded-xl text-white font-bold text-sm">
                    Upload & Import
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')

<!-- ===== Professional Confirm Modal ===== -->
<div id="confirmModal" style="display:none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-md">
    <div class="relative bg-[#0c0f1a] rounded-2xl p-8 w-full max-w-sm shadow-2xl border border-white/10 overflow-hidden">
        <!-- Decorative glow blob -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <!-- Icon -->
        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-red-500/10 border border-red-500/20 mx-auto mb-5">
            <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <!-- Text -->
        <h3 class="text-xl font-extrabold text-white text-center mb-2" id="confirmTitle">Delete Product?</h3>
        <p class="text-slate-400 text-sm text-center leading-relaxed mb-7" id="confirmMsg">This action will permanently remove the product from your store.</p>
        <!-- Buttons -->
        <div class="flex gap-3">
            <button onclick="cancelConfirm()" class="flex-1 py-3 rounded-xl text-sm font-semibold text-slate-300 hover:text-white glass hover:bg-white/10 transition border border-white/10">
                Cancel
            </button>
            <button id="confirmOkBtn" class="flex-1 py-3 rounded-xl text-sm font-bold text-white bg-red-500 hover:bg-red-600 active:scale-95 transition border border-red-400/30 shadow-lg shadow-red-500/20">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- ===== Undo Toast ===== -->
<div id="undoToast" style="display:none;position:fixed;bottom:28px;left:50%;transform:translateX(-50%);z-index:9998;"
     class="flex items-center gap-4 px-5 py-3 rounded-2xl shadow-2xl border border-white/10 min-w-[300px]"
     style="background:rgba(15,23,42,0.95);">
    <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/>
    </svg>
    <span id="undoToastMsg" class="text-white text-sm flex-1 font-medium"></span>
    <span id="undoCountdown" class="text-slate-400 text-xs font-mono w-5 text-right"></span>
    <button onclick="undoDelete()" class="px-4 py-1.5 rounded-lg bg-glow-500/20 hover:bg-glow-500/30 text-glow-400 text-xs font-bold transition border border-glow-500/30">
        Undo
    </button>
</div>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
    let _undoTimer = null, _undoCountTimer = null, _pendingDeleteFn = null, _undoSecondsLeft = 5;

    /* ===== Custom Confirm Modal ===== */
    function showConfirm(title, message, onConfirm) {
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMsg').textContent   = message;
        document.getElementById('confirmModal').style.display = 'flex';
        document.getElementById('confirmOkBtn').onclick = () => {
            document.getElementById('confirmModal').style.display = 'none';
            onConfirm();
        };
    }
    function cancelConfirm() {
        document.getElementById('confirmModal').style.display = 'none';
    }
    // Close on backdrop click
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) cancelConfirm();
    });

    /* ===== Undo Toast ===== */
    function showUndoToast(message, deleteFn) {
        if (_undoTimer) { clearTimeout(_undoTimer); clearInterval(_undoCountTimer); }
        _pendingDeleteFn = deleteFn;
        _undoSecondsLeft = 5;
        document.getElementById('undoToastMsg').textContent   = message;
        document.getElementById('undoCountdown').textContent  = _undoSecondsLeft;
        document.getElementById('undoToast').style.display    = 'flex';
        _undoCountTimer = setInterval(() => {
            _undoSecondsLeft--;
            document.getElementById('undoCountdown').textContent = _undoSecondsLeft;
            if (_undoSecondsLeft <= 0) clearInterval(_undoCountTimer);
        }, 1000);
        _undoTimer = setTimeout(() => {
            hideUndoToast();
            if (_pendingDeleteFn) { _pendingDeleteFn(); _pendingDeleteFn = null; }
        }, 5000);
    }
    function undoDelete() {
        clearTimeout(_undoTimer); clearInterval(_undoCountTimer);
        _pendingDeleteFn = null;
        hideUndoToast();
    }
    function hideUndoToast() { document.getElementById('undoToast').style.display = 'none'; }

    /* ===== Single Delete ===== */
    function deleteProduct(productId) {
        showConfirm(
            'Delete Product?',
            'This will permanently remove the product from your store.',
            () => {
                const card = document.querySelector(`[data-product-id="${productId}"]`);
                if (card) { card.style.opacity = '0.3'; card.style.pointerEvents = 'none'; }

                showUndoToast('Product removed.', () => {
                    fetch(`/admin/products/${productId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && card) {
                            card.style.transition = 'all 0.35s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            setTimeout(() => card.remove(), 350);
                        } else if (!data.success) {
                            if (card) { card.style.opacity = '1'; card.style.pointerEvents = ''; }
                            alert('Failed to delete product.');
                        }
                    })
                    .catch(() => { if (card) { card.style.opacity = '1'; card.style.pointerEvents = ''; } alert('Network error.'); });
                });
            }
        );
    }

    /* ===== Delete All ===== */
    function deleteAllProducts() {
        const count = document.querySelectorAll('[data-product-id]').length;
        if (count === 0) { alert('No products to delete.'); return; }

        showConfirm(
            `Delete All ${count} Products?`,
            'This will permanently erase every product from your store. You cannot undo this.',
            () => {
                const allCards = document.querySelectorAll('[data-product-id]');
                allCards.forEach(c => { c.style.opacity = '0.3'; c.style.pointerEvents = 'none'; });

                showUndoToast(`All ${count} products will be deleted.`, () => {
                    fetch('/admin/products', {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            allCards.forEach(c => { c.style.opacity = '1'; c.style.pointerEvents = ''; });
                            alert('Delete failed.');
                        }
                    })
                    .catch(() => { allCards.forEach(c => { c.style.opacity = '1'; c.style.pointerEvents = ''; }); alert('Network error.'); });
                });
            }
        );
    }
</script>
@endpush

