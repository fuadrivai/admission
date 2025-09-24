<?php

namespace App\Providers;

use App\Services\Implement\LevelImplement;
use App\Services\LevelService;
use Illuminate\Support\ServiceProvider;

class LevelProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        LevelService::class => LevelImplement::class
    ];
    public function provides(): array
    {
        return [LevelService::class];
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
