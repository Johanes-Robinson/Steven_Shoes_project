<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\BiteshipService;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class TransactionController extends Controller
{
    private const MISSING_WHATSAPP_MESSAGE = 'Isi nomor WhatsApp dulu sebelum checkout.';

    public function __construct(
        private readonly MidtransService $midtrans,
        private readonly BiteshipService $biteship,
    )
    {
    }

    public function checkout(Request $request)
    {
        $user = $request->user();
        $cartItems = $this->currentCart($user->id);

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('customer.dashboard')
                ->with('warning', 'Keranjang masih kosong.');
        }

        if (! $this->hasWhatsappNumber($user)) {
            return $this->redirectToProfileForWhatsapp();
        }

        $cartSubtotal = $this->subtotal($cartItems);
        $biteshipReady = $this->biteship->isConfigured();

        return view('customer.checkouts', compact('user', 'cartItems', 'cartSubtotal', 'biteshipReady'));
    }

    public function shippingRates(Request $request): JsonResponse
    {
        if (! $this->hasWhatsappNumber($request->user())) {
            return response()->json([
                'message' => self::MISSING_WHATSAPP_MESSAGE,
                'redirect' => $this->profileUrl(),
            ], 422);
        }

        $data = $request->validate([
            'destination_postal_code' => ['required', 'digits:5'],
        ]);

        if (! $this->biteship->isConfigured()) {
            return response()->json([
                'message' => 'Biteship belum dikonfigurasi. Isi BITESHIP_API_KEY dan BITESHIP_ORIGIN_POSTAL_CODE.',
            ], 422);
        }

        $cartItems = $this->currentCart($request->user()->id);

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang masih kosong.'], 422);
        }

        try {
            $rates = $this->biteship->rates($cartItems, $data['destination_postal_code']);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Gagal mengambil ongkir Biteship. Coba lagi beberapa saat lagi.',
            ], 422);
        }

        if ($rates === []) {
            return response()->json([
                'message' => 'Tidak ada layanan pengiriman tersedia untuk kode pos tersebut.',
            ], 422);
        }

        return response()->json(['rates' => $rates]);
    }

    public function prepareCheckout(Request $request): JsonResponse
    {
        if (! $this->hasWhatsappNumber($request->user())) {
            return response()->json([
                'message' => self::MISSING_WHATSAPP_MESSAGE,
                'redirect' => $this->profileUrl(),
            ], 422);
        }

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
                $product = Product::findOrFail($item['product_id']);
                $cart = new Cart([
                    'selected_size' => $item['selected_size'],
                    'quantity' => $item['quantity'],
                ]);
                $cart->user()->associate($request->user());
                $cart->product()->associate($product);
                $cart->save();
            }
        });

        return response()->json([
            'message' => 'Keranjang siap diproses.',
            'redirect' => route('checkout'),
        ]);
    }

    public function process(Request $request)
    {
        $user = $request->user();

        if (! $this->hasWhatsappNumber($user)) {
            return $this->redirectToProfileForWhatsapp();
        }

        $data = $request->validate([
            'shipping_address' => ['required', 'string', 'max:1000'],
            'shipping_destination_postal_code' => ['nullable', 'digits:5'],
            'shipping_service_key' => ['nullable', 'string'],
            'shipping_courier' => ['nullable', 'in:JNE,J&T,Onsite'],
            'payment_method' => ['required', 'in:bca,mandiri,qris'],
        ]);

        $cartItems = $this->currentCart($user->id);

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('customer.dashboard')
                ->with('warning', 'Keranjang masih kosong.');
        }

        $shipping = $this->resolveShippingSelection($cartItems, $data);
        $cartSubtotal = $this->subtotal($cartItems);

        $transaction = DB::transaction(function () use ($user, $cartItems, $data, $shipping, $cartSubtotal) {
            $transaction = new Transaction([
                'status' => 'menunggu_pembayaran',
                'total_amount' => $cartSubtotal + $shipping['cost'],
                'payment_method' => $data['payment_method'],
                'shipping_address' => $data['shipping_address'],
                'shipping_destination_postal_code' => $data['shipping_destination_postal_code'] ?? null,
                'shipping_courier' => $shipping['label'],
                'shipping_courier_code' => $shipping['courier_code'],
                'shipping_service_code' => $shipping['service_code'],
                'shipping_service_name' => $shipping['service_name'],
                'shipping_estimation' => $shipping['estimation'],
                'shipping_cost' => $shipping['cost'],
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
            ]);
            $transaction->user()->associate($user);
            $transaction->cart()->associate($cartItems->first());
            $transaction->save();

            foreach ($cartItems as $item) {
                $detail = new TransactionDetail([
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'product_name' => $item->product->name,
                    'product_category' => $item->product->category,
                    'product_size' => $item->selected_size ?? $item->product->size,
                    'product_image_url' => $item->product->image_url,
                ]);
                $detail->transaction()->associate($transaction);
                $detail->product()->associate($item->product);
                $detail->save();
            }

            $transaction->cart()->dissociate();
            $transaction->save();

            Cart::query()
                ->whereIn('id', $cartItems->pluck('id'))
                ->delete();

            return $transaction;
        });

        if ($this->midtrans->isConfigured()) {
            try {
                $transaction = $this->midtrans->createSnapTransaction($transaction);
            } catch (Throwable $exception) {
                report($exception);

                return redirect()
                    ->route('checkout.success', $transaction)
                    ->with('warning', 'Pesanan dibuat, tapi koneksi ke Midtrans belum berhasil. Coba tombol bayar beberapa saat lagi.');
            }
        }

        return redirect()->route('checkout.success', $transaction);
    }

    public function success(Request $request, Transaction $transaction)
    {
        $this->authorizeTransactionAccess($request, $transaction);

        $transaction->load(['details.product']);

        if ($transaction->status === 'menunggu_pembayaran' && $this->midtrans->isConfigured() && blank($transaction->snap_token)) {
            try {
                $transaction = $this->midtrans->createSnapTransaction($transaction);
                $transaction->load(['details.product']);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        $midtrans = [
            'enabled' => $this->midtrans->isConfigured() && filled($transaction->snap_token),
            'client_key' => $this->midtrans->clientKey(),
            'snap_script_url' => $this->midtrans->snapScriptUrl(),
        ];

        return view('customer.Success', compact('transaction', 'midtrans'));
    }

    public function pay(Request $request, Transaction $transaction)
    {
        $this->authorizeTransactionAccess($request, $transaction);

        if ($transaction->status !== 'menunggu_pembayaran') {
            return redirect()
                ->route('customer.dashboard')
                ->with('warning', 'Pembayaran pesanan ini sudah diproses.');
        }

        if ($this->midtrans->isConfigured()) {
            try {
                $transaction = $this->midtrans->createSnapTransaction($transaction);
            } catch (Throwable $exception) {
                report($exception);

                return redirect()
                    ->route('checkout.success', $transaction)
                    ->with('warning', 'Link pembayaran Midtrans belum bisa dibuat. Silakan coba lagi.');
            }

            if (filled($transaction->snap_redirect_url)) {
                return redirect()->away($transaction->snap_redirect_url);
            }

            return redirect()
                ->route('checkout.success', $transaction)
                ->with('warning', 'Link pembayaran Midtrans belum tersedia. Silakan coba lagi.');
        }

        $transaction->update([
            'status' => 'diproses',
            'paid_at' => now(),
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

    public function notification(Request $request): JsonResponse
    {
        if ($request->isMethod('get') || $request->all() === []) {
            return response()->json([
                'message' => 'Endpoint notifikasi Midtrans aktif.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'string'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'transaction_id' => ['nullable', 'string'],
            'payment_type' => ['nullable', 'string'],
            'fraud_status' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Payload Midtrans tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = $validator->validated();

        if (! $this->midtrans->signatureMatches($payload)) {
            return response()->json([
                'message' => 'Notifikasi Midtrans diabaikan.',
            ]);
        }

        try {
            $transaction = $this->midtrans->applyNotification($payload);
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Order Midtrans tidak ditemukan, notifikasi diabaikan.',
            ]);
        }

        return response()->json([
            'message' => 'Notifikasi Midtrans diterima.',
            'order_id' => $transaction->id,
            'status' => $transaction->status,
        ]);
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:menunggu_pembayaran,diproses,dikirim,selesai,batal'],
        ]);

        if ($transaction->isFinal()) {
            return back()->with('warning', 'Status pesanan final tidak bisa diubah lagi.');
        }

        if ($data['status'] === 'batal' && ! $transaction->canBeCanceled()) {
            return back()->with('warning', 'Pesanan yang sudah dibayar tidak bisa dibatalkan.');
        }

        if ($data['status'] === 'menunggu_pembayaran' && $transaction->isPaid()) {
            return back()->with('warning', 'Pesanan yang sudah dibayar tidak bisa dikembalikan ke menunggu pembayaran.');
        }

        if ($data['status'] === 'dikirim' && ! $transaction->canBeShipped()) {
            return back()->with('warning', 'Pesanan hanya bisa dikirim setelah pembayaran diproses.');
        }

        if ($data['status'] === 'selesai' && ! $transaction->canBeCompleted()) {
            return back()->with('warning', 'Pesanan hanya bisa diselesaikan setelah statusnya dikirim.');
        }

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

    private function resolveShippingSelection($cartItems, array $data): array
    {
        $serviceKey = $data['shipping_service_key'] ?? null;

        if ($serviceKey === 'pickup') {
            return [
                'label' => 'Ambil di Toko',
                'courier_code' => 'pickup',
                'service_code' => 'pickup',
                'service_name' => 'Ambil di Toko',
                'estimation' => null,
                'cost' => 0,
            ];
        }

        if (! $this->biteship->isConfigured()) {
            $courier = $data['shipping_courier'] ?? 'JNE';

            return [
                'label' => $courier,
                'courier_code' => strtolower($courier),
                'service_code' => null,
                'service_name' => $courier,
                'estimation' => null,
                'cost' => 0,
            ];
        }

        if (! $serviceKey || blank($data['shipping_destination_postal_code'] ?? null)) {
            throw ValidationException::withMessages([
                'shipping_service_key' => 'Pilih layanan pengiriman dulu sebelum lanjut pembayaran.',
            ]);
        }

        try {
            $rate = $this->biteship->findRate(
                $cartItems,
                $data['shipping_destination_postal_code'],
                $serviceKey
            );
        } catch (Throwable $exception) {
            report($exception);
            $rate = null;
        }

        if (! $rate) {
            throw ValidationException::withMessages([
                'shipping_service_key' => 'Layanan pengiriman tidak valid atau sudah tidak tersedia.',
            ]);
        }

        return [
            'label' => trim($rate['courier_name'].' '.$rate['service_name']),
            'courier_code' => $rate['courier_code'],
            'service_code' => $rate['service_code'],
            'service_name' => $rate['service_name'],
            'estimation' => $rate['duration'] ?: null,
            'cost' => (int) $rate['price'],
        ];
    }

    private function hasWhatsappNumber($user): bool
    {
        return $user?->whatsappNumber() !== null;
    }

    private function redirectToProfileForWhatsapp()
    {
        return redirect()
            ->to($this->profileUrl())
            ->with('warning', self::MISSING_WHATSAPP_MESSAGE);
    }

    private function profileUrl(): string
    {
        return route('customer.dashboard').'#profil';
    }

    private function authorizeTransactionAccess(Request $request, Transaction $transaction): void
    {
        abort_unless(
            $request->user()->isAdmin() || $transaction->user_id === $request->user()->id,
            403
        );
    }
}
