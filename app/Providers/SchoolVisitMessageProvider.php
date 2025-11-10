<?php

namespace App\Providers;

use App\Services\Implement\SchoolVisitMessageImplement;
use App\Services\SchoolVisitMessageService;
use Illuminate\Support\ServiceProvider;

class SchoolVisitMessageProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public array $singletons = [
        SchoolVisitMessageService::class => SchoolVisitMessageImplement::class
    ];
    public function provides(): array
    {
        return [SchoolVisitMessageService::class];
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
