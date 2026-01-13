<?php

namespace App\Providers;

use App\Services\AdmissionDocumentService;
use App\Services\Implement\AdmissionDocumentImplement;
use Illuminate\Support\ServiceProvider;

class AdmissionDocumentProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public array $singletons = [
        AdmissionDocumentService::class => AdmissionDocumentImplement::class
    ];
    public function provides(): array
    {
        return [AdmissionDocumentService::class];
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
