<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\SupplierServiceInterface;
use App\Services\SupplierService;
use App\Contracts\CltLayupServiceInterface;
use App\Services\CltLayupService;

class ServiceBindingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(SupplierServiceInterface::class, SupplierService::class);
        $this->app->bind(CltLayupServiceInterface::class, CltLayupService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
