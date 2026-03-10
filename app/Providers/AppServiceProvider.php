<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\MemberObserver;
use App\Services\Payment\DokuService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Binding DokuService ke container (sebenarnya optional, tapi aman)
        $this->app->singleton(DokuService::class, function () {
            return new DokuService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(MemberObserver::class);
    }
}