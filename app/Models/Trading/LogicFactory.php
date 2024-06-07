<?php
namespace App\Models\Trading;

use App\Models\Exchange\Local\ExchangeState;
use App\Models\Exchange\Local\ExchangeStorageFactory;
use App\Services\Exchange\Binance\Api as ApiSpot;
use App\Services\Exchange\Binance\ApiFutures;
use App\Services\Exchange\ExchangeService;
use App\Services\Exchange\ExchangeServiceFactory;
use App\Services\Trading\SessionService;

class LogicFactory
{
    public function create($userId, $mode, &$view): Logic
    {
        $sessionService = new SessionService();
        $exchangeServiceFactory = new ExchangeServiceFactory();
        if ($mode === 'f') {
            $exchangeService = $exchangeServiceFactory->create(true);
        } else {
            $exchangeService = $exchangeServiceFactory->create(false);
        }

        return new Logic(
            $sessionService,
            $exchangeService,
            $userId,
            $mode,
            $view
        );
    }
}
