<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('customer is redirected to verification notice after register', function () {
    Notification::fake();

    $response = $this->post('/register', [
        'name' => 'Steven Customer',
        'email' => 'customer@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => '1',
    ]);

    $user = User::where('email', 'customer@example.com')->firstOrFail();

    $response
        ->assertRedirect('/email/verify')
        ->assertSessionHas('email', $user->email)
        ->assertSessionHas('verification_type', 'account');

    $this->assertGuest();
    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('admin is redirected to verification notice after register', function () {
    Notification::fake();

    $response = $this->post('/register', [
        'name' => 'Steven Admin',
        'email' => User::adminEmails()[0],
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => '1',
    ]);

    $admin = User::where('email', User::adminEmails()[0])->firstOrFail();

    $response
        ->assertRedirect('/email/verify')
        ->assertSessionHas('email', $admin->email)
        ->assertSessionHas('verification_type', 'account');

    $this->assertGuest();
    expect($admin->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($admin, VerifyEmail::class);
});

test('customer verification link logs user in and redirects to customer dashboard', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'customer@example.com',
    ]);

    $response = $this->get(URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]
    ));

    $response
        ->assertRedirect('/customer/dashboard')
        ->assertSessionHas('status', 'Email berhasil diverifikasi.');

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('html encoded verification link still passes signature validation', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'customer@example.com',
    ]);

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]
    );

    $response = $this->get(str_replace('&signature=', '&amp;signature=', $url));

    $response->assertRedirect('/customer/dashboard');
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('admin verification link logs user in and redirects to admin dashboard', function () {
    $admin = User::factory()->unverified()->create([
        'email' => User::adminEmails()[0],
    ]);

    $response = $this->get(URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $admin->id,
            'hash' => sha1($admin->getEmailForVerification()),
        ]
    ));

    $response
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('status', 'Email berhasil diverifikasi.');

    $this->assertAuthenticatedAs($admin);
    expect($admin->fresh()->hasVerifiedEmail())->toBeTrue();
});
