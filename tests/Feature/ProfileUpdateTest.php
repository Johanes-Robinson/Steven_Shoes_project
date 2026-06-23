<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer can update their profile from the dashboard form', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'phone' => null,
        'address' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/customer/dashboard')
        ->put('/profile/update', [
            'name' => 'Steven Customer',
            'phone' => '081234567890',
            'address' => 'Jl. Mangga Dua No. 10',
        ]);

    $response
        ->assertRedirect('/customer/dashboard')
        ->assertSessionHas('success', 'Profil berhasil diperbarui.');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Steven Customer',
        'phone' => '081234567890',
        'address' => 'Jl. Mangga Dua No. 10',
    ]);
});
