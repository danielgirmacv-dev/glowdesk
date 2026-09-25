@extends('layouts.app')

@section('title', 'Orders Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="ordersManager()">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-10">
        <div>
            <p class="text-glow-400 text-xs font-semibold uppercase tracking-widest mb-1">Admin Panel</p>
            <h1 class="text-3xl font-extrabold text-white">Orders</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $orders->count() }} total order{{ $orders->count() !== 1 ? 's' : '' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Status filter pills -->
            <div class="flex items-center gap-2 text-xs font-semibold">
                <span class="px-3 py-1.5 rounded-full bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">
                    {{ $orders->where('status','pending')->count() }} Pending
                </span>
                <span class="px-3 py-1.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                    {{ $orders->where('status','processing')->count() }} Processing
                </span>
                <span class="px-3 py-1.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    {{ $orders->where('status','delivered')->count() }} Delivered
                </span>
            </div>
        </div>
    </div>

    <!-- Toast notification -->
    <div x-show="toast.show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display:none;"
         class="fixed bottom-6 right-6 z-50 glass-dark rounded-xl px-5 py-3 flex items-center gap-3 shadow-xl"
         :class="toast.success ? 'border border-emerald-500/30' : 'border border-red-500/30'">
        <span :class="toast.success ? 'text-emerald-400' : 'text-red-400'" class="text-sm font-semibold" x-text="toast.message"></span>
    </div>

    <!-- Orders Table -->
    @if($orders->count() > 0)
    <div class="glass-dark rounded-2xl overflow-hidden" style="border: 1px solid rgba(255,255,255,0.07);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($orders as $order)
                    <tr class="hover:bg-white/[0.02] transition-colors group" id="order-row-{{ $order->id }}">

                        <!-- Order ID & Time -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-glow-400 text-xs font-bold font-mono">#GD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-slate-600 text-xs mt-0.5">{{ $order->created_at->diffForHumans() }}</p>
                        </td>

                        <!-- Customer -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg glow-gradient flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $order->customer_name }}</p>
                                    <p class="text-slate-500 text-xs">{{ $order->phone }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Products -->
                        <td class="px-6 py-4">
                            @foreach($order->items as $item)
                            <p class="text-slate-300 text-xs">{{ $item->product->name ?? '—' }}</p>
                            @endforeach
                        </td>

                        <!-- Quantity -->
                        <td class="px-6 py-4">
                            @foreach($order->items as $item)
                            <p class="text-white font-semibold text-center w-8 h-8 rounded-lg glass flex items-center justify-center">{{ $item->quantity }}</p>
                            @endforeach
                        </td>

                        <!-- Department -->
                        <td class="px-6 py-4">
                            <span class="text-slate-400 text-xs">{{ $order->department }}</span>
                        </td>

                        <!-- Status Dropdown (AJAX) -->
                        <td class="px-6 py-4">
                            <select
                                onchange="updateStatus({{ $order->id }}, this.value, this)"
                                class="status-select text-xs font-semibold rounded-full px-3 py-1.5 border cursor-pointer appearance-none focus:outline-none transition-all"
                                data-status="{{ $order->status }}"
                                style="background: transparent;">
                                <option value="pending"     {{ $order->status === 'pending'     ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="processing"  {{ $order->status === 'processing'  ? 'selected' : '' }}>🔄 Processing</option>
                                <option value="delivered"   {{ $order->status === 'delivered'   ? 'selected' : '' }}>✅ Delivered</option>
                            </select>
                        </td>

                        <!-- Action Buttons -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <!-- View Modal -->
                                <button
                                    @click="openModal({{ $order->toJson() }})"
                                    title="View Details"
                                    class="w-8 h-8 rounded-lg glass hover:bg-white/10 border border-white/10 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>

                                <!-- Mark Delivered -->
                                <button
                                    onclick="updateStatus({{ $order->id }}, 'delivered', null)"
                                    title="Mark as Delivered"
                                    class="w-8 h-8 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center text-emerald-400 hover:text-emerald-300 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>

                                <!-- Call -->
                                <a href="tel:{{ $order->phone }}"
                                   title="Call Customer"
                                   class="w-8 h-8 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/20 flex items-center justify-center text-blue-400 hover:text-blue-300 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </a>

                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $order->phone);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '251' . substr($cleanPhone, 1);
                                    } elseif (strlen($cleanPhone) == 9) {
                                        $cleanPhone = '251' . $cleanPhone;
                                    }
                                @endphp
                                <!-- WhatsApp -->
                                <a href="https://wa.me/{{ $cleanPhone }}"
                                   target="_blank"
                                   title="WhatsApp"
                                   class="w-8 h-8 rounded-lg bg-green-500/10 hover:bg-green-500/20 border border-green-500/20 flex items-center justify-center text-green-400 hover:text-green-300 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>

                                <!-- Telegram -->
                                <a href="https://t.me/+{{ $cleanPhone }}"
                                   target="_blank"
                                   title="Telegram"
                                   class="w-8 h-8 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 border border-sky-500/20 flex items-center justify-center text-sky-400 hover:text-sky-300 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.244-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                </a>

                                <!-- Delete -->
                                <button
                                    onclick="deleteOrder({{ $order->id }})"
                                    title="Delete Order"
                                    class="w-8 h-8 rounded-lg bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 flex items-center justify-center text-red-400 hover:text-red-300 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="py-24 text-center glass-dark rounded-2xl" style="border: 1px solid rgba(255,255,255,0.07);">
        <div class="w-16 h-16 mx-auto rounded-2xl glass flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        </div>
        <p class="text-slate-400 font-medium">No orders yet</p>
        <p class="text-slate-600 text-sm mt-1">Orders will appear here once customers place them.</p>
    </div>
    @endif

    <!-- Order Details Modal -->
    <div x-show="modal.show" style="display:none;" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
        <div x-show="modal.show"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="modal.show = false"
             class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

        <div x-show="modal.show"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md glass-dark rounded-2xl overflow-hidden shadow-2xl"
             style="border: 1px solid rgba(217,70,239,0.2); box-shadow: 0 25px 80px rgba(217,70,239,0.15);">

            <div class="glow-gradient p-5 flex items-center justify-between">
                <div>
                    <p class="text-white/60 text-xs font-mono" x-text="'Order #GD-' + String(modal.order?.id ?? '').padStart(4, '0')"></p>
                    <h3 class="font-bold text-white">Order Details</h3>
                </div>
                <button @click="modal.show = false" class="text-white/60 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="glass rounded-xl p-4" style="border: 1px solid rgba(255,255,255,0.06);">
                        <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Customer</p>
                        <p class="text-white font-semibold text-sm" x-text="modal.order?.customer_name"></p>
                    </div>
                    <div class="glass rounded-xl p-4" style="border: 1px solid rgba(255,255,255,0.06);">
                        <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Department</p>
                        <p class="text-white font-semibold text-sm" x-text="modal.order?.department"></p>
                    </div>
                    <div class="glass rounded-xl p-4" style="border: 1px solid rgba(255,255,255,0.06);">
                        <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Phone</p>
                        <p class="text-glow-400 font-semibold text-sm" x-text="modal.order?.phone"></p>
                    </div>
                    <div class="glass rounded-xl p-4" style="border: 1px solid rgba(255,255,255,0.06);">
                        <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Total</p>
                        <p class="text-glow-400 font-bold text-sm" x-text="'Br ' + parseFloat(modal.order?.total_amount ?? 0).toFixed(2)"></p>
                    </div>
                </div>

                <div class="glass rounded-xl p-4" style="border: 1px solid rgba(255,255,255,0.06);">
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-2">Status</p>
                    <span class="text-xs font-bold px-3 py-1 rounded-full"
                          :class="{
                            'bg-yellow-500/15 text-yellow-400 border border-yellow-500/20': modal.order?.status === 'pending',
                            'bg-blue-500/15 text-blue-400 border border-blue-500/20': modal.order?.status === 'processing',
                            'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20': modal.order?.status === 'delivered',
                          }"
                          x-text="modal.order?.status ? (modal.order.status.charAt(0).toUpperCase() + modal.order.status.slice(1)) : ''">
                    </span>
                </div>

                <!-- Action buttons in modal -->
                <div class="flex gap-3 pt-2">
                    <a :href="'tel:' + modal.order?.phone"
                       class="flex-1 glass text-blue-400 hover:text-blue-300 text-sm font-semibold py-2.5 rounded-xl text-center transition-all border border-blue-500/20 hover:border-blue-500/40 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        Call
                    </a>
                    <a :href="'https://wa.me/' + formatPhone(modal.order?.phone)"
                       target="_blank"
                       class="flex-1 glass text-green-400 hover:text-green-300 text-sm font-semibold py-2.5 rounded-xl text-center transition-all border border-green-500/20 hover:border-green-500/40 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>

                    <!-- Telegram button in modal -->
                    <a :href="'https://t.me/+' + formatPhone(modal.order?.phone)"
                       target="_blank"
                       class="flex-1 glass text-sky-400 hover:text-sky-300 text-sm font-semibold py-2.5 rounded-xl text-center transition-all border border-sky-500/20 hover:border-sky-500/40 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.244-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                        Telegram
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Status badge colors applied via JS --}}
<style>
    .status-select[data-status="pending"]    { color: #facc15; border-color: rgba(234,179,8,0.3); background-color: rgba(234,179,8,0.08); }
    .status-select[data-status="processing"] { color: #60a5fa; border-color: rgba(96,165,250,0.3); background-color: rgba(96,165,250,0.08); }
    .status-select[data-status="delivered"]  { color: #34d399; border-color: rgba(52,211,153,0.3); background-color: rgba(52,211,153,0.08); }
    .status-select option { background: #0f172a; color: #e2e8f0; }
</style>

<script>
    // CSRF token for AJAX
    const csrfToken = '{{ csrf_token() }}';

    /**
     * Format Ethiopian phone numbers
     */
    function formatPhone(phone) {
        if (!phone) return '';
        let p = phone.replace(/[^0-9]/g, '');
        if (p.startsWith('0')) p = '251' + p.substring(1);
        else if (p.length === 9) p = '251' + p;
        return p;
    }

    /**
     * Send AJAX PUT request to update order status.
     * @param {number} orderId
     * @param {string} newStatus
     * @param {HTMLElement|null} selectEl — the <select> element, or null if called from button
     */
    function updateStatus(orderId, newStatus, selectEl) {
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: newStatus }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update select element styling if provided
                if (selectEl) {
                    selectEl.dataset.status = data.status;
                } else {
                    // Update the select in the row when called from a button
                    const row = document.getElementById(`order-row-${orderId}`);
                    if (row) {
                        const sel = row.querySelector('.status-select');
                        if (sel) {
                            sel.value = data.status;
                            sel.dataset.status = data.status;
                        }
                    }
                }
                showToast(data.message, true);
            } else {
                showToast('Failed to update status', false);
            }
        })
        .catch(() => showToast('Network error. Please try again.', false));
    }

    /**
     * Send AJAX DELETE request to delete the order.
     */
    function deleteOrder(orderId) {
        showConfirm(
            'Delete Order?',
            'This will permanently remove this order. This action cannot be undone.',
            () => {
                const row = document.getElementById(`order-row-${orderId}`);
                if (row) { row.style.opacity = '0.5'; row.style.pointerEvents = 'none'; }

                fetch(`/admin/orders/${orderId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (row) row.remove();
                        showToast(data.message, true);
                    } else {
                        if (row) { row.style.opacity = '1'; row.style.pointerEvents = 'auto'; }
                        showToast('Failed to delete order', false);
                    }
                })
                .catch(() => {
                    if (row) { row.style.opacity = '1'; row.style.pointerEvents = 'auto'; }
                    showToast('Network error. Please try again.', false);
                });
            }
        );
    }

    function showToast(message, success) {
        // Use Alpine to trigger toast via dispatched event
        const event = new CustomEvent('show-toast', { detail: { message, success } });
        document.dispatchEvent(event);
    }

    // Alpine component
    function ordersManager() {
        return {
            modal: { show: false, order: null },
            toast: { show: false, message: '', success: true },
            latestOrderId: {{ $orders->max('id') ?? 0 }},
            openModal(order) {
                this.modal.order = order;
                this.modal.show = true;
            },
            init() {
                document.addEventListener('show-toast', (e) => {
                    this.toast.message = e.detail.message;
                    this.toast.success = e.detail.success;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 3000);
                });

                // Periodic check for new orders
                setInterval(() => {
                    fetch(`{{ route('admin.orders.check-new') }}?after_id=${this.latestOrderId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.new_orders && data.new_orders > 0) {
                                this.toast.message = 'New order received! Refreshing...';
                                this.toast.success = true;
                                this.toast.show = true;
                                setTimeout(() => window.location.reload(), 2000);
                            }
                        })
                        .catch(() => {}); // Fail silently
                }, 10000);
            }
        }
    }
</script>

<!-- ===== Professional Confirm Modal ===== -->
<div id="confirmModal" style="display:none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-md">
    <div class="relative bg-[#0c0f1a] rounded-2xl p-8 w-full max-w-sm shadow-2xl border border-white/10 overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-red-500/10 border border-red-500/20 mx-auto mb-5">
            <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <h3 class="text-xl font-extrabold text-white text-center mb-2" id="confirmTitle">Delete?</h3>
        <p class="text-slate-400 text-sm text-center leading-relaxed mb-7" id="confirmMsg">This action cannot be undone.</p>
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

<script>
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
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) cancelConfirm();
    });
</script>

@endsection
