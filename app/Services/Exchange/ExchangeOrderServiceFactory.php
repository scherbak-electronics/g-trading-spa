<?php
namespace App\Services\Exchange;

use App\Models\Exchange\Local\ExchangeState;
use App\Models\Exchange\Local\ExchangeStorageFactory;
use App\Services\Exchange\Binance\Api as ApiSpot;
use App\Services\Exchange\Binance\ApiFutures;

class ExchangeOrderServiceFactory
{
    public function create($isFutures): ExchangeOrderService
    {
        if ($isFutures) {
            $api = app(ApiFutures::class);
        } else {
            $api = app(ApiSpot::class);
        }
        $storageFactory = new ExchangeStorageFactory();
        return new ExchangeOrderService(
            $api,
            app(ExchangeState::class),
            $storageFactory->create($isFutures)
        );
    }
}
