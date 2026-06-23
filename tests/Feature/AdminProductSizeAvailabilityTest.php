<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can update product size availability', function () {
    $admin = User::factory()->create([
        'email' => User::adminEmails()[0],
    ]);

    $product = Product::create([
        'name' => 'Steven Size Sneakers',
        'category' => 'sepatu-sneakers-pria',
        'size' => 42,
        'description' => 'Sepatu untuk test stok ukuran.',
        'image_url' => '/storage/products/size.jpg',
        'price' => 450000,
        'is_available' => true,
    ]);

    $this
        ->actingAs($admin)
        ->from('/admin/dashboard')
        ->patch("/admin/products/{$product->id}/sizes", [
            'size_available' => [
                40 => '1',
                44 => '1',
            ],
        ])
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('success', 'Ketersediaan ukuran produk berhasil diperbarui.');

    $this->assertDatabaseHas('product_sizes', [
        'product_id' => $product->id,
        'size' => 44,
        'is_available' => true,
    ]);

    $this->assertDatabaseHas('product_sizes', [
        'product_id' => $product->id,
        'size' => 42,
        'is_available' => false,
    ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'size' => 40,
        'is_available' => true,
    ]);
});
