<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function isConfigured(): bool
    {
        return filled($this->serverKey()) && filled($this->clientKey());
    }

    public function clientKey(): ?string
    {
        return config('services.midtrans.client_key');
    }

    public function snapScriptUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    public function createSnapTransaction(Transaction $transaction): Transaction
    {
        if (! $this->isConfigured() || filled($transaction->snap_token)) {
            return $transaction;
        }

        $transaction->loadMissing('details');
        $this->configure($transaction->id);

        $snap = Snap::createTransaction($this->snapPayload($transaction));

        $transaction->forceFill([
            'snap_token' => $snap->token ?? null,
            'snap_redirect_url' => $snap->redirect_url ?? null,
            'midtrans_status' => 'created',
        ])->save();

        return $transaction->refresh();
    }

    public function signatureMatches(array $payload): bool
    {
        $signature = (string) Arr::get($payload, 'signature_key', '');

        if ($signature === '' || ! filled($this->serverKey())) {
            return false;
        }

        foreach (['order_id', 'status_code', 'gross_amount'] as $key) {
            if (! array_key_exists($key, $payload)) {
                return false;
            }
        }

        $expected = hash(
            'sha512',
            (string) $payload['order_id'].
            (string) $payload['status_code'].
            (string) $payload['gross_amount'].
            $this->serverKey()
        );

        return hash_equals($expected, $signature);
    }

    public function applyNotification(array $payload): Transaction
    {
        $transaction = Transaction::query()->findOrFail((string) $payload['order_id']);
        $targetStatus = $this->statusFromNotification($payload);

        $updates = [
            'midtrans_transaction_id' => Arr::get($payload, 'transaction_id', $transaction->midtrans_transaction_id),
            'midtrans_payment_type' => Arr::get($payload, 'payment_type', $transaction->midtrans_payment_type),
            'midtrans_status' => Arr::get($payload, 'transaction_status', $transaction->midtrans_status),
            'midtrans_fraud_status' => Arr::get($payload, 'fraud_status', $transaction->midtrans_fraud_status),
        ];

        if ($targetStatus === 'diproses') {
            if (! $transaction->isFinal() && ! $transaction->isPaid()) {
                $updates['status'] = 'diproses';
            }

            if (! $transaction->paid_at) {
                $updates['paid_at'] = now();
            }
        }

        if ($targetStatus === 'menunggu_pembayaran' && ! $transaction->isPaid() && ! $transaction->isFinal()) {
            $updates['status'] = 'menunggu_pembayaran';
        }

        if ($targetStatus === 'batal' && ! $transaction->isPaid() && ! $transaction->isFinal()) {
            $updates['status'] = 'batal';
        }

        $transaction->forceFill($updates)->save();

        return $transaction->refresh();
    }

    private function configure(?string $idempotencyKey = null): void
    {
        Config::$serverKey = $this->serverKey();
        Config::$clientKey = $this->clientKey();
        Config::$isProduction = $this->isProduction();
        Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('services.midtrans.is_3ds', true);
        Config::$appendNotifUrl = $this->notificationUrl();
        Config::$overrideNotifUrl = null;
        Config::$paymentIdempotencyKey = $idempotencyKey;
        Config::$curlOptions = [];
    }

    private function snapPayload(Transaction $transaction): array
    {
        $payload = [
            'transaction_details' => [
                'order_id' => $transaction->id,
                'gross_amount' => (int) round((float) $transaction->total_amount),
            ],
            'customer_details' => [
                'first_name' => $transaction->customerName(),
                'email' => $transaction->customer_email,
                'phone' => $transaction->customerPhone(),
                'shipping_address' => [
                    'first_name' => $transaction->customerName(),
                    'phone' => $transaction->customerPhone(),
                    'address' => $transaction->shipping_address,
                    'country_code' => 'IDN',
                ],
            ],
            'callbacks' => [
                'finish' => route('orders.payment-success', $transaction),
            ],
        ];

        $items = $transaction->details
            ->map(fn ($detail) => [
                'id' => (string) ($detail->product_id ?: $detail->id),
                'price' => (int) round((float) $detail->price),
                'quantity' => (int) $detail->quantity,
                'name' => Str::limit($detail->productName(), 50, ''),
            ])
            ->values()
            ->all();

        if ($items !== []) {
            $payload['item_details'] = $items;
        }

        if ((float) $transaction->shipping_cost > 0) {
            $payload['item_details'][] = [
                'id' => 'shipping',
                'price' => (int) round((float) $transaction->shipping_cost),
                'quantity' => 1,
                'name' => Str::limit('Ongkir '.$transaction->shipping_courier, 50, ''),
            ];
        }

        $enabledPayments = $this->enabledPayments($transaction->payment_method);

        if ($enabledPayments !== []) {
            $payload['enabled_payments'] = $enabledPayments;
        }

        return $payload;
    }

    private function enabledPayments(?string $paymentMethod): array
    {
        return match ($paymentMethod) {
            'bca' => ['bca_va'],
            'mandiri' => ['echannel'],
            'qris' => ['other_qris'],
            default => [],
        };
    }

    private function statusFromNotification(array $payload): ?string
    {
        $status = (string) Arr::get($payload, 'transaction_status', '');
        $paymentType = (string) Arr::get($payload, 'payment_type', '');
        $fraudStatus = (string) Arr::get($payload, 'fraud_status', '');

        return match ($status) {
            'capture' => $paymentType === 'credit_card' && $fraudStatus === 'challenge'
                ? null
                : 'diproses',
            'settlement' => 'diproses',
            'pending' => 'menunggu_pembayaran',
            'deny', 'expire', 'cancel', 'failure' => 'batal',
            default => null,
        };
    }

    private function serverKey(): ?string
    {
        return config('services.midtrans.server_key');
    }

    private function isProduction(): bool
    {
        return (bool) config('services.midtrans.is_production', false);
    }

    private function notificationUrl(): string
    {
        return config('services.midtrans.notification_url') ?: route('midtrans.notification');
    }
}
