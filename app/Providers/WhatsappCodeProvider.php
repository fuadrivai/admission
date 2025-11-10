<?php

namespace App\Providers;

use App\Services\Implement\WhatsappCodeImplement;
use App\Services\WhatsappCodeService;
use Illuminate\Support\ServiceProvider;

class WhatsappCodeProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */public array $singletons = [
        WhatsappCodeService::class => WhatsappCodeImplement::class
    ];
    public function provides(): array
    {
        return [WhatsappCodeService::class];
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
