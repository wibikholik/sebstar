<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon; // <--- WAJIB ADA INI AGAR TIDAK ERROR

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
        // Set locale Carbon ke Bahasa Indonesia
        Carbon::setLocale('id');

        // Set zona waktu agar sinkron dengan waktu lokal kita (WIB)
        date_default_timezone_set('Asia/Jakarta');
    }
}