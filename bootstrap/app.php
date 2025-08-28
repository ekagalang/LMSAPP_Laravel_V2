<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\HoneypotMiddleware; // Import HoneypotMiddleware
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // PERBAIKAN: Tambahkan ini untuk mendaftarkan alias middleware Spatie
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'force.json' => ForceJsonResponse::class,
            // 🍯 Tambahkan honeypot middleware alias
            'honeypot' => HoneypotMiddleware::class,
        ]);

        // 🍯 Opsional: Terapkan honeypot secara global pada web group
        // Uncomment baris di bawah jika ingin honeypot aktif di semua routes
        // $middleware->web(append: [
        //     HoneypotMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();