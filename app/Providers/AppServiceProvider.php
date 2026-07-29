<?php

namespace App\Providers;

use App\Services\AcademicYearService;
use App\Services\Implement\AcademicYearImplement;
use App\Services\Implement\UniformProductImplement;
use App\Services\UniformProductService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */

    public array $singletons = [
        AcademicYearService::class => AcademicYearImplement::class,
        UniformProductService::class => UniformProductImplement::class,
    ];
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
    }
}
