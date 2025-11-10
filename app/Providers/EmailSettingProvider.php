<?php

namespace App\Providers;

use App\Services\EmailSettingService;
use App\Services\Implement\EmailSettingImplement;
use Illuminate\Support\ServiceProvider;

class EmailSettingProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public array $singletons = [
        EmailSettingService::class => EmailSettingImplement::class
    ];
    public function provides(): array
    {
        return [EmailSettingService::class];
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
