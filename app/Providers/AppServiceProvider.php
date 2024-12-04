<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\JobsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(JobsService::class, function ($app) {
            return new JobsService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
