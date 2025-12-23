<?php

namespace App\Providers;

use App\Services\AdmissionService;
use App\Services\Implement\AdmissionImplement;
use Illuminate\Support\ServiceProvider;

class AdmissionProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public array $singletons = [
        AdmissionService::class => AdmissionImplement::class
    ];
    public function provides(): array
    {
        return [AdmissionService::class];
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
