<?php

namespace App\Providers;

use App\Models\Response;
use App\Observers\ResponseObserver;
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
        Response::observe(ResponseObserver::class);
    }
}
