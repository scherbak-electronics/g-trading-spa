<?php

namespace App\Providers;

use App\Contracts\Exchange\ServiceInterface as ExchangeServiceInterface;
use App\Services\Exchange\Binance\Api as BinanceApi;
use App\Services\Exchange\Binance\Local\State;
use App\Services\Exchange\Binance\Local\Storage;
use App\Services\Exchange\Service as ExchangeService;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ExchangeServiceInterface::class, function ($app) {
            return new ExchangeService(
                new BinanceApi(
                    new State()
                ),
                new State(),
                new Storage()
            );
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
