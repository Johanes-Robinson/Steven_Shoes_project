<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'admin/products',
            'admin/products/*',
            'checkout',
            'checkout/process',
            'logout',
            'midtrans/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            if ($request->is('admin/products')) {
                return back()->withErrors([
                    'image' => 'Ukuran foto terlalu besar. Maksimal 2 MB untuk upload produk.',
                ]);
            }

            return null;
        });

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->is('admin/products')) {
                return back()->withErrors([
                    'image' => 'Sesi upload kedaluwarsa atau ukuran file terlalu besar. Muat ulang halaman, lalu pilih foto maksimal 2 MB.',
                ]);
            }

            return null;
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
