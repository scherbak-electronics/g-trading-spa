<?php

namespace App\Providers;

use App\Contracts\Exchange\ServiceInterface as ExchangeServiceInterface;
use App\Models\Exchange\Local\State as ExchangeState;
use App\Models\Exchange\Local\Storage;
use App\Models\App\State as AppState;
use App\Models\Variable\BigUInt;
use App\Models\Variable\Text;
use App\Services\Exchange\Binance\Api as BinanceApi;
use App\Services\Exchange\Service as ExchangeService;
use App\Services\Trading\OrderQueueService;
use App\Services\Trading\PriceWatchService;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(AppState::class, function ($app) {
            return new AppState(
                new Text()
            );
        });
        $this->app->bind(ExchangeState::class, function ($app) {
            return new ExchangeState(
                new BigUInt(),
                new Text()
            );
        });
        $this->app->bind(BinanceApi::class, function ($app) {
            return new BinanceApi(
                $this->app->make(ExchangeState::class)
            );
        });
        $this->app->bind(ExchangeServiceInterface::class, function ($app) {
            return new ExchangeService(
                $this->app->make(BinanceApi::class),
                $this->app->make(ExchangeState::class),
                new Storage()
            );
        });
        $this->app->bind(OrderQueueService::class, function ($app) {
            return new OrderQueueService(
                $this->app->make(ExchangeService::class)
            );
        });
        $this->app->bind(PriceWatchService::class, function ($app) {
            return new PriceWatchService(
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
