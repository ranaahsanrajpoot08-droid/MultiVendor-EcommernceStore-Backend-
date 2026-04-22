<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller {

    public function index(Request $request) {
        return response()->json(
            Cart::where('user_id', $request->user()->id)
                ->with('product.vendor')
                ->get()
        );
    }

    public function add(Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        $cart = Cart::where('user_id', $request->user()->id)
                    ->where('product_id', $request->product_id)
                    ->first();

        if ($cart) {
            // Already cart mein hai → quantity add karo
            $cart->update([
                'quantity' => $cart->quantity + $request->quantity
            ]);
        } else {
            // Naya item → create karo
            $cart = Cart::create([
                'user_id'    => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
            ]);
        }

        return response()->json([
            'message' => 'Added to cart',
            'cart'    => $cart->load('product')
        ]);
    }

    public function update(Request $request, $id) {
        $cart = Cart::where('user_id', $request->user()->id)
                    ->findOrFail($id);
        $cart->update(['quantity' => $request->quantity]);
        return response()->json($cart);
    }

    public function remove(Request $request, $id) {
        Cart::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->delete();
        return response()->json(['message' => 'Removed']);
    }

    public function clear(Request $request) {
        Cart::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => 'Cleared']);
    }
}
