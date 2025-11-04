<?php

namespace App\Providers;

use App\Services\Implement\SchoolVisitIMplement;
use App\Services\SchoolVisitService;
use Illuminate\Support\ServiceProvider;

class SchoolVisitProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    
    public array $singletons = [
        SchoolVisitService::class => SchoolVisitIMplement::class
    ];
    public function provides(): array
    {
        return [SchoolVisitService::class];
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
