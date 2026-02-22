<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use FMS\Core\Contracts\FundRepository;
use FMS\Infrastructure\Repositories\LaravelFundRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FundRepository::class, LaravelFundRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
