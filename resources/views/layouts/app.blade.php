<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/glowdesk-logo.png">
    <title>GlowDesk – @yield('title', 'Internal Store')</title>
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Telegram WebApp SDK -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        glow: {
                            50:  '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                            800: '#86198f',
                            900: '#701a75',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    },
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }
        
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.1); }
        }
        .animate-pulse-soft { animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }

        body { background: #0a0a0f; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .glow-gradient { background: linear-gradient(135deg, #d946ef 0%, #7c3aed 50%, #2563eb 100%); }
        .glow-text { background: linear-gradient(135deg, #f0abfc, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); }
        .glass-dark { background: rgba(10,10,20,0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 60px rgba(217, 70, 239, 0.2); }
        .btn-glow { background: linear-gradient(135deg, #d946ef, #7c3aed); box-shadow: 0 4px 20px rgba(217, 70, 239, 0.4); transition: all 0.3s ease; }
        .btn-glow:hover { box-shadow: 0 6px 30px rgba(217, 70, 239, 0.6); transform: translateY(-1px); }
        .input-field { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #e2e8f0; transition: all 0.2s; }
        .input-field:focus { outline: none; border-color: #d946ef; box-shadow: 0 0 0 3px rgba(217,70,239,0.15); background: rgba(255,255,255,0.08); }
        .input-field::placeholder { color: #64748b; }
        ::-webkit-scrollbar { width: 6px; } ::-webkit-scrollbar-track { background: #0a0a0f; } ::-webkit-scrollbar-thumb { background: #d946ef; border-radius: 3px; }
        @keyframes pulse-glow { 0%, 100% { box-shadow: 0 0 20px rgba(217,70,239,0.3); } 50% { box-shadow: 0 0 40px rgba(217,70,239,0.6); } }
        .pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }
        /* Light Mode Overrides */
        .light-mode { background: #f8fafc; color: #0f172a; }
        .light-mode .glass { background: rgba(255,255,255,0.7); border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .light-mode .glass-dark { background: rgba(255,255,255,0.9); border: 1px solid rgba(0,0,0,0.1); }
        .light-mode .text-white:not(.btn-glow) { color: #0f172a !important; }
        .light-mode .text-slate-400, .light-mode .text-slate-500 { color: #475569 !important; }
        .light-mode .input-field { background: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; }
        .light-mode .input-field:focus { background: #ffffff; border-color: #d946ef; box-shadow: 0 0 0 3px rgba(217,70,239,0.1); }
        .light-mode .input-field::placeholder { color: #94a3b8; }
        .light-mode .text-glow-400 { color: #a21caf !important; }
        .light-mode .text-emerald-400 { color: #059669 !important; }
        .light-mode .bg-emerald-500\/10 { background-color: rgba(5, 150, 105, 0.1) !important; }
        .light-mode .border-emerald-500\/20 { border-color: rgba(5, 150, 105, 0.3) !important; }
        .light-mode .text-red-400 { color: #dc2626 !important; }
        .light-mode .bg-red-500\/10 { background-color: rgba(220, 38, 38, 0.1) !important; }
        .light-mode .border-red-500\/20 { border-color: rgba(220, 38, 38, 0.3) !important; }
        .light-mode .bg-slate-900 { background-color: #f1f5f9 !important; }
        .light-mode .from-glow-900\/40 { --tw-gradient-from: rgba(217, 70, 239, 0.1) !important; }
        .light-mode .to-slate-900 { --tw-gradient-to: rgba(255, 255, 255, 1) !important; }
        .light-mode [style*="rgba(10,10,15,0.95)"] { background: linear-gradient(to right, rgba(255,255,255,0.95) 40%, rgba(255,255,255,0.3)) !important; }
        .light-mode ::-webkit-scrollbar-track { background: #f1f5f9; }

        /* Light Mode Custom Header (Nav) Overrides */
        .light-mode nav.glass-dark {
            background: linear-gradient(135deg, #d946ef 0%, #a21caf 100%) !important;
            border-bottom: none !important;
            box-shadow: 0 10px 40px rgba(217, 70, 239, 0.3) !important;
        }
        .light-mode nav.glass-dark .text-slate-400 { color: rgba(255,255,255,0.85) !important; }
        .light-mode nav.glass-dark .text-slate-400:hover { color: #ffffff !important; }
        .light-mode nav.glass-dark .glow-text, .light-mode nav.glass-dark .text-white { 
            -webkit-text-fill-color: #ffffff !important;
            color: #ffffff !important; 
            text-shadow: 0 2px 10px rgba(255,255,255,0.2) !important;
        }
        .light-mode nav.glass-dark .glow-gradient { background: rgba(255,255,255,0.2) !important; border: 1px solid rgba(255,255,255,0.4) !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased transition-colors duration-500"
      x-data="{ theme: localStorage.getItem('theme') || 'dark' }"
      :class="theme === 'light' ? 'light-mode' : ''"
      x-init="$watch('theme', val => localStorage.setItem('theme', val))">

    <!-- Ambient background orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full opacity-20" style="background: radial-gradient(circle, #d946ef, transparent 70%);"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full opacity-20" style="background: radial-gradient(circle, #7c3aed, transparent 70%);"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-5" style="background: radial-gradient(circle, #2563eb, transparent 70%);"></div>
    </div>

    <!-- Navbar -->
    <nav class="glass-dark sticky top-0 z-50 border-b border-white/8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl glow-gradient flex items-center justify-center pulse-glow">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2s-4 2-4 8c0 4 4 12 4 12s4-8 4-12c0-6-4-8-4-8z"/>
                            <path d="M14 4s4 4 4 8c0 3-2 6-6 10"/>
                            <path d="M10 4s-4 4-4 8c0 3 2 6 6 10"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight glow-text">GlowDesk</span>
                </a>

                <!-- Nav links -->
                <div class="flex items-center gap-2">
                    <!-- Shopping Cart -->
                    <div class="relative z-50 flex items-center mr-2">
                        <button @click="$store.cart.open = !$store.cart.open" @click.away="$store.cart.open = false" class="relative p-2 text-slate-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="hidden sm:inline font-bold ml-1 text-sm">Cart</span>
                            <span x-show="$store.cart.count > 0" x-transition x-text="$store.cart.count" class="absolute top-0 right-0 sm:right-6 -mt-1 -mr-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-[#0f0f14] shadow-md"></span>
                        </button>
                        
                        <!-- Dropdown -->
                        <div x-show="$store.cart.open" x-transition.opacity.duration.200ms style="display: none;" class="absolute right-0 top-full mt-3 w-[85vw] max-w-[320px] sm:w-[22rem] bg-[#0f0f14] border border-[#7c3aed]/50 shadow-[0_10px_40px_rgba(124,58,237,0.3)] rounded-2xl overflow-hidden backdrop-blur-xl z-[90]">
                            <div class="bg-[#1a1a24]/90 p-3 sm:p-4 border-b border-white/5">
                                <h3 class="font-bold text-white uppercase tracking-wider text-xs sm:text-sm flex justify-between items-center">
                                    Your Cart
                                    <span class="text-[#f0abfc] text-xs bg-[#7c3aed]/20 px-2 py-0.5 rounded-full" x-text="$store.cart.count + ' items'"></span>
                                </h3>
                            </div>
                            
                            <div class="max-h-60 overflow-y-auto px-4 py-2 custom-scrollbar">
                                <template x-if="$store.cart.items.length === 0">
                                    <div class="py-8 text-center text-slate-500 text-sm">
                                        Your cart is completely empty.
                                    </div>
                                </template>
                                <template x-for="item in $store.cart.items" :key="item.id">
                                    <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-0 relative group">
                                        <div class="flex items-center gap-3 w-3/4">
                                            <div class="w-10 h-10 rounded-lg bg-[#1a1a24] overflow-hidden flex-shrink-0 flex items-center justify-center border border-white/5">
                                                <template x-if="item.image_url"><img :src="item.image_url" class="w-full h-full object-cover"></template>
                                                <template x-if="!item.image_url"><span class="text-xl">✨</span></template>
                                            </div>
                                            <div class="flex flex-col flex-1 pl-1">
                                                <span class="text-white font-semibold text-sm leading-tight line-clamp-1" x-text="item.name"></span>
                                                <span class="text-slate-400 text-xs mt-0.5" x-text="item.quantity + ' × Br ' + parseFloat(item.price).toFixed(2)"></span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button @click="$store.cart.remove(item.id)" class="w-6 h-6 rounded-full flex items-center justify-center text-slate-500 hover:bg-red-500/20 hover:text-red-400 transition-colors" title="Remove">×</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <template x-if="$store.cart.items.length > 0">
                                <div class="bg-[#1a1a24]/90 p-5 border-t border-white/5 space-y-4">
                                    <div class="flex justify-between items-center font-bold text-white mb-2">
                                        <span class="text-slate-400 text-sm">Total</span>
                                        <span class="text-[#f0abfc] text-xl" x-text="'Br ' + $store.cart.total.toFixed(2)"></span>
                                    </div>
                                    <button @click="$dispatch('open-cart-checkout'); $store.cart.open = false" class="w-full bg-gradient-to-r from-[#d946ef] to-[#7c3aed] text-white hover:brightness-110 py-3 rounded-xl font-bold transition-all shadow-[0_4px_15px_rgba(217,70,239,0.4)] hover:-translate-y-0.5">
                                        Proceed to Checkout
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Theme Toggle -->
                    <button @click="theme = theme === 'dark' ? 'light' : 'dark'" class="px-2 py-2 text-slate-400 hover:text-glow-400 transition-colors mr-2">
                        <!-- Sun Icon for Dark Mode -->
                        <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <!-- Moon Icon for Light Mode -->
                        <svg x-show="theme === 'light'" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>

                    @if(request()->is('admin*'))
                        <a href="{{ route('admin.orders') }}" class="px-3 py-2 text-sm font-medium text-slate-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">Orders</a>
                        <a href="{{ route('admin.products') }}" class="px-3 py-2 text-sm font-medium text-slate-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">Products</a>
                        <a href="{{ route('admin.products.create') }}" class="px-3 py-2 text-sm font-medium text-slate-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">+ Add Product</a>
                        @if(session('admin_logged_in'))
                            <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="ml-2 px-3 py-2 text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-all border border-red-500/20">Logout</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages (Toasts) -->
    <div class="fixed top-20 right-4 z-[100] flex flex-col gap-3 max-w-sm pointer-events-none">
        @if(session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto glass border border-emerald-500/30 text-emerald-400 px-4 py-4 rounded-2xl flex items-start gap-4 shadow-2xl" style="background: rgba(16,185,129,0.05); box-shadow: 0 10px 40px rgba(52,211,153,0.15)">
                <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-white mb-0.5">Success!</h4>
                    <p class="text-xs text-emerald-400/80 leading-relaxed">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-emerald-400/50 hover:text-white transition flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 7000)" x-show="show"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto glass border border-red-500/30 text-red-400 px-4 py-4 rounded-2xl flex items-start gap-4 shadow-2xl" style="background: rgba(239,68,68,0.05); box-shadow: 0 10px 40px rgba(239,68,68,0.15)">
                <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold mb-1">Notice</h4>
                    <p class="text-xs opacity-90 leading-relaxed">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-red-400/50 hover:text-red-400 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto glass border border-red-500/30 text-red-400 px-4 py-4 rounded-2xl flex items-start gap-4 shadow-2xl" style="background: rgba(239,68,68,0.05); box-shadow: 0 10px 40px rgba(239,68,68,0.15)">
                <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold mb-1">Please check your inputs</h4>
                    <ul class="text-xs opacity-90 leading-relaxed list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-red-400/50 hover:text-red-400 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        @endif
    </div>

    <main class="flex-grow relative z-10">
        @yield('content')
    </main>

    <footer class="relative z-10 border-t border-white/5 mt-20 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            {{-- Secret: click logo 3 times to access admin --}}
            <div class="flex items-center gap-2 cursor-pointer select-none"
                 x-data="{ clicks: 0, timer: null }"
                 @click="
                    clicks++;
                    clearTimeout(timer);
                    timer = setTimeout(() => clicks = 0, 2000);
                    if (clicks >= 3) { window.location.href = '{{ route('admin.dashboard') }}'; }
                 ">
                <div class="w-6 h-6 rounded-lg glow-gradient flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2s-4 2-4 8c0 4 4 12 4 12s4-8 4-12c0-6-4-8-4-8z"/>
                        <path d="M14 4s4 4 4 8c0 3-2 6-6 10"/>
                        <path d="M10 4s-4 4-4 8c0 3 2 6 6 10"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold glow-text">GlowDesk</span>
            </div>
            <p class="text-xs text-slate-600">&copy; {{ date('Y') }} GlowDesk Internal System. All rights reserved.</p>
        </div>
    <!-- GlowBot Chat Widget -->
    <div x-data="glowbot()" class="fixed bottom-6 right-6 z-[100] flex flex-col items-end">
        <!-- Chat Window -->
        <div x-show="open" x-transition.opacity.scale.origin.bottom.right 
             :class="isMaximized ? '!fixed !inset-0 sm:!inset-10 !w-auto !h-auto !mb-0 z-[110] sm:rounded-2xl rounded-none' : 'mb-4 w-[350px] sm:w-[400px] h-[500px] rounded-2xl'"
             class="bg-[#0f0f14] border border-[#7c3aed]/50 shadow-[0_10px_40px_rgba(124,58,237,0.3)] flex flex-col overflow-hidden transition-all duration-300" 
             style="display: none;">
            <!-- Header -->
            <div class="bg-[#1a1a24]/90 p-4 border-b border-white/5 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-[#d946ef] to-[#7c3aed] flex items-center justify-center text-white relative shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-[#0f0f14] rounded-full"></span>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-sm flex items-center gap-2">GlowBot <span class="bg-[#7c3aed]/20 text-[#f0abfc] text-[9px] px-1.5 py-0.5 rounded uppercase tracking-wider">AI</span></h3>
                        <p class="text-[10px] text-glow-400 font-medium">Online | Ready to assist</p>
                    </div>
                </div>
                <div class="flex gap-1">
                    <button @click="isMaximized = !isMaximized" class="text-slate-400 hover:text-white transition w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/10">
                        <svg x-show="!isMaximized" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        <svg x-show="isMaximized" style="display: none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h4v4m0-4l-5 5m16-9h-4V4m0 4l5-5"/></svg>
                    </button>
                    <button @click="open = false; isMaximized = false" class="text-slate-400 hover:text-white transition w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/10"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            </div>
            
            <!-- Messages Array -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar bg-[#0f0f14]" id="chatbox">
                <template x-for="(msg, index) in messages" :key="index">
                    <div :class="msg.role === 'model' ? 'justify-start' : 'justify-end'" class="flex">
                        <div :class="msg.role === 'model' ? 'bg-[#1a1a24] text-slate-200 border border-white/5 rounded-tr-xl' : 'bg-gradient-to-r from-[#d946ef] to-[#7c3aed] text-white rounded-tl-xl shadow-[0_4px_15px_rgba(217,70,239,0.3)]'" 
                             class="max-w-[85%] rounded-b-xl px-4 py-2.5 text-sm leading-relaxed whitespace-pre-wrap break-words border-t border-white/5" x-text="msg.text"></div>
                    </div>
                </template>
                <template x-if="isLoading">
                    <div class="flex justify-start">
                        <div class="bg-[#1a1a24] text-slate-400 border border-white/5 rounded-tr-xl rounded-b-xl px-4 py-3 text-sm flex gap-1">
                            <span class="animate-bounce inline-block w-1.5 h-1.5 bg-[#7c3aed] rounded-full shadow-[0_0_5px_rgba(124,58,237,0.5)]"></span>
                            <span class="animate-bounce inline-block w-1.5 h-1.5 bg-[#7c3aed] rounded-full shadow-[0_0_5px_rgba(124,58,237,0.5)]" style="animation-delay: 0.2s"></span>
                            <span class="animate-bounce inline-block w-1.5 h-1.5 bg-[#7c3aed] rounded-full shadow-[0_0_5px_rgba(124,58,237,0.5)]" style="animation-delay: 0.4s"></span>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- Quick Chips -->
            <div x-show="messages.length === 1 && !isLoading" class="px-4 py-2.5 flex gap-2 overflow-x-auto custom-scrollbar border-t border-white/5 bg-[#0f0f14]">
                <button @click="send('Best products for oily skin?')" class="whitespace-nowrap flex-shrink-0 bg-[#1a1a24] hover:bg-[#7c3aed]/30 border border-white/10 hover:border-[#7c3aed]/50 text-xs text-glow-300 font-medium px-3 py-1.5 rounded-full transition shadow-sm">Oily skin?</button>
                <button @click="send('Do you have SPF products?')" class="whitespace-nowrap flex-shrink-0 bg-[#1a1a24] hover:bg-[#7c3aed]/30 border border-white/10 hover:border-[#7c3aed]/50 text-xs text-glow-300 font-medium px-3 py-1.5 rounded-full transition shadow-sm">SPF products?</button>
                <button @click="send('What is currently on sale?')" class="whitespace-nowrap flex-shrink-0 bg-[#1a1a24] hover:bg-[#7c3aed]/30 border border-white/10 hover:border-[#7c3aed]/50 text-xs text-glow-300 font-medium px-3 py-1.5 rounded-full transition shadow-sm">On sale?</button>
            </div>
            
            <!-- Explicit Alternative UI Button for Custom Requests -->
            <div class="px-4 py-2.5 border-t border-white/5 bg-[#1a1a24]/90 flex items-center justify-center">
                <button type="button" @click="$dispatch('open-custom-request'); open = false; isMaximized = false" class="w-full py-2 bg-gradient-to-r from-white/5 to-white/10 hover:from-[#7c3aed]/20 hover:to-[#d946ef]/20 border border-white/10 rounded-xl text-glow-200 text-[11px] font-semibold flex items-center justify-center gap-2 transition shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Product not listed? Request a custom order!
                </button>
            </div>
            
            <!-- Input Area -->
            <form @submit.prevent="send()" class="p-3 bg-[#1a1a24]/90 border-t border-white/5 flex gap-2 items-center">
                <input x-model="input" type="text" placeholder="Ask GlowBot..." class="flex-1 bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-sm !text-white placeholder:text-slate-500 focus:outline-none focus:border-[#7c3aed] focus:ring-1 focus:ring-[#7c3aed] transition">
                <button type="submit" :disabled="isLoading || !input.trim()" class="bg-gradient-to-r from-[#d946ef] to-[#7c3aed] hover:brightness-110 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:brightness-100 !text-white w-12 h-12 flex items-center justify-center flex-shrink-0 rounded-xl transition shadow-[0_0_15px_rgba(124,58,237,0.4)]">
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        </div>
        
        <!-- Floating FAB -->
        <button @click="open = !open; if(open) hasUnread = false" 
                class="w-14 h-14 bg-gradient-to-r from-[#d946ef] to-[#7c3aed] rounded-full shadow-[0_4px_20px_rgba(124,58,237,0.5)] flex items-center justify-center text-white hover:-translate-y-1 hover:shadow-[0_8px_25px_rgba(124,58,237,0.6)] transition-all relative border border-white/10 z-[101]">
            <svg x-show="!open" class="w-7 h-7 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <svg x-show="open" style="display: none;" class="w-7 h-7 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span x-show="hasUnread && !open" class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-[#0f0f14] shadow-md z-10 transition transform scale-100 origin-bottom-left">1</span>
        </button>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('glowbot', () => ({
                open: false,
                isMaximized: false,
                hasUnread: true,
                isLoading: false,
                input: '',
                messages: [
                    { role: 'model', text: 'Hi! I\'m GlowBot 👋 How can I help you find your perfect routine today?' }
                ],
                async send(textOverride = null) {
                    const text = textOverride || this.input;
                    if (!text.trim() || this.isLoading) return;
                    
                    this.messages.push({ role: 'user', text: text.trim() });
                    if (!textOverride) this.input = '';
                    this.isLoading = true;
                    this.scrollToBottom();

                    try {
                        const response = await fetch('{{ route("api.chat") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                message: text.trim(),
                                history: this.messages.slice(0, -1)
                            })
                        });
                        
                        const data = await response.json();
                        let botReply = data.reply || 'I am sorry, I did not receive a response.';
                        
                        // Feature Hook: Automatically open the custom request modal if the AI triggers it
                        if (botReply.includes('[ACTION:OPEN_REQUEST]')) {
                            botReply = botReply.replace('[ACTION:OPEN_REQUEST]', '').trim();
                            window.dispatchEvent(new CustomEvent('open-custom-request'));
                            this.isMaximized = false;
                        }

                        // Intent Hook: Parse structure like INTENT: ORDER\nPRODUCT: Cleanser
                        if (botReply.trim().startsWith('INTENT:')) {
                            const lines = botReply.trim().split('\n');
                            const intentLine = lines.find(l => l.startsWith('INTENT:'));
                            const productLine = lines.find(l => l.startsWith('PRODUCT:'));
                            
                            if (intentLine && productLine) {
                                const intent = intentLine.replace('INTENT:', '').trim();
                                const productName = productLine.replace('PRODUCT:', '').trim();
                                
                                // Find product in global library
                                const product = (window.__glowProducts || []).find(p => p.name.toLowerCase().includes(productName.toLowerCase()));
                                
                                if (product) {
                                    window.dispatchEvent(new CustomEvent('open-modal', { detail: product }));
                                    this.isMaximized = false;
                                    
                                    // Make the chat message friendly instead of raw intent
                                    botReply = intent === 'ORDER' 
                                        ? `Great choice! I've opened the details for **${product.name}** so you can place your order. ✨`
                                        : `Certainly! Here is all the information about **${product.name}**. Let me know if you have more questions!`;
                                }
                            }
                        }
                        
                        this.messages.push({ role: 'model', text: botReply });
                    } catch (e) {
                        this.messages.push({ role: 'model', text: 'Hello! 👋 I am GlowBot. How can I help you find the right skincare product today?' });
                    }
                    
                    this.isLoading = false;
                    this.scrollToBottom();
                },
                scrollToBottom() {
                    setTimeout(() => {
                        const chatbox = document.getElementById('chatbox');
                        if(chatbox) chatbox.scrollTop = chatbox.scrollHeight;
                    }, 50);
                }
            }));

            Alpine.store('cart', {
                items: [],
                open: false,
                init() {
                    const saved = localStorage.getItem('glowdesk_cart');
                    if (saved) { this.items = JSON.parse(saved); }
                },
                add(product) {
                    let existing = this.items.find(i => i.id === product.id);
                    if (existing) existing.quantity++;
                    else this.items.push({...product, quantity: 1});
                    this.save();
                },
                remove(id) {
                    this.items = this.items.filter(i => i.id !== id);
                    if (this.items.length === 0) { this.open = false; }
                    this.save();
                },
                clear() {
                    this.items = [];
                    this.save();
                },
                save() { localStorage.setItem('glowdesk_cart', JSON.stringify(this.items)); },
                get count() { return this.items.reduce((c, i) => c + i.quantity, 0); },
                get total() { return this.items.reduce((t, i) => t + (i.price * i.quantity), 0); }
            });

            Alpine.store('telegram', {
                isTMA: false,
                user: null,
                initData: '',
                init() {
                    if (window.Telegram && window.Telegram.WebApp) {
                        const tg = window.Telegram.WebApp;
                        tg.ready();
                        tg.expand();
                        
                        this.initData = tg.initData || '';
                        const unsafeUser = tg.initDataUnsafe?.user;
                        
                        if (unsafeUser) {
                            this.isTMA = true;
                            this.user = {
                                id: unsafeUser.id,
                                firstName: unsafeUser.first_name || '',
                                lastName: unsafeUser.last_name || '',
                                name: ((unsafeUser.first_name || '') + ' ' + (unsafeUser.last_name || '')).trim(),
                                username: unsafeUser.username || '',
                            };
                            console.log('✨ Telegram WebApp User Loaded:', this.user);
                        }

                        if (tg.colorScheme) {
                            document.body.classList.toggle('light-mode', tg.colorScheme === 'light');
                        }
                    }
                }
            });
        });
    </script>
    @if(session('clear_cart'))
        <script>localStorage.removeItem('glowdesk_cart');</script>
    @endif
</body>
</html>
