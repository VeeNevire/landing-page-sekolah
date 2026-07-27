<?php

namespace App\Providers;

use App\Models\Kelas;
use App\Observers\KelasObserver;
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
        Kelas::observe(KelasObserver::class);
    }
}
