<?php

namespace App\Providers;

use App\Services\Implement\SiswaEreportImplement;
use App\Services\SiswaEreportService;
use Illuminate\Support\ServiceProvider;

class SiswaEreportProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        SiswaEreportService::class => SiswaEreportImplement::class
    ];
    public function provides(): array
    {
        return [SiswaEreportService::class];
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
