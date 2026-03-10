<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Trust proxies / host
        \App\Http\Middleware\TrustProxies::class,

        // Handle CORS (kalau kamu pakai laravel-cors atau built-in)
        \Illuminate\Http\Middleware\HandleCors::class,

        // Maintenance mode
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Validate post size
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Trim strings
        \App\Http\Middleware\TrimStrings::class,

        // Convert empty strings to null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // Encrypt cookies
            \App\Http\Middleware\EncryptCookies::class,

            // Add cookies to response
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,

            // Start session
            \Illuminate\Session\Middleware\StartSession::class,

            // Share errors from session
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // CSRF protection
            \App\Http\Middleware\VerifyCsrfToken::class,

            // Substitute route bindings
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Kalau kamu pakai sanctum:
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,

            // Throttling
            'throttle:api',

            // Bindings
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [

        // Auth
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,

        // Session / cache
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,

        // Authorization
        'can' => \Illuminate\Auth\Middleware\Authorize::class,

        // Guest redirect
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,

        // Password confirm
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,

        // Signed URL
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,

        // Throttle
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        // Email verified
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // ✅ Custom: Role middleware (ini yang kamu pakai di routes: role:pb,pengprov,...)
        'role' => \App\Http\Middleware\RoleManager::class,
    ];
}