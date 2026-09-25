<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/glowdesk-logo.png">
    <title>GlowAddis – @yield('title', 'Beauty Store')</title>
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Early Theme Init Script (Zero FOUC) -->
    <script>
        (function() {
            var saved = localStorage.getItem('glowaddis_theme') || localStorage.getItem('glowdesk_theme') || localStorage.getItem('theme');
            var theme = saved || 'dark';
            if (theme === 'light') {
                document.documentElement.classList.add('light-mode');
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.remove('light-mode');
                document.documentElement.classList.add('dark');
            }
        })();

        window.toggleGlowTheme = function() {
            var isCurrentlyDark = document.documentElement.classList.contains('dark');
            var nextTheme = isCurrentlyDark ? 'light' : 'dark';
            
            if (nextTheme === 'light') {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light-mode');
                if (document.body) {
                    document.body.classList.remove('dark');
                    document.body.classList.add('light-mode');
                }
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light-mode');
                if (document.body) {
                    document.body.classList.add('dark');
                    document.body.classList.remove('light-mode');
                }
            }
            
            localStorage.setItem('glowaddis_theme', nextTheme);
            localStorage.setItem('glowdesk_theme', nextTheme);
            localStorage.setItem('theme', nextTheme);

            if (window.Alpine && window.Alpine.store && window.Alpine.store('theme')) {
                window.Alpine.store('theme').current = nextTheme;
            }

            if (window.Telegram && window.Telegram.WebApp) {
                try {
                    window.Telegram.WebApp.setHeaderColor(nextTheme === 'light' ? '#faf8fc' : '#0d0d12');
                    window.Telegram.WebApp.setBackgroundColor(nextTheme === 'light' ? '#faf8fc' : '#0d0d12');
                } catch(e) {}
            }
        };
    </script>
    
    <!-- Tailwind Configuration (MUST be before Tailwind script for CDN) -->
    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = {
            darkMode: 'class',
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
        };
    </script>
    <!-- Telegram WebApp SDK -->
    <script defer src="https://telegram.org/js/telegram-web-app.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }

        /* ─── Base Theme ─────────────────────────────────────────── */
        html {
            background-color: #faf8fc;
            color-scheme: light;
            transition: background-color 0.2s ease;
            min-height: 100%;
        }
        html.dark {
            background-color: #0d0d12;
            color-scheme: dark;
        }

        body {
            background-color: inherit;  /* inherits from html — single source of truth */
            color: #0f172a;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        html.dark body {
            color: #f1f5f9 !important;
        }

        /* Global dark mode text fallback — catches any element that inherits color from body */
        html.dark h1, html.dark h2, html.dark h3, html.dark h4, html.dark h5, html.dark h6 {
            color: #f1f5f9;
        }
        html.dark p:not([class*="text-"]), html.dark span:not([class*="text-"]), html.dark label:not([class*="text-"]) {
            color: inherit;
        }


        /* Clean, controlled gradients (no neon color spill) */
        .glow-gradient {
            background: linear-gradient(135deg, #c026d3 0%, #7c3aed 100%);
        }
        .glow-text {
            background: linear-gradient(135deg, #a21caf, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glass styles - sharp, crisp, subtle depth */
        .glass {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.07);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .glass-dark {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        }

        /* Modern card hover — premium lift + shadow bloom */
        .card-hover {
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .card-hover:hover {
            transform: translateY(-5px) scale(1.015);
            box-shadow: 0 22px 44px -8px rgba(0, 0, 0, 0.13);
        }

        /* Premium gradient button with background-position shimmer */
        .btn-glow {
            background: linear-gradient(135deg, #c026d3 0%, #7c3aed 55%, #a21caf 100%);
            background-size: 200% 200%;
            background-position: 0% 50%;
            color: #ffffff !important;
            box-shadow: 0 2px 10px rgba(124, 58, 237, 0.28);
            transition: all 0.22s ease;
        }
        .btn-glow:hover {
            box-shadow: 0 6px 22px rgba(124, 58, 237, 0.48);
            transform: translateY(-1px);
            background-position: 100% 50%;
        }
        .btn-glow:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);
        }

        /* Crisp clean input fields */
        .input-field {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .input-field:focus {
            outline: none;
            border-color: #a21caf;
            box-shadow: 0 0 0 3px rgba(162, 28, 175, 0.12);
        }
        .input-field::placeholder { color: #94a3b8; }

        /* Subtle scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Ensure clean white text on gradient elements */
        button.text-white, a.text-white, .btn-glow, .glow-gradient, .btn-glow *, .glow-gradient * {
            color: #ffffff !important;
        }

        /* Light Mode navbar & dropdowns */
        nav.glass-dark {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }
        nav.glass-dark .text-slate-400 { color: #64748b; }
        nav.glass-dark .text-slate-400:hover { color: #0f172a; }
        nav.glass-dark .text-white { color: #0f172a; }

        .cart-dropdown {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.12);
        }
        .cart-dropdown .cart-header,
        .cart-dropdown .cart-footer {
            background: #f8fafc;
            border-color: rgba(0, 0, 0, 0.06);
        }
        .cart-dropdown .cart-header h3 { color: #0f172a; }
        .cart-dropdown .cart-item-name { color: #0f172a; }

        .chat-window {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.15);
        }
        .chat-header {
            background: #f8fafc;
            border-color: rgba(0, 0, 0, 0.06);
        }
        .chat-header h3 { color: #0f172a; }
        .chat-messages { background: #faf8fc; }
        .chat-model-msg {
            background: #ffffff;
            color: #1e293b;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .chat-chips, .chat-custom-req-bar, .chat-input-bar {
            background: #ffffff;
            border-color: rgba(0, 0, 0, 0.06);
        }
        .chat-chips button {
            background: #f3f0f7;
            color: #7c3aed;
            border: 1px solid rgba(124, 58, 237, 0.15);
        }
        .chat-chips button:hover {
            background: #ede9fe;
        }
        .chat-input-field {
            background: #f8fafc;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }

        /* ========================================================
           DARK MODE: Sleek, Crisp, Apple-Style (NO NEON GLOW)
           ======================================================== */

        html.dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }
        html.dark .glow-text {
            background: linear-gradient(135deg, #f0abfc, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        html.dark .glass {
            background: rgba(22, 22, 30, 0.75) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3) !important;
        }
        html.dark .glass-dark {
            background: rgba(18, 18, 26, 0.85) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
        }
        html.dark .card-hover,
        .dark .card-hover {
            background: #14141e !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        html.dark .card-hover h3,
        .dark .card-hover h3 {
            color: #ffffff !important;
        }
        html.dark .card-hover p,
        .dark .card-hover p {
            color: #94a3b8 !important;
        }
        html.dark .card-hover:hover {
            box-shadow: 0 24px 48px -8px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(124, 58, 237, 0.15) !important;
        }
        html.dark .input-field {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #f1f5f9 !important;
        }
        html.dark .input-field:focus {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: #c026d3 !important;
            box-shadow: 0 0 0 3px rgba(192, 38, 211, 0.2) !important;
        }
        html.dark .input-field::placeholder { color: #64748b !important; }

        html.dark nav.glass-dark {
            background: rgba(13, 13, 18, 0.82) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4) !important;
        }
        html.dark nav.glass-dark .text-white { color: #ffffff !important; }
        html.dark nav.glass-dark .text-slate-400 { color: #94a3b8 !important; }
        html.dark nav.glass-dark .text-slate-400:hover { color: #ffffff !important; }

        html.dark .cart-dropdown {
            background: #14141e !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5) !important;
        }
        html.dark .cart-dropdown .cart-header,
        html.dark .cart-dropdown .cart-footer {
            background: #191926 !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        html.dark .cart-dropdown .cart-header h3 { color: #ffffff !important; }
        html.dark .cart-dropdown .cart-item-name { color: #f1f5f9 !important; }

        html.dark .chat-window {
            background: #14141e !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5) !important;
        }
        html.dark .chat-header {
            background: #191926 !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        html.dark .chat-header h3 { color: #ffffff !important; }
        html.dark .chat-messages { background: #0f0f16 !important; }
        html.dark .chat-model-msg {
            background: #191926 !important;
            color: #e2e8f0 !important;
            border: 1px solid rgba(255, 255, 255, 0.07) !important;
        }
        html.dark .chat-chips, html.dark .chat-custom-req-bar, html.dark .chat-input-bar {
            background: #14141e !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        html.dark .chat-chips button {
            background: rgba(255, 255, 255, 0.06) !important;
            color: #d8b4fe !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        html.dark .chat-chips button:hover {
            background: rgba(255, 255, 255, 0.1) !important;
        }
        html.dark .chat-input-field {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* ═══════════════════════════════════════════
           MODERNIZATION v2 — New Component Styles
           ═══════════════════════════════════════════ */

        /* Hero slideshow — rich purple gradient, works in any theme */
        .hero-slideshow {
            background: linear-gradient(135deg, #3b0764 0%, #4c1d95 30%, #312e81 65%, #6b21a8 100%);
        }

        /* Product card glass overlay — bottom fade */
        .product-card-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.58) 48%, transparent 100%);
        }

        /* Category / sort chip — active glowing state */
        .chip-active {
            background: linear-gradient(135deg, #c026d3 0%, #7c3aed 100%);
            color: #ffffff !important;
            box-shadow: 0 2px 16px rgba(124, 58, 237, 0.45);
            border-color: transparent !important;
        }

        /* Search + sort container — better glass */
        .sort-glass {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            border: 1.5px solid rgba(0, 0, 0, 0.07);
            border-radius: 1rem;
            box-shadow: 0 2px 14px rgba(0, 0, 0, 0.05);
        }
        html.dark .sort-glass {
            background: rgba(18, 18, 26, 0.82);
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 2px 14px rgba(0, 0, 0, 0.32);
        }

        /* Featured Products heading underline accent */
        .section-heading-accent {
            width: 2.5rem;
            height: 3px;
            background: linear-gradient(90deg, #c026d3, #7c3aed);
            border-radius: 9999px;
            margin-top: 0.35rem;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased transition-colors duration-200" x-data>

    <!-- Navbar -->
    <nav class="glass-dark sticky top-0 z-50 border-b border-black/5 dark:border-white/8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl glow-gradient flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2s-4 2-4 8c0 4 4 12 4 12s4-8 4-12c0-6-4-8-4-8z"/>
                            <path d="M14 4s4 4 4 8c0 3-2 6-6 10"/>
                            <path d="M10 4s-4 4-4 8c0 3 2 6 6 10"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight glow-text">GlowAddis</span>
                </a>

                <!-- Nav links -->
                <div class="flex items-center gap-2">
                    <!-- Shopping Cart -->
                    <div class="relative z-50 flex items-center mr-2" x-data @keydown.escape.window="$store.cart.open = false">
                        <button @click="$store.cart.open = !$store.cart.open" class="relative p-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="hidden sm:inline font-bold ml-1 text-sm">Cart</span>
                            <span x-show="$store.cart.count > 0" x-transition x-text="$store.cart.count" class="absolute top-0 right-0 sm:right-6 -mt-1 -mr-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border-2 border-white dark:border-[#0d0d12] shadow-md"></span>
                        </button>

                        <!-- Click-away overlay (transparent) -->
                        <div x-show="$store.cart.open" x-cloak @click="$store.cart.open = false" class="fixed inset-0 z-[89]" aria-hidden="true"></div>
                        
                        <!-- Dropdown -->
                        <div x-show="$store.cart.open" x-cloak x-transition.opacity.duration.200ms class="cart-dropdown absolute right-0 top-full mt-3 w-[85vw] max-w-[320px] sm:w-[22rem] rounded-2xl overflow-hidden z-[90]">
                            <div class="cart-header p-3 sm:p-4 border-b">
                                <h3 class="font-bold uppercase tracking-wider text-xs sm:text-sm flex justify-between items-center text-slate-900 dark:text-white">
                                    Your Cart
                                    <span class="text-purple-700 dark:text-purple-300 text-xs bg-purple-100 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800/40 px-2 py-0.5 rounded-full font-semibold" x-text="$store.cart.count + ' items'"></span>
                                </h3>
                            </div>
                            
                            <div class="max-h-60 overflow-y-auto px-4 py-2 custom-scrollbar">
                                <template x-if="$store.cart.items.length === 0">
                                    <div class="py-8 text-center text-slate-500 text-sm">
                                        Your cart is completely empty.
                                    </div>
                                </template>
                                <template x-for="item in $store.cart.items" :key="item.id">
                                    <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-white/5 last:border-0 relative group">
                                        <div class="flex items-center gap-3 w-3/4">
                                            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-white/5 overflow-hidden flex-shrink-0 flex items-center justify-center border border-slate-200/80 dark:border-white/10">
                                                <template x-if="item.image_url"><img :src="item.image_url" class="w-full h-full object-cover"></template>
                                                <template x-if="!item.image_url"><span class="text-xl">✨</span></template>
                                            </div>
                                            <div class="flex flex-col flex-1 pl-1">
                                                <span class="cart-item-name font-semibold text-sm leading-tight line-clamp-1" x-text="item.name"></span>
                                                <span class="text-slate-500 dark:text-slate-400 text-xs mt-0.5" x-text="item.quantity + ' × Br ' + parseFloat(item.price).toFixed(2)"></span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button @click="$store.cart.remove(item.id)" class="w-6 h-6 rounded-full flex items-center justify-center text-slate-400 hover:bg-red-500/10 hover:text-red-500 transition-colors" title="Remove">×</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <template x-if="$store.cart.items.length > 0">
                                <div class="cart-footer p-4 sm:p-5 border-t space-y-3">
                                    <div class="flex justify-between items-center font-bold text-slate-900 dark:text-white mb-2">
                                        <span class="text-slate-500 text-sm">Total</span>
                                        <span class="text-purple-600 dark:text-purple-300 text-xl" x-text="'Br ' + $store.cart.total.toFixed(2)"></span>
                                    </div>
                                    <button @click="window.dispatchEvent(new CustomEvent('open-cart-checkout')); $dispatch('open-cart-checkout'); $store.cart.open = false" class="w-full btn-glow py-3 rounded-xl font-bold transition-all shadow-sm">
                                        Proceed to Checkout
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Theme Toggle Pill -->
                    <button @click="$store.theme.toggle()" type="button"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-200/80 dark:bg-white/10 border border-slate-300/80 dark:border-white/10 text-slate-700 dark:text-slate-200 hover:bg-slate-300/80 dark:hover:bg-white/15 transition-all mr-2 text-xs font-semibold shadow-xs cursor-pointer select-none"
                        :title="$store.theme.current === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                        <span class="inline-flex items-center" x-show="$store.theme.current === 'dark'">
                            <svg class="w-3.5 h-3.5 text-amber-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span class="hidden sm:inline">Light</span>
                        </span>
                        <span class="inline-flex items-center" x-show="$store.theme.current !== 'dark'">
                            <svg class="w-3.5 h-3.5 text-indigo-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <span class="hidden sm:inline">Dark</span>
                        </span>
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

        @if(isset($errors) && $errors->any())
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

    <footer class="relative z-10 border-t border-slate-200/80 dark:border-white/5 mt-20 py-8">
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
                <div class="w-6 h-6 rounded-lg glow-gradient flex items-center justify-center shadow-sm">
                    <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2s-4 2-4 8c0 4 4 12 4 12s4-8 4-12c0-6-4-8-4-8z"/>
                        <path d="M14 4s4 4 4 8c0 3-2 6-6 10"/>
                        <path d="M10 4s-4 4-4 8c0 3 2 6 6 10"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold glow-text">GlowAddis</span>
            </div>
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} GlowAddis. All rights reserved.</p>
        </div>
    </footer>

    <!-- GlowBot Chat Widget -->
    <div x-data="glowbot()" class="fixed bottom-6 right-6 z-[100] flex flex-col items-end">
        <!-- Chat Window -->
        <div x-show="open" x-cloak x-transition.opacity.scale.origin.bottom.right 
             :class="isMaximized ? '!fixed !inset-0 sm:!inset-10 !w-auto !h-auto !mb-0 z-[110] sm:rounded-2xl rounded-none' : 'mb-4 w-[350px] sm:w-[400px] h-[500px] rounded-2xl'"
             class="chat-window flex flex-col overflow-hidden transition-all duration-300">
            <!-- Header -->
            <div class="chat-header p-4 border-b flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full glow-gradient flex items-center justify-center text-white relative shadow-xs">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white dark:border-[#0d0d12] rounded-full"></span>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm flex items-center gap-2">GlowBot <span class="bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/40 text-[9px] px-1.5 py-0.5 rounded font-semibold uppercase tracking-wider">AI</span></h3>
                        <p class="text-[10px] text-purple-600 dark:text-purple-400 font-medium">Online | Ready to assist</p>
                    </div>
                </div>
                <div class="flex gap-1">
                    <button @click="isMaximized = !isMaximized" class="text-slate-400 hover:text-slate-700 dark:hover:text-white transition w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-white/10">
                        <svg x-show="!isMaximized" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        <svg x-show="isMaximized" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h4v4m0-4l-5 5m16-9h-4V4m0 4l5-5"/></svg>
                    </button>
                    <button @click="open = false; isMaximized = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white transition w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-white/10"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            </div>
            
            <!-- Messages Array -->
            <div class="chat-messages flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar" id="chatbox">
                <template x-for="(msg, index) in messages" :key="index">
                    <div :class="msg.role === 'model' ? 'justify-start' : 'justify-end'" class="flex">
                        <div :class="msg.role === 'model' ? 'chat-model-msg rounded-tr-xl' : 'btn-glow text-white rounded-tl-xl shadow-xs'" 
                             class="max-w-[85%] rounded-b-xl px-4 py-2.5 text-sm leading-relaxed whitespace-pre-wrap break-words" x-text="msg.text"></div>
                    </div>
                </template>
                <template x-if="isLoading">
                    <div class="flex justify-start">
                        <div class="chat-model-msg rounded-tr-xl rounded-b-xl px-4 py-3 text-sm flex gap-1.5 items-center">
                            <span class="animate-bounce inline-block w-1.5 h-1.5 bg-purple-600 rounded-full"></span>
                            <span class="animate-bounce inline-block w-1.5 h-1.5 bg-purple-600 rounded-full" style="animation-delay: 0.2s"></span>
                            <span class="animate-bounce inline-block w-1.5 h-1.5 bg-purple-600 rounded-full" style="animation-delay: 0.4s"></span>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- Quick Chips -->
            <div x-show="messages.length === 1 && !isLoading" class="chat-chips px-4 py-2 flex gap-2 overflow-x-auto custom-scrollbar border-t">
                <button @click="send('Best products for oily skin?')" class="whitespace-nowrap flex-shrink-0 text-xs font-medium px-3 py-1.5 rounded-full transition">Oily skin?</button>
                <button @click="send('Do you have SPF products?')" class="whitespace-nowrap flex-shrink-0 text-xs font-medium px-3 py-1.5 rounded-full transition">SPF products?</button>
                <button @click="send('I want to make a custom order')" class="whitespace-nowrap flex-shrink-0 text-xs font-medium px-3 py-1.5 rounded-full transition">Custom order?</button>
            </div>
            
            <!-- Explicit Alternative UI Button for Custom Order -->
            <div class="chat-custom-req-bar px-4 py-2.5 border-t flex items-center justify-center">
                <button type="button" @click="$dispatch('open-custom-request'); open = false; isMaximized = false" class="w-full py-2 bg-purple-50 dark:bg-white/5 hover:bg-purple-100 dark:hover:bg-white/10 border border-purple-200 dark:border-white/10 rounded-xl text-purple-700 dark:text-purple-300 text-[11px] font-semibold flex items-center justify-center gap-2 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Product not listed? Request a Custom Order!
                </button>
            </div>
            
            <!-- Input Area -->
            <form @submit.prevent="send()" class="chat-input-bar p-3 border-t flex gap-2 items-center">
                <input x-model="input" type="text" placeholder="Ask GlowBot..." class="chat-input-field flex-1 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-600 transition">
                <button type="submit" :disabled="isLoading || !input.trim()" class="btn-glow disabled:opacity-40 disabled:cursor-not-allowed !text-white w-10 h-10 flex items-center justify-center flex-shrink-0 rounded-xl transition shadow-xs">
                    <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        </div>
        
        <!-- Floating FAB -->
        <button @click="open = !open; if(open) hasUnread = false" 
                class="relative w-14 h-14 rounded-full shadow-lg flex items-center justify-center text-white hover:scale-105 transition-all z-[101] group">
            <!-- Ring pulse -->
            <span class="absolute inset-0 rounded-full bg-gradient-to-br from-purple-600 to-fuchsia-500 animate-ping opacity-20 group-hover:opacity-30"></span>
            <!-- Solid circle -->
            <span class="absolute inset-0 rounded-full bg-gradient-to-br from-purple-600 to-fuchsia-500 shadow-lg"></span>
            <svg x-show="!open" class="w-6 h-6 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <svg x-show="open" x-cloak class="w-6 h-6 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span x-show="hasUnread && !open" class="absolute top-0 right-0 w-3.5 h-3.5 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-[#0d0d12] shadow-sm z-10"></span>
        </button>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                current: (function() {
                    return localStorage.getItem('glowaddis_theme') || localStorage.getItem('glowdesk_theme') || localStorage.getItem('theme') || 'dark';
                })(),
                init() {
                    this.apply();
                },
                toggle() {
                    this.current = this.current === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('glowaddis_theme', this.current);
                    localStorage.setItem('glowdesk_theme', this.current);
                    localStorage.setItem('theme', this.current);
                    this.apply();
                },
                set(val) {
                    this.current = val;
                    localStorage.setItem('glowaddis_theme', val);
                    localStorage.setItem('glowdesk_theme', val);
                    localStorage.setItem('theme', val);
                    this.apply();
                },
                apply() {
                    const isLight = this.current === 'light';
                    document.documentElement.classList.toggle('light-mode', isLight);
                    document.documentElement.classList.toggle('dark', !isLight);
                    if (document.body) {
                        document.body.classList.toggle('light-mode', isLight);
                        document.body.classList.toggle('dark', !isLight);
                    }
                    if (window.Telegram && window.Telegram.WebApp) {
                        const tg = window.Telegram.WebApp;
                        try {
                            if (tg.setHeaderColor) tg.setHeaderColor(isLight ? '#faf8fc' : '#0d0d12');
                            if (tg.setBackgroundColor) tg.setBackgroundColor(isLight ? '#faf8fc' : '#0d0d12');
                        } catch(e) {}
                    }
                }
            });

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
                    const saved = localStorage.getItem('glowaddis_cart') || localStorage.getItem('glowdesk_cart');
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
                save() { 
                    localStorage.setItem('glowaddis_cart', JSON.stringify(this.items)); 
                    localStorage.setItem('glowdesk_cart', JSON.stringify(this.items)); 
                },
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

                            // Only sync theme with Telegram if inside TMA AND user has not set a local preference
                            const hasManualTheme = localStorage.getItem('glowaddis_theme') || localStorage.getItem('glowdesk_theme') || localStorage.getItem('theme');
                            if (!hasManualTheme && tg.colorScheme) {
                                Alpine.store('theme').set(tg.colorScheme);
                            }
                        }
                    }
                }
            });
        });
    </script>
    @if(session('clear_cart'))
        <script>
            localStorage.removeItem('glowaddis_cart');
            localStorage.removeItem('glowdesk_cart');
        </script>
    @endif
</body>
</html>
