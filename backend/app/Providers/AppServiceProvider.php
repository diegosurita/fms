<?php

namespace App\Providers;

use FMS\Core\Contracts\CompanyRepository;
use FMS\Core\Contracts\EventDispatcherInterface;
use Illuminate\Support\ServiceProvider;
use FMS\Core\Contracts\FundRepository;
use FMS\Core\Events\DuplicateFundFoundEvent;
use FMS\Core\Events\FundCreatedEvent;
use FMS\Infrastructure\Adapter\LaravelEventDispatcherAdapter;
use FMS\Infrastructure\Listeners\DuplicatedFundListener;
use FMS\Infrastructure\Listeners\ValidateUniqueFundListener;
use FMS\Infrastructure\Repositories\LaravelCompanyRepository;
use FMS\Infrastructure\Repositories\LaravelFundRepository;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository
        $this->app->bind(CompanyRepository::class, LaravelCompanyRepository::class);
        $this->app->bind(FundRepository::class, LaravelFundRepository::class);

        // Event Dispatcher
        $this->app->bind(EventDispatcherInterface::class, LaravelEventDispatcherAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            FundCreatedEvent::class,
            ValidateUniqueFundListener::class,
        );

        Event::listen(
            DuplicateFundFoundEvent::class,
            DuplicatedFundListener::class,
        );
    }
}
