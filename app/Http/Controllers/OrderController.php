<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'department'    => 'nullable|string|max:255',
            'telegram_username' => 'nullable|string|max:255',
            'phone'         => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
            'cart_items'    => 'nullable|string',
            'product_id'    => 'nullable|exists:products,id',
            'quantity'      => 'nullable|integer|min:1',
        ]);

        $validated['department'] = $validated['department'] ?? 'N/A';
        
        if (!empty($validated['telegram_username']) && str_starts_with($validated['telegram_username'], '@')) {
            $validated['telegram_username'] = substr($validated['telegram_username'], 1);
        }

        $cartItems = [];
        $isCartCheckout = false;

        if (!empty($validated['cart_items'])) {
            $cartItems = json_decode($validated['cart_items'], true);
            $isCartCheckout = true;
        } elseif (!empty($validated['product_id'])) {
            $cartItems = [
                ['id' => $validated['product_id'], 'quantity' => $validated['quantity'] ?? 1]
            ];
            $isCartCheckout = false;
        }

        if (!is_array($cartItems) || empty($cartItems)) {
            return redirect()->route('shop.index')->with('error', 'No items selected.');
        }

        DB::beginTransaction();

        try {
            $activeOrderCount = Order::where('phone', $validated['phone'])
                                     ->where('status', '!=', 'delivered')
                                     ->count();
                                     
            if ($activeOrderCount >= 3) {
                DB::rollBack();
                return redirect()->route('shop.index')->with('error', '⚠️ You have reached the maximum of 3 active orders. Please wait for your orders to be delivered or contact the admin.');
            }

            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $product = Product::findOrFail($item['id']);
                $totalAmount += $product->price * $item['quantity'];
            }

            $order = Order::create([
                'customer_name' => $validated['customer_name'],
                'department' => $validated['department'],
                'phone' => $validated['phone'],
                'telegram_username' => $validated['telegram_username'] ?? null,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $product = Product::findOrFail($item['id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            DB::commit();

            $order->load('items.product');
            $this->telegramService->sendOrderNotification($order);
            
            if (!empty($order->telegram_username)) {
                $this->telegramService->sendClientConfirmation($order);
            }

            if ($isCartCheckout) {
                return redirect()->route('shop.index')->with('success', 'Your order has been placed successfully!')->with('clear_cart', true);
            } else {
                return redirect()->route('shop.index')->with('success', 'Your direct order has been placed successfully!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('shop.index')->with('error', 'Something went wrong while placing your order: ' . $e->getMessage());
        }

    }

    // Admin routes
    public function adminIndex()
    {
        $orders = Order::with('items.product')->latest()->get();
        return view('admin.orders', compact('orders'));
    }

    /**
     * Update order status via AJAX.
     * Accepts: pending | processing | delivered
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,delivered',
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'status'  => $order->status,
            'message' => 'Order status updated to ' . ucfirst($order->status),
        ]);
    }

    /**
     * Check if there are new orders since the given order ID.
     */
    public function checkNew(Request $request)
    {
        $afterId = $request->query('after_id', 0);
        $newOrdersCount = Order::where('id', '>', $afterId)->count();
        
        return response()->json([
            'new_orders' => $newOrdersCount
        ]);
    }

    /**
     * Delete an order.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }

    public function submitCustomRequest(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone'         => 'required|string|max:50',
            'telegram_username' => 'nullable|string|max:255',
            'request_message' => 'required|string|max:1000',
        ]);

        if (!empty($validated['telegram_username']) && str_starts_with($validated['telegram_username'], '@')) {
            $validated['telegram_username'] = substr($validated['telegram_username'], 1);
        }

        try {
            $this->telegramService->sendCustomRequestNotification(
                $validated['customer_name'],
                $validated['phone'],
                $validated['request_message'],
                $validated['telegram_username'] ?? null
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Custom request notification dispatch failed: ' . $e->getMessage());
        }

        return redirect()->route('shop.index')->with('success', 'Your custom request has been sent! We will check our stock and get back to you shortly.');
    }
}
