<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 1. Tambahkan alias middleware kamu yang sudah ada
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // 2. MENGATASI CORS: Izinkan API agar bisa diakses dari browser/frontend
        // Ini akan secara otomatis mengatur header Access-Control-Allow-Origin
        $middleware->validateCsrfTokens(except: [
            'api/*', // Kecualikan semua route API dari pengecekan CSRF
        ]);

        // Jika kamu ingin mengaktifkan fitur stateful untuk Sanctum/API
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();