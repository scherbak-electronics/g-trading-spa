<?php
/*
 * Binance API Database Layer
 * */
namespace App\Models\Exchange\Local;

use App\Contracts\ExchangeInterface;
use App\Models\Exchange\Kline;
use App\Models\Exchange\Symbol;
use App\Models\Exchange\Ticker;

class ExchangeStorage
{
    const TICKER_COLUMNS = [
        'price_change', 'price_change_percent', 'last_price', 'open', 'high', 'low',
        'volume', 'quote_volume', 'open_time', 'close_time'
    ];

    protected array $klines;

    public function updateTickers(array $tickers): int
    {
        return Ticker::query()->upsert($tickers, ['symbol'], self::TICKER_COLUMNS);
    }

    public function getAllTickers(): array
    {
        $tickers = Ticker::query()->get();
        return $tickers->toArray();
    }

    public function getTickers(string $quoteAsset, string $sortBy, string $sortDir): array
    {
        $query = Ticker::query();
        if (!empty($quoteAsset)) {
            $query->where('symbol', 'like', '%' . $quoteAsset);
            $query->where('last_price', '>', 0);
        }
        if (!empty($sortBy) && !empty($sortDir)) {
            $query->orderBy($sortBy, $sortDir);
        }
        $tickers = $query->get();
        return $tickers->toArray();
    }

    public function getKlines(string $symbol, string $interval): array
    {
        $this->klines = [];
        $query = Kline::query();
        $query->where('interval', $interval);
        $query->where('symbol', $symbol);
        $query->orderBy('open_time');
        //$query->limit(1000);
        $klines = $query->get();
        if (!$klines->isEmpty()) {
            $this->klines = $klines->toArray();
        }
        return $this->klines;
    }

    public function isTheLastBarStillOpen(array $lastBarFromEx): bool
    {
        $lastBarFromDb = $this->getLastBar();
        if (!empty($lastBarFromDb)) {
            return $lastBarFromEx['open_time'] == $lastBarFromDb['open_time'];
        }
        return false;
    }

    public function getLastBar(): array
    {
        if (!empty($this->klines)) {
            return $this->klines[count($this->klines) - 1];
        }
        return [];
    }

    public function getMissingKlinesStartTime(string $interval): int
    {
        $lastBar = $this->getLastBar();
        if (!empty($lastBar)) {
            $oneIntervalTime = $this->intervalToTime($interval);
            return $lastBar['open_time'] - ($oneIntervalTime * 2);
        }
        return 0;
    }

    protected function intervalToTime(string $interval): int
    {
        return ExchangeInterface::TIMEFRAMES[$interval];
    }

    public function updateLastBar(array $lastBarFromEx): void
    {
        $lastBarFromDb = $this->getLastBar();
        if (!empty($lastBarFromDb)) {
            // update last bar we almost up to date
            $query = Kline::query();
            // update the last bar from db
            $query->upsert(
                [$lastBarFromEx],
                ['symbol', 'interval', 'open_time']
            );
        }
    }

    public function updateMissingKlines(array $missingKlines): void
    {
        $query = Kline::query();
        $query->upsert($missingKlines, ['symbol', 'interval', 'open_time']);
    }

    public function createNewKlines(array $klines): void
    {
        $query = Kline::query();
        $query->insert($klines);
    }

    public function getSymbols(string $quoteAsset = null): array
    {
        $query = Symbol::query();
        $query->select('symbol');
        $query->where('status', 'TRADING');
        if ($quoteAsset) {
            $query->where('quote_asset', $quoteAsset);
        }
        $symbols = $query->get();
        if (!$symbols->isEmpty()) {
            return $symbols->toArray();
        }
        return [];
    }

    public function updateExchangeInfo(array $info): array
    {
        $querySymbol = Symbol::query();
        $symbols = $this->extractSymbols($info);
        $querySymbol->upsert($symbols, ['symbol']);
        return $symbols;
    }

    protected function extractSymbols(array $info): array
    {
        if ($info['symbols'] && is_array($info['symbols'])) {
            $symbols = [];
            foreach ($info['symbols'] as $symbol) {
                $symbols[] = [
                    'symbol' => $symbol['symbol'],
                    'status' => $symbol['status'],
                    'base_asset' => $symbol['baseAsset'],
                    'base_asset_precision' => $symbol['baseAssetPrecision'],
                    'quote_asset' => $symbol['quoteAsset'],
                    'min_price' => $this->getMinPrice($symbol),
                    'order_types' => $this->getOrderTypes($symbol),
                    'permissions' => $this->getPermissions($symbol)
                ];
            }
            return $symbols;
        }
        return [];
    }

    protected function getOrderTypes($symbol): string
    {
        if ($symbol['orderTypes'] && is_array($symbol['orderTypes'])) {
            return implode(',', $symbol['orderTypes']);
        }
        return '';
    }

    protected function getPermissions($symbol): string
    {
        if ($symbol['permissions'] && is_array($symbol['permissions'])) {
            return implode(',', $symbol['permissions']);
        }
        return '';
    }

    protected function getMinPrice($symbol): string
    {
        if ($symbol['filters'] && is_array($symbol['filters'])) {
            foreach ($symbol['filters'] as $filter) {
                if ($filter['filterType'] === 'PRICE_FILTER') {
                    return $filter['minPrice'];
                }
            }
        }
        return '';
    }

    public function getSymbolMinPrice(string $symbol): string
    {
        $query = Symbol::query();
        $query->select('min_price');
        $query->where('symbol', $symbol);
        $result = $query->first();
        if ($result !== null) {
            return $result->min_price;
        }
        return '';
    }
}
