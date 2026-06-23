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
            'cart.*.selected_size' => ['nullable', 'integer', 'min:20', 'max:60'],
            'cart.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $submittedItems = collect($data['cart']);
        $resolvedItems = $submittedItems
            ->map(fn (array $item) => $this->resolveCartItem($request->user()->id, $item))
            ->filter();

        if ($resolvedItems->count() !== $submittedItems->count()) {
            return response()->json(['message' => 'Ada ukuran produk yang sedang tidak tersedia.'], 422);
        }

        $resolvedItems = $resolvedItems
            ->groupBy(fn (array $item) => $item['product_id'].'-'.$item['selected_size'])
            ->map(fn ($items) => [
                'product_id' => $items->first()['product_id'],
                'selected_size' => $items->first()['selected_size'],
                'quantity' => $items->sum('quantity'),
            ])
            ->values();

        if ($resolvedItems->isEmpty()) {
            return response()->json(['message' => 'Produk pada keranjang tidak ditemukan.'], 422);
        }

        if ($availabilityError = $this->firstAvailabilityError($resolvedItems)) {
            return response()->json(['message' => $availabilityError], 422);
        }

        DB::transaction(function () use ($request, $resolvedItems) {
            Cart::query()
                ->where('user_id', $request->user()->id)
                ->delete();

            foreach ($resolvedItems as $item) {
                Cart::create([
                    'user_id' => $request->user()->id,
                    'product_id' => $item['product_id'],
                    'selected_size' => $item['selected_size'],
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
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
            ]);

            foreach ($cartItems as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'product_name' => $item->product->name,
                    'product_category' => $item->product->category,
                    'product_size' => $item->selected_size ?? $item->product->size,
                    'product_image_url' => $item->product->image_url,
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
        $this->authorizeTransactionAccess($request, $transaction);

        $transaction->load(['details.product']);

        return view('customer.Success', compact('transaction'));
    }

    public function pay(Request $request, Transaction $transaction)
    {
        $this->authorizeTransactionAccess($request, $transaction);

        if ($transaction->status !== 'menunggu_pembayaran') {
            return redirect()
                ->route('customer.dashboard')
                ->with('warning', 'Pembayaran pesanan ini sudah diproses.');
        }

        $transaction->update([
            'status' => 'diproses',
        ]);

        return redirect()->route('orders.payment-success', $transaction);
    }

    public function paymentSuccess(Request $request, Transaction $transaction)
    {
        $this->authorizeTransactionAccess($request, $transaction);

        if ($transaction->status === 'menunggu_pembayaran') {
            return redirect()->route('checkout.success', $transaction);
        }

        return view('customer.payment-success', compact('transaction'));
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
            ->with('product.sizes')
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
        $selectedSize = isset($item['selected_size']) ? (int) $item['selected_size'] : null;
        $product = Product::query()
            ->with('sizes')
            ->where('is_available', true)
            ->find($item['id']);

        if (! $product) {
            $cart = Cart::query()
                ->with('product.sizes')
                ->where('user_id', $userId)
                ->find($item['id']);

            $product = $cart?->product?->load('sizes');
            $selectedSize ??= $cart?->selected_size;
        }

        if (! $product || ! $product->is_available) {
            return null;
        }

        $selectedSize = $selectedSize ?: (int) $product->size;

        if (! $product->hasAvailableSize($selectedSize)) {
            return null;
        }

        return [
            'product_id' => $product->id,
            'selected_size' => $selectedSize,
            'quantity' => $quantity,
        ];
    }

    private function firstAvailabilityError($items): ?string
    {
        foreach ($items as $item) {
            $product = Product::query()
                ->with('sizes')
                ->where('is_available', true)
                ->find($item['product_id']);

            if (! $product || ! $product->hasAvailableSize((int) $item['selected_size'])) {
                return 'Ukuran produk yang dipilih sedang tidak tersedia.';
            }
        }

        return null;
    }

    private function authorizeTransactionAccess(Request $request, Transaction $transaction): void
    {
        abort_unless(
            $request->user()->isAdmin() || $transaction->user_id === $request->user()->id,
            403
        );
    }
}
