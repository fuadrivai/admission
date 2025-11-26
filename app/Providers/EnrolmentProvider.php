<?php

namespace App\Providers;

use App\Services\EnrolmentService;
use App\Services\Implement\EnrolmentImplement;
use Illuminate\Support\ServiceProvider;

class EnrolmentProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public array $singletons = [
        EnrolmentService::class => EnrolmentImplement::class
    ];
    public function provides(): array
    {
        return [EnrolmentService::class];
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
