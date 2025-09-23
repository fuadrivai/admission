<?php

namespace App\Providers;

use App\Services\Implement\ObservationImplement;
use App\Services\ObservationService;
use Illuminate\Support\ServiceProvider;

class ObservationProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        ObservationService::class => ObservationImplement::class
    ];
    public function provides(): array
    {
        return [ObservationService::class];
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
