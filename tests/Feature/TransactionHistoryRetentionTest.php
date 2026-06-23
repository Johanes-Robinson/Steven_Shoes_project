<?php

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('completed transaction history stays when customer and product are deleted', function () {
    $customer = User::factory()->create([
        'name' => 'Budi Customer',
        'email' => 'budi@example.com',
        'phone' => '0812-3456-7890',
    ]);

    $product = Product::create([
        'name' => 'Steven Archive Sneakers',
        'category' => 'sepatu-sneakers-pria',
        'size' => 42,
        'description' => 'Sepatu arsip untuk transaksi.',
        'image_url' => '/storage/products/archive.jpg',
        'price' => 350000,
        'is_available' => true,
    ]);

    $transaction = new Transaction([
        'status' => 'selesai',
        'total_amount' => 350000,
        'payment_method' => 'bca',
        'shipping_address' => 'Jl. Mangga Dua No. 10',
        'shipping_courier' => 'JNE',
        'customer_name' => $customer->name,
        'customer_email' => $customer->email,
        'customer_phone' => $customer->phone,
    ]);
    $transaction->user()->associate($customer);
    $transaction->save();

    $detail = new TransactionDetail([
        'quantity' => 1,
        'price' => $product->price,
        'product_name' => $product->name,
        'product_category' => $product->category,
        'product_size' => $product->size,
        'product_image_url' => $product->image_url,
    ]);
    $detail->transaction()->associate($transaction);
    $detail->product()->associate($product);
    $detail->save();

    $product->delete();
    $customer->delete();

    $this->assertDatabaseHas('transaction', [
        'id' => $transaction->id,
        'user_id' => null,
        'customer_name' => 'Budi Customer',
        'customer_email' => 'budi@example.com',
        'customer_phone' => '0812-3456-7890',
    ]);

    $this->assertDatabaseHas('transaction_details', [
        'id' => $detail->id,
        'transaction_id' => $transaction->id,
        'product_id' => null,
        'product_name' => 'Steven Archive Sneakers',
        'product_category' => 'sepatu-sneakers-pria',
        'product_size' => 42,
        'product_image_url' => '/storage/products/archive.jpg',
    ]);

    $transaction->refresh();
    $detail->refresh();

    expect($transaction->customerName())->toBe('Budi Customer')
        ->and($transaction->customerPhone())->toBe('0812-3456-7890')
        ->and($detail->productName())->toBe('Steven Archive Sneakers')
        ->and($detail->productImageUrl())->toBe('/storage/products/archive.jpg');
});
