<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\TradingLogicInterface;
use App\Services\Trading\Exchange\ExchangeService;
use App\Services\Trading\Exchange\Binance\Api as BinanceApi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TradingLogicInterface::class, function ($app) {
            return new ExchangeService(new BinanceApi());
        });
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
