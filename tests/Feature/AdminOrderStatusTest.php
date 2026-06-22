<?php

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can update an order status', function () {
    $admin = User::factory()->create([
        'email' => User::ADMIN_EMAIL,
    ]);

    $customer = User::factory()->create();

    $transaction = Transaction::create([
        'user_id' => $customer->id,
        'status' => 'menunggu_pembayaran',
        'total_amount' => 250000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);

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
        'email' => User::ADMIN_EMAIL,
    ]);

    $customer = User::factory()->create([
        'name' => 'Budi Customer',
        'phone' => '0812-3456-7890',
    ]);

    Transaction::create([
        'user_id' => $customer->id,
        'status' => 'diproses',
        'total_amount' => 250000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
    ]);

    $this
        ->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Budi Customer')
        ->assertSee('WA: <span class="font-semibold">0812-3456-7890</span>', false)
        ->assertSee('https://wa.me/6281234567890', false)
        ->assertSee('Hubungi WA');
});
