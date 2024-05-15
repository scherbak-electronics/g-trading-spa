<?php
namespace App\Models\Exchange\Local;

use App\Models\Exchange\Futures\Kline as KlineFutures;
use App\Models\Exchange\Kline as KlineSpot;
use App\Models\Exchange\Futures\Symbol as SymbolFutures;
use App\Models\Exchange\Symbol as SymbolSpot;
use App\Models\Exchange\Futures\Ticker as TickerFutures;
use App\Models\Exchange\Ticker as TickerSpot;

class ExchangeStorageFactory
{
    public function create($isFutures = false): ExchangeStorage
    {
        if ($isFutures) {
            $kline = KlineFutures::query();
            $symbol = SymbolFutures::query();
            $ticker = TickerFutures::query();
        } else {
            $kline = KlineSpot::query();
            $symbol = SymbolSpot::query();
            $ticker = TickerSpot::query();
        }
        return new ExchangeStorage($kline, $symbol, $ticker);
    }
}
