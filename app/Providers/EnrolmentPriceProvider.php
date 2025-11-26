<?php

namespace App\Providers;

use App\Services\EnrolmentPriceService;
use App\Services\Implement\EnrolmentPriceImplement;
use Illuminate\Support\ServiceProvider;

class EnrolmentPriceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        EnrolmentPriceService::class => EnrolmentPriceImplement::class
    ];
    public function provides(): array
    {
        return [EnrolmentPriceService::class];
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
