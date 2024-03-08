<?php

namespace App\Providers;

use App\Models\AppLogic;
use App\Models\Exchange\Local\ExchangeState;
use App\Models\Exchange\Local\ExchangeStorage;
use App\Models\AppState;
use App\Models\Variable\BigUInt;
use App\Models\Variable\Text;
use App\Services\Exchange\Binance\Api as BinanceApi;
use App\Services\Exchange\ExchangeService;
use App\Services\Trading\OrderQueueService;
use App\Services\Trading\PriceWatchService;
use App\Services\Trading\SessionService;
use Illuminate\Support\ServiceProvider;
use App\Models\Trading\Logic;



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
        $this->app->bind(ExchangeService::class, function ($app) {
            return new ExchangeService(
                $this->app->make(BinanceApi::class),
                $this->app->make(ExchangeState::class),
                new ExchangeStorage()
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
        $this->app->bind(Logic::class, function ($app) {
            return new Logic(
                $this->app->make(SessionService::class)
            );
        });
        $this->app->bind(AppLogic::class, function ($app) {
            return new AppLogic(
                $this->app->make(AppState::class),
                new ExchangeStorage(),
                $this->app->make(Logic::class)
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
