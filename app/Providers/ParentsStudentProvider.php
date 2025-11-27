<?php

namespace App\Providers;

use App\Services\Implement\ParentsStudentImplement;
use App\Services\ParentsStudentService;
use Illuminate\Support\ServiceProvider;

class ParentsStudentProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
     public array $singletons = [
        ParentsStudentService::class => ParentsStudentImplement::class
    ];
    public function provides(): array
    {
        return [ParentsStudentService::class];
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
