<?php

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer can complete a pending payment simulation', function () {
    $customer = User::factory()->create();

    $transaction = Transaction::create([
        'user_id' => $customer->id,
        'status' => 'menunggu_pembayaran',
        'total_amount' => 40000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);

    $response = $this
        ->actingAs($customer)
        ->from('/customer/dashboard')
        ->post(route('orders.pay', $transaction));

    $response->assertRedirect(route('orders.payment-success', $transaction));

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'diproses',
    ]);

    $this
        ->actingAs($customer)
        ->get(route('orders.payment-success', $transaction))
        ->assertOk()
        ->assertSee('Pembayaran Berhasil')
        ->assertSee('#'.$transaction->invoice_number);
});

test('customer cannot pay another customer transaction', function () {
    $owner = User::factory()->create();
    $otherCustomer = User::factory()->create();

    $transaction = Transaction::create([
        'user_id' => $owner->id,
        'status' => 'menunggu_pembayaran',
        'total_amount' => 40000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);

    $this
        ->actingAs($otherCustomer)
        ->post(route('orders.pay', $transaction))
        ->assertForbidden();

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'menunggu_pembayaran',
    ]);
});

test('pending transaction cannot open payment success page directly', function () {
    $customer = User::factory()->create();

    $transaction = Transaction::create([
        'user_id' => $customer->id,
        'status' => 'menunggu_pembayaran',
        'total_amount' => 40000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);

    $this
        ->actingAs($customer)
        ->get(route('orders.payment-success', $transaction))
        ->assertRedirect(route('checkout.success', $transaction));
});
