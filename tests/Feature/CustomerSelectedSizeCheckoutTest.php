<?php

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer selected shoe size is stored on cart and transaction detail', function () {
    $customer = User::factory()->create([
        'name' => 'Steven Customer',
        'phone' => '081234567890',
        'address' => 'Jl. Mangga Dua No. 10',
    ]);

    $product = Product::create([
        'name' => 'Steven Runner',
        'category' => 'sepatu-lari-pria',
        'size' => 42,
        'description' => 'Sepatu lari untuk test ukuran.',
        'image_url' => '/storage/products/runner.jpg',
        'price' => 400000,
        'is_available' => true,
    ]);
    $product->sizes()->where('size', 44)->update(['is_available' => true]);

    $this
        ->actingAs($customer)
        ->get('/customer/dashboard')
        ->assertOk()
        ->assertSee('product-size-'.$product->id, false)
        ->assertSee('Ukuran');

    $this
        ->actingAs($customer)
        ->postJson('/checkout', [
            'cart' => [
                [
                    'id' => $product->id,
                    'selected_size' => 45,
                    'quantity' => 1,
                ],
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Ada ukuran produk yang sedang tidak tersedia.');

    $this
        ->actingAs($customer)
        ->postJson('/checkout', [
            'cart' => [
                [
                    'id' => $product->id,
                    'selected_size' => 44,
                    'quantity' => 2,
                ],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('redirect', route('checkout'));

    $this->assertDatabaseHas('carts', [
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'selected_size' => 44,
        'quantity' => 2,
    ]);

    $this
        ->actingAs($customer)
        ->get(route('checkout'))
        ->assertOk()
        ->assertSee('Ukuran: 44');

    $this
        ->actingAs($customer)
        ->post('/checkout/process', [
            'shipping_address' => 'Jl. Mangga Dua No. 10',
            'shipping_courier' => 'JNE',
            'payment_method' => 'bca',
        ])
        ->assertRedirect();

    $transaction = Transaction::query()->firstOrFail();

    $this->assertDatabaseHas('transaction_details', [
        'transaction_id' => $transaction->id,
        'product_id' => $product->id,
        'product_size' => 44,
        'quantity' => 2,
    ]);

    $this->assertDatabaseHas('product_sizes', [
        'product_id' => $product->id,
        'size' => 44,
        'is_available' => true,
    ]);
});
