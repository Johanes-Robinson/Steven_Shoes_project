<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::query()
            ->with('product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'cart' => $cartItems,
            'subtotal' => $this->subtotal($cartItems),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if (! $product->is_available) {
            return response()->json(['message' => 'Produk sedang tidak tersedia.'], 422);
        }

        $cart = Cart::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cart) {
            $cart->increment('quantity', $data['quantity'] ?? 1);
        } else {
            $cart = Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'quantity' => $data['quantity'] ?? 1,
            ]);
        }

        $cart->load('product');

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'cart' => $cart,
        ]);
    }

    public function update(Request $request, Cart $cart)
    {
        abort_unless($cart->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:0', 'max:99'],
            'change' => ['nullable', 'integer', 'min:-99', 'max:99'],
        ]);

        $quantity = array_key_exists('quantity', $data)
            ? (int) $data['quantity']
            : (int) $cart->quantity + (int) ($data['change'] ?? 0);

        if ($quantity <= 0) {
            $cart->delete();

            return response()->json(['message' => 'Produk dihapus dari keranjang.']);
        }

        $cart->update(['quantity' => $quantity]);
        $cart->load('product');

        return response()->json([
            'message' => 'Jumlah produk diperbarui.',
            'cart' => $cart,
        ]);
    }

    public function destroy(Request $request, Cart $cart)
    {
        abort_unless($cart->user_id === $request->user()->id, 403);

        $cart->delete();

        return response()->json(['message' => 'Produk dihapus dari keranjang.']);
    }

    private function subtotal($cartItems): float
    {
        return (float) $cartItems->sum(
            fn (Cart $item) => (float) ($item->product?->price ?? 0) * (int) $item->quantity
        );
    }
}
