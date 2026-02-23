<?php

namespace App\Providers;

use App\Models\User;                // Tambahkan ini
use App\Observers\MemberObserver;   // Tambahkan ini
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan Observer di sini
        User::observe(MemberObserver::class);
    }
}