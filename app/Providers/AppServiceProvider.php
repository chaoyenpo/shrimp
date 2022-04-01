<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TsaiYiHua\ECPay\ECPay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        ECPay::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
