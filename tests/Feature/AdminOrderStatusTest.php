<?php

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can update an order status', function () {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[0],
    ]);

    $customer = User::factory()->create();

    $transaction = new Transaction([
        'status' => 'menunggu_pembayaran',
        'total_amount' => 250000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);
    $transaction->user()->associate($customer);
    $transaction->save();

    $response = $this
        ->actingAs($admin)
        ->from('/admin/dashboard')
        ->patch("/admin/orders/{$transaction->id}/status", [
            'status' => 'diproses',
        ]);

    $response
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('success', 'Status pesanan berhasil diperbarui.');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'diproses',
    ]);
});

test('admin can open whatsapp chat from an order row', function () {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[1],
    ]);

    $customer = User::factory()->create([
        'name' => 'Budi Customer',
        'phone' => '0812-3456-7890',
    ]);

    $transaction = new Transaction([
        'status' => 'diproses',
        'total_amount' => 250000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);
    $transaction->user()->associate($customer);
    $transaction->save();

    $this
        ->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Budi Customer')
        ->assertSee('WA: <span class="font-semibold">0812-3456-7890</span>', false)
        ->assertSee('https://wa.me/6281234567890', false)
        ->assertSee('Hubungi WA');
});
