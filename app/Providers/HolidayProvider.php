<?php

namespace App\Providers;

use App\Services\HolidayService;
use App\Services\Implement\HolidayImplement;
use Illuminate\Support\ServiceProvider;

class HolidayProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
   
    public array $singletons = [
        HolidayService::class => HolidayImplement::class
    ];
    public function provides(): array
    {
        return [HolidayService::class];
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
