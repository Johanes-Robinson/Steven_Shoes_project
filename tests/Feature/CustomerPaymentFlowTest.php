<?php

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer can complete a pending payment simulation', function () {
    $customer = User::factory()->create();

    $transaction = new Transaction([
        'status' => 'menunggu_pembayaran',
        'total_amount' => 40000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);
    $transaction->user()->associate($customer);
    $transaction->save();

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

    $transaction = new Transaction([
        'status' => 'menunggu_pembayaran',
        'total_amount' => 40000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);
    $transaction->user()->associate($owner);
    $transaction->save();

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

    $transaction = new Transaction([
        'status' => 'menunggu_pembayaran',
        'total_amount' => 40000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);
    $transaction->user()->associate($customer);
    $transaction->save();

    $this
        ->actingAs($customer)
        ->get(route('orders.payment-success', $transaction))
        ->assertRedirect(route('checkout.success', $transaction));
});
