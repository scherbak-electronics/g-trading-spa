<?php

namespace App\Providers;

use App\Models\AppLogic;
use App\Models\Exchange\Local\ExchangeState;
use App\Models\Exchange\Local\ExchangeStorage;
use App\Models\AppState;
use App\Models\Exchange\Local\ExchangeStorageFactory;
use App\Models\Variable\BigUInt;
use App\Models\Variable\Text;
use App\Services\Exchange\Binance\Api as ApiSpot;
use App\Services\Exchange\Binance\ApiFutures;
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
        $this->app->bind(ApiSpot::class, function ($app) {
            return new ApiSpot(
                $this->app->make(ExchangeState::class)
            );
        });
        $this->app->bind(ApiFutures::class, function ($app) {
            return new ApiFutures(
                $this->app->make(ExchangeState::class)
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
