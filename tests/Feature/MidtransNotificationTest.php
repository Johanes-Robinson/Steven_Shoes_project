<?php

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'services.midtrans.server_key' => 'server-test-key',
        'services.midtrans.client_key' => 'client-test-key',
        'services.midtrans.is_production' => false,
    ]);
});

test('midtrans settlement notification marks an order as paid', function () {
    $transaction = midtransNotificationTransaction('menunggu_pembayaran');
    $payload = midtransNotificationPayload($transaction, [
        'transaction_status' => 'settlement',
        'status_code' => '200',
    ]);

    $this
        ->postJson(route('midtrans.notification'), $payload)
        ->assertOk()
        ->assertJsonPath('status', 'diproses');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'diproses',
        'midtrans_transaction_id' => 'midtrans-'.$transaction->invoice_number,
        'midtrans_payment_type' => 'bank_transfer',
        'midtrans_status' => 'settlement',
    ]);

    expect($transaction->refresh()->paid_at)->not->toBeNull();
});

test('midtrans notification rejects invalid signature', function () {
    $transaction = midtransNotificationTransaction('menunggu_pembayaran');
    $payload = midtransNotificationPayload($transaction, [
        'signature_key' => 'invalid-signature',
    ]);

    $this
        ->postJson(route('midtrans.notification'), $payload)
        ->assertOk()
        ->assertJsonPath('message', 'Notifikasi Midtrans diabaikan.');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'menunggu_pembayaran',
    ]);
});

test('midtrans notification health check returns ok', function () {
    $this
        ->postJson(route('midtrans.notification'), [])
        ->assertOk()
        ->assertJsonPath('message', 'Endpoint notifikasi Midtrans aktif.');
});

test('midtrans expire notification does not cancel an already paid order', function () {
    $transaction = midtransNotificationTransaction('dikirim');
    $payload = midtransNotificationPayload($transaction, [
        'transaction_status' => 'expire',
        'status_code' => '202',
    ]);

    $this
        ->postJson(route('midtrans.notification'), $payload)
        ->assertOk()
        ->assertJsonPath('status', 'dikirim');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'dikirim',
        'midtrans_status' => 'expire',
    ]);
});

function midtransNotificationTransaction(string $status): Transaction
{
    $customer = User::factory()->create();

    $transaction = new Transaction([
        'status' => $status,
        'total_amount' => 40000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);
    $transaction->user()->associate($customer);
    $transaction->save();

    return $transaction;
}

function midtransNotificationPayload(Transaction $transaction, array $overrides = []): array
{
    $payload = array_merge([
        'order_id' => $transaction->id,
        'status_code' => '200',
        'gross_amount' => '40000.00',
        'transaction_status' => 'settlement',
        'transaction_id' => 'midtrans-'.$transaction->invoice_number,
        'payment_type' => 'bank_transfer',
        'fraud_status' => 'accept',
    ], $overrides);

    $payload['signature_key'] ??= hash(
        'sha512',
        $payload['order_id'].$payload['status_code'].$payload['gross_amount'].'server-test-key'
    );

    return $payload;
}
