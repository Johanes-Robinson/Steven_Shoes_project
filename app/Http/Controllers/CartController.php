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
            ->with('product.sizes')
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
            'selected_size' => ['nullable', 'integer', 'min:20', 'max:60'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()
            ->with('sizes')
            ->findOrFail($data['product_id']);
        $selectedSize = (int) ($data['selected_size'] ?? $product->size);
        $quantity = (int) ($data['quantity'] ?? 1);

        if (! $product->is_available) {
            return response()->json(['message' => 'Produk sedang tidak tersedia.'], 422);
        }

        $cart = Cart::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->where('selected_size', $selectedSize)
            ->first();

        if (! $product->hasAvailableSize($selectedSize)) {
            return response()->json(['message' => 'Ukuran yang dipilih sedang tidak tersedia.'], 422);
        }

        if ($cart) {
            $cart->increment('quantity', $quantity);
        } else {
            $cart = Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'selected_size' => $selectedSize,
                'quantity' => $quantity,
            ]);
        }

        $cart->load('product.sizes');

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

        $cart->loadMissing('product.sizes');

        if (! $cart->product?->hasAvailableSize((int) $cart->selected_size)) {
            return response()->json(['message' => 'Ukuran yang dipilih sedang tidak tersedia.'], 422);
        }

        $cart->update(['quantity' => $quantity]);
        $cart->load('product.sizes');

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
