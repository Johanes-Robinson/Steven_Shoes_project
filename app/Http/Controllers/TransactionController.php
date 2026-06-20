<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();
        $cartItems = $this->currentCart($user->id);

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('customer.dashboard')
                ->with('warning', 'Keranjang masih kosong.');
        }

        $cartSubtotal = $this->subtotal($cartItems);

        return view('customer.checkouts', compact('user', 'cartItems', 'cartSubtotal'));
    }

    public function prepareCheckout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.id' => ['required', 'string'],
            'cart.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $resolvedItems = collect($data['cart'])
            ->map(fn (array $item) => $this->resolveCartItem($request->user()->id, $item))
            ->filter()
            ->groupBy('product_id')
            ->map(fn ($items) => [
                'product_id' => $items->first()['product_id'],
                'quantity' => $items->sum('quantity'),
            ])
            ->values();

        if ($resolvedItems->isEmpty()) {
            return response()->json(['message' => 'Produk pada keranjang tidak ditemukan.'], 422);
        }

        DB::transaction(function () use ($request, $resolvedItems) {
            Cart::query()
                ->where('user_id', $request->user()->id)
                ->delete();

            foreach ($resolvedItems as $item) {
                Cart::create([
                    'user_id' => $request->user()->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return response()->json([
            'message' => 'Keranjang siap diproses.',
            'redirect' => route('checkout'),
        ]);
    }

    public function process(Request $request)
    {
        $data = $request->validate([
            'shipping_address' => ['required', 'string', 'max:1000'],
            'shipping_courier' => ['required', 'in:JNE,J&T,Onsite'],
            'payment_method' => ['required', 'in:bca,mandiri,qris'],
        ]);

        $user = $request->user();
        $cartItems = $this->currentCart($user->id);

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('customer.dashboard')
                ->with('warning', 'Keranjang masih kosong.');
        }

        $transaction = DB::transaction(function () use ($user, $cartItems, $data) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'cart_id' => $cartItems->first()->id,
                'status' => 'menunggu_pembayaran',
                'total_amount' => $this->subtotal($cartItems),
                'payment_method' => $data['payment_method'],
                'shipping_address' => $data['shipping_address'],
                'shipping_courier' => $data['shipping_courier'],
            ]);

            foreach ($cartItems as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            $transaction->update(['cart_id' => null]);

            Cart::query()
                ->whereIn('id', $cartItems->pluck('id'))
                ->delete();

            return $transaction;
        });

        return redirect()->route('checkout.success', $transaction);
    }

    public function success(Request $request, Transaction $transaction)
    {
        abort_unless(
            $request->user()->isAdmin() || $transaction->user_id === $request->user()->id,
            403
        );

        $transaction->load(['details.product']);

        return view('customer.Success', compact('transaction'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:menunggu_pembayaran,diproses,dikirim,selesai,batal'],
        ]);

        $transaction->update($data);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    private function currentCart(string $userId)
    {
        return Cart::query()
            ->with('product')
            ->where('user_id', $userId)
            ->latest()
            ->get()
            ->filter(fn (Cart $item) => $item->product !== null);
    }

    private function subtotal($cartItems): float
    {
        return (float) $cartItems->sum(
            fn (Cart $item) => (float) ($item->product?->price ?? 0) * (int) $item->quantity
        );
    }

    private function resolveCartItem(string $userId, array $item): ?array
    {
        $quantity = (int) $item['quantity'];
        $product = Product::query()
            ->where('is_available', true)
            ->find($item['id']);

        if (! $product) {
            $cart = Cart::query()
                ->with('product')
                ->where('user_id', $userId)
                ->find($item['id']);

            $product = $cart?->product;
        }

        if (! $product || ! $product->is_available) {
            return null;
        }

        return [
            'product_id' => $product->id,
            'quantity' => $quantity,
        ];
    }
}
