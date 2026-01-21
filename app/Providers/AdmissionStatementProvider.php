<?php

namespace App\Providers;

use App\Services\AdmissionStatementService;
use App\Services\Implement\AdmissionStatementImplement;
use Illuminate\Support\ServiceProvider;

class AdmissionStatementProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        AdmissionStatementService::class => AdmissionStatementImplement::class
    ];
    public function provides(): array
    {
        return [AdmissionStatementService::class];
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
