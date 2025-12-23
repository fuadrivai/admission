<?php

namespace App\Providers;

use App\Services\Implement\XenditCallBackImplement;
use App\Services\XenditCallBackService;
use Illuminate\Support\ServiceProvider;

class XenditCallBackProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public array $singletons = [
        XenditCallBackService::class => XenditCallBackImplement::class
    ];
    public function provides(): array
    {
        return [XenditCallBackService::class];
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
