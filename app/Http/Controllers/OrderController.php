<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller {
    public function checkout(Request $request) {
        $request->validate(['shipping_address' => 'required', 'phone' => 'required']);

        $carts = Cart::where('user_id', $request->user()->id)->with('product')->get();
        if ($carts->isEmpty()) return response()->json(['message' => 'Cart empty'], 400);

        return DB::transaction(function () use ($request, $carts) {
            $total = $carts->sum(fn($c) => ($c->product->discount_price ?? $c->product->price) * $c->quantity);

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total' => $total,
                'shipping_address' => $request->shipping_address,
                'phone' => $request->phone,
                'payment_method' => 'cod',
                'status' => 'pending',
            ]);

            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'vendor_id' => $cart->product->vendor_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->product->discount_price ?? $cart->product->price,
                ]);
                $cart->product->decrement('stock', $cart->quantity);
            }

            Cart::where('user_id', $request->user()->id)->delete();
            return response()->json(['message' => 'Order placed', 'order' => $order->load('items.product')]);
        });
    }

    public function myOrders(Request $request) {
        return response()->json(Order::where('user_id', $request->user()->id)->with('items.product')->latest()->get());
    }

    public function vendorOrders(Request $request) {
        $vendor = $request->user()->vendor;
        return response()->json(OrderItem::where('vendor_id', $vendor->id)->with('order.user', 'product')->latest()->get());
    }

    public function updateItemStatus(Request $request, $id) {
        $item = OrderItem::findOrFail($id);
        if ($item->vendor_id !== $request->user()->vendor->id) return response()->json(['message' => 'Unauthorized'], 403);
        $item->update(['status' => $request->status]);
        return response()->json($item);
    }

    public function allOrders() {
        return response()->json(Order::with('user', 'items.product')->latest()->get());
    }
}
