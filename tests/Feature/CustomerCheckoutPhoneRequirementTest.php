<?php

use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer without whatsapp number is redirected to profile before opening checkout page', function () {
    $customer = User::factory()->create([
        'phone' => null,
    ]);
    $product = checkoutPhoneRequirementProduct();

    $cart = new Cart([
        'selected_size' => 42,
        'quantity' => 1,
    ]);
    $cart->user()->associate($customer);
    $cart->product()->associate($product);
    $cart->save();

    $this
        ->actingAs($customer)
        ->get(route('checkout'))
        ->assertRedirect(route('customer.dashboard').'#profil')
        ->assertSessionHas('warning', 'Isi nomor WhatsApp dulu sebelum checkout.');
});

test('customer without whatsapp number cannot prepare checkout from cart drawer', function () {
    $customer = User::factory()->create([
        'phone' => null,
    ]);
    $product = checkoutPhoneRequirementProduct();

    $this
        ->actingAs($customer)
        ->postJson(route('checkout.prepare'), [
            'cart' => [
                [
                    'id' => $product->id,
                    'selected_size' => 42,
                    'quantity' => 1,
                ],
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Isi nomor WhatsApp dulu sebelum checkout.')
        ->assertJsonPath('redirect', route('customer.dashboard').'#profil');

    $this->assertDatabaseMissing('carts', [
        'user_id' => $customer->id,
        'product_id' => $product->id,
    ]);
});

test('customer without whatsapp number cannot create transaction', function () {
    $customer = User::factory()->create([
        'phone' => null,
    ]);
    $product = checkoutPhoneRequirementProduct();

    $cart = new Cart([
        'selected_size' => 42,
        'quantity' => 1,
    ]);
    $cart->user()->associate($customer);
    $cart->product()->associate($product);
    $cart->save();

    $this
        ->actingAs($customer)
        ->from(route('checkout'))
        ->post(route('checkout.process'), [
            'shipping_address' => 'Jl. Mangga Dua No. 10',
            'shipping_courier' => 'JNE',
            'payment_method' => 'bca',
        ])
        ->assertRedirect(route('customer.dashboard').'#profil')
        ->assertSessionHas('warning', 'Isi nomor WhatsApp dulu sebelum checkout.');

    $this->assertDatabaseCount('transaction', 0);
    $this->assertDatabaseHas('carts', [
        'id' => $cart->id,
        'user_id' => $customer->id,
        'product_id' => $product->id,
    ]);
});

function checkoutPhoneRequirementProduct(): Product
{
    return Product::create([
        'name' => 'Steven Runner',
        'category' => 'sepatu-lari-pria',
        'size' => 42,
        'description' => 'Sepatu lari untuk test checkout.',
        'image_url' => '/storage/products/runner.jpg',
        'price' => 400000,
        'is_available' => true,
    ]);
}
