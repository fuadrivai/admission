<?php

namespace App\Providers;

use App\Services\GradeService;
use App\Services\Implement\GradeImplement;
use Illuminate\Support\ServiceProvider;

class GradeProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        GradeService::class => GradeImplement::class
    ];
    public function provides(): array
    {
        return [GradeService::class];
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
