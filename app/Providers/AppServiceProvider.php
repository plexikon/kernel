<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Plexikon\Kernel\Provider\AccountServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(\Plexikon\Kernel\Provider\AppServiceProvider::class);

        $this->app->register(AccountServiceProvider::class);

        $this->app->register(CustomerServiceProvider::class);
    }

    public function boot()
    {
        //
    }
}
