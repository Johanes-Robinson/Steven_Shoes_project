<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public store pages use store route prefix', function () {
    expect(route('store.products', [], false))->toBe('/store/products')
        ->and(route('store.location', [], false))->toBe('/store/location')
        ->and(route('store.shop', [], false))->toBe('/store/shop');

    $this->get(route('store.products'))
        ->assertOk()
        ->assertSee('Daftar Produk');

    $this->get(route('store.location'))
        ->assertOk()
        ->assertSee('Steven Shoes Mangga Dua');

    $this->get(route('store.shop'))
        ->assertRedirect(route('login'));
});

test('old public store urls redirect to store prefix', function (string $oldUrl, string $newUrl) {
    $this->get($oldUrl)
        ->assertRedirect($newUrl);
})->with([
    ['/products', '/store/products'],
    ['/location', '/store/location'],
    ['/shop', '/store/shop'],
]);
