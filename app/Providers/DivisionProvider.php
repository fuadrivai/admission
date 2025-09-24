<?php

namespace App\Providers;

use App\Services\DivisionService;
use App\Services\Implement\DivisionImplement;
use Illuminate\Support\ServiceProvider;

class DivisionProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        DivisionService::class => DivisionImplement::class
    ];
    public function provides(): array
    {
        return [DivisionService::class];
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
