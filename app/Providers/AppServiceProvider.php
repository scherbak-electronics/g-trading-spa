<?php

namespace App\Providers;

use App\Contracts\Exchange\ServiceInterface as ExchangeServiceInterface;
use App\Services\Exchange\Binance\Api as BinanceApi;
use App\Services\Exchange\Binance\Local\State;
use App\Services\Exchange\Binance\Local\Storage;
use App\Services\Exchange\Service as ExchangeService;
use App\Services\Trading\OrderQueueService;
use Illuminate\Support\ServiceProvider;
use App\Models\Variable\BigUInt;
use App\Models\Variable\Text;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(State::class, function ($app) {
            return new State(
                new BigUInt(),
                new Text()
            );
        });
        $this->app->bind(BinanceApi::class, function ($app) {
            return new BinanceApi(
                $this->app->make(State::class)
            );
        });
        $this->app->bind(ExchangeServiceInterface::class, function ($app) {
            return new ExchangeService(
                $this->app->make(BinanceApi::class),
                $this->app->make(State::class),
                new Storage()
            );
        });
        $this->app->bind(OrderQueueService::class, function ($app) {
            return new OrderQueueService(
                $this->app->make(ExchangeService::class)
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
