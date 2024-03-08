<?php

namespace App\Models;

use App\Models\Exchange\ActiveSymbol;
use App\Models\Exchange\Local\ExchangeStorage;
use App\Models\Exchange\Ticker;
use App\Models\Variable\Text;
use App\Services\Trading\SessionService;
use App\Models\Trading\Logic;

class AppLogic
{
    public function __construct(
        protected readonly AppState $state,
        protected readonly ExchangeStorage $exchangeStorage,
        protected readonly Logic $tradingLogic
    ){}

    public function handleSocketMessage(array $msg): array
    {
        $result = [];
        if (!empty($msg[0]['e']) && $msg[0]['e'] === '24hrTicker') {
            $result = [
                ...$result,
                ...$this->updateCurrentTickers($msg),
                ...$this->tradingLogic->handleSocketTickers($msg)
            ];
        }
        return $result;
    }

    private function updateCurrentTickers(array $tickers): array
    {
        $result = [];
        $symbols = ActiveSymbol::getActiveSymbols();
        if (empty($symbols)) {
            $result['active_symbols'] = 'no active symbols';
            return $result;
        }

        $result['active_symbols'] = implode(' ', array_map(function($arr) {
            return $arr['symbol'];
        }, $symbols));

        $activeTickers = [];
        foreach ($tickers as $ticker) {
            foreach ($symbols as $symbol) {
                if ($ticker['s'] === $symbol['symbol']) {
                    $result[$ticker['s'] . '_last_price'] = $ticker['c'];
                    $activeTickers[] = Ticker::convertSocketData($ticker);
                }
            }
        }
        if (empty($activeTickers)) {
            return [];
        }
        $this->exchangeStorage->updateTickers($activeTickers);
        return $result;
    }
}
