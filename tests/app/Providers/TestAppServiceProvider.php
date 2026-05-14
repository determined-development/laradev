<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TestAppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        $this->app->alias(\App\Services\Commerce\CustomerService::class, 'commerce.customers');
        $this->app->alias(\App\Services\Commerce\OrdersService::class, 'commerce.orders');
    }
}
