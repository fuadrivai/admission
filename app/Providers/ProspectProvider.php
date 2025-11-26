<?php

namespace App\Providers;

use App\Services\Implement\ProspectImplement;
use App\Services\ProspectService;
use Illuminate\Support\ServiceProvider;

class ProspectProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        ProspectService::class => ProspectImplement::class
    ];
    public function provides(): array
    {
        return [ProspectService::class];
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
