<?php

namespace App\Providers;

use App\Services\AcademicYearService;
use App\Services\Implement\AcademicYearImplement;
use Illuminate\Support\ServiceProvider;

class AcademicYearProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        AcademicYearService::class => AcademicYearImplement::class
    ];
    public function provides(): array
    {
        return [AcademicYearService::class];
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
