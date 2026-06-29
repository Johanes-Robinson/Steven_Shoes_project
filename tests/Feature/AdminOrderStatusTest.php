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

test('admin can mark a processed order as shipped', function () {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[0],
    ]);

    $customer = User::factory()->create();

    $transaction = new Transaction([
        'status' => 'diproses',
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
            'status' => 'dikirim',
        ]);

    $response
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('success', 'Status pesanan berhasil diperbarui.');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'dikirim',
    ]);
});

test('admin cannot complete a processed order before it is shipped', function () {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[0],
    ]);

    $customer = User::factory()->create();

    $transaction = new Transaction([
        'status' => 'diproses',
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
            'status' => 'selesai',
        ]);

    $response
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('warning', 'Pesanan hanya bisa diselesaikan setelah statusnya dikirim.');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'diproses',
    ]);
});

test('admin can complete a shipped order', function () {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[0],
    ]);

    $customer = User::factory()->create();

    $transaction = new Transaction([
        'status' => 'dikirim',
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
            'status' => 'selesai',
        ]);

    $response
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('success', 'Status pesanan berhasil diperbarui.');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'selesai',
    ]);
});

test('admin cannot ship an unpaid order', function () {
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
            'status' => 'dikirim',
        ]);

    $response
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('warning', 'Pesanan hanya bisa dikirim setelah pembayaran diproses.');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'menunggu_pembayaran',
    ]);
});

test('admin cannot update a final order status', function (string $finalStatus) {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[0],
    ]);

    $customer = User::factory()->create();

    $transaction = new Transaction([
        'status' => $finalStatus,
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
        ->assertSessionHas('warning', 'Status pesanan final tidak bisa diubah lagi.');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => $finalStatus,
    ]);
})->with(['selesai', 'batal']);

test('admin cannot cancel a paid order', function (string $paidStatus) {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[0],
    ]);

    $customer = User::factory()->create();

    $transaction = new Transaction([
        'status' => $paidStatus,
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
            'status' => 'batal',
        ]);

    $response
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('warning', 'Pesanan yang sudah dibayar tidak bisa dibatalkan.');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => $paidStatus,
    ]);
})->with(['diproses', 'dikirim']);

test('admin cannot return a paid order to pending payment', function () {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[0],
    ]);

    $customer = User::factory()->create();

    $transaction = new Transaction([
        'status' => 'diproses',
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
            'status' => 'menunggu_pembayaran',
        ]);

    $response
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('warning', 'Pesanan yang sudah dibayar tidak bisa dikembalikan ke menunggu pembayaran.');

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'status' => 'diproses',
    ]);
});

test('admin dashboard hides cancel option for paid orders', function () {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[1],
    ]);

    $customer = User::factory()->create();

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
        ->assertSee('value="diproses"', false)
        ->assertSee('value="dikirim"', false)
        ->assertDontSee('value="menunggu_pembayaran"', false)
        ->assertDontSee('value="selesai"', false)
        ->assertDontSee('value="batal"', false);
});

test('customer order status follows admin updates', function () {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[1],
    ]);

    $customer = User::factory()->create();

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
        ->from('/admin/dashboard')
        ->patch("/admin/orders/{$transaction->id}/status", [
            'status' => 'dikirim',
        ])
        ->assertRedirect('/admin/dashboard');

    $this
        ->actingAs($customer)
        ->get('/customer/dashboard')
        ->assertOk()
        ->assertSee('#'.$transaction->invoice_number)
        ->assertSee('Dikirim');
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
