<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\productController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\userController;
use App\Http\Controllers\verified_emailController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [HomeController::class, 'products'])->name('products');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/location', [HomeController::class, 'location'])->name('location');

Route::get('/email/verify', [verified_emailController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [verified_emailController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [verified_emailController::class, 'resendVerification'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

    Route::get('/password/reset', [verified_emailController::class, 'forgotPassword'])->name('password.request');
    Route::post('/password/email', [verified_emailController::class, 'sendResetLink'])->name('password.email');
    Route::get('/password/sent', [verified_emailController::class, 'show'])->name('password.sent');
    Route::get('/password/reset/{token}', [verified_emailController::class, 'resetPasswordForm'])->name('password.reset');
    Route::post('/password/reset', [verified_emailController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [userController::class, 'redirectDashboard'])->name('dashboard');
    Route::get('/customer/dashboard', [HomeController::class, 'dashboard'])->name('customer.dashboard');
    Route::match(['post', 'put'], '/profile/update', [userController::class, 'updateProfile'])->name('profile.update');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [TransactionController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [TransactionController::class, 'prepareCheckout'])->name('checkout.prepare');
    Route::post('/checkout/process', [TransactionController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{transaction}', [TransactionController::class, 'success'])->name('checkout.success');
    Route::post('/orders/{transaction}/pay', [TransactionController::class, 'pay'])->name('orders.pay');
    Route::get('/orders/{transaction}/payment-success', [TransactionController::class, 'paymentSuccess'])->name('orders.payment-success');

    Route::get('/admin/dashboard', [productController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/products', [productController::class, 'store'])->name('admin.products.store');
    Route::put('/admin/products/{product}', [productController::class, 'update'])->name('admin.products.update');
    Route::patch('/admin/products/{product}/availability', [productController::class, 'updateAvailability'])->name('admin.products.availability');
    Route::delete('/admin/products/{product}', [productController::class, 'destroy'])->name('admin.products.destroy');
    Route::patch('/admin/orders/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('admin.orders.status');
});
