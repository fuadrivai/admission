<?php

namespace App\Providers;

use App\Services\Implement\ObservationDateImplement;
use App\Services\ObservationDateService;
use Illuminate\Support\ServiceProvider;

class ObservationDateProvifer extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        ObservationDateService::class => ObservationDateImplement::class
    ];
    public function provides(): array
    {
        return [ObservationDateService::class];
    }
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
