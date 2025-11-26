<?php

namespace App\Providers;

use App\Services\BankChargerService;
use App\Services\Implement\BankChargerImplement;
use Illuminate\Support\ServiceProvider;

class BankChargerProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    
    public array $singletons = [
        BankChargerService::class => BankChargerImplement::class
    ];
    public function provides(): array
    {
        return [BankChargerService::class];
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
