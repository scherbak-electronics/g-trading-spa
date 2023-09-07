<?php

namespace App\Services\Trading\Exchange;

use App\Contracts\ExchangeInterface;

use App\Http\Resources\SymbolsResource;
use App\Models\Trading\Kline;
use App\Models\Trading\ExchangeSymbols;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExchangeService implements ExchangeInterface
{
    protected ExchangeInterface $exchange;

    public function __construct(ExchangeInterface $exchange)
    {
        $this->exchange = $exchange;
    }

    public function getKlineData(string $symbol, string $interval, int $startTime, int $endTime = null, int $limit = null): array
    {
        $this->removeKlinesDuplicates($symbol, $interval);
        $query = Kline::query();
        $query->where('interval', $interval);
        $query->where('symbol', $symbol);
        if ($endTime) {
            $endTimeParam = $endTime;
        } else {
            $lastBarResponse = $this->getLastBar($symbol, $interval);
            $lastServerOpenTime = $lastBarResponse[0]['open_time'];
            $endTimeParam = $lastServerOpenTime;
        }
        $query->whereBetween('open_time', [$startTime, $endTimeParam]);
        $klines = $query->get();
        if ($klines->isEmpty()) {
            $klinesNew = $this->exchange->getKlineData($symbol, $interval, $startTime, $endTimeParam, $limit);
            $insertQuery = Kline::query();
            $insertQuery->insert($klinesNew);
            return $klinesNew;
        } else {
            $lastOpenTime = $query->max('open_time');
            if ($lastOpenTime != $endTimeParam) {
                $klinesNew = $this->exchange->getKlineData($symbol, $interval, $lastOpenTime + 1, $endTimeParam, $limit);
                $barsCount = count($klinesNew);
                if ($barsCount > 0) {
                    $insertQuery = Kline::query();
                    $insertQuery->insert($klinesNew);
                }
            }
            $klines = $query->get();
            return $klines->toArray();
        }
    }

    public function getServerTime(): array
    {
        return $this->exchange->getServerTime();
    }

    protected function getLastBar(string $symbol, string $interval): array
    {
        return $this->exchange->getKlineData($symbol, $interval, null, null, 1);
    }

    protected function removeKlinesDuplicates(string $symbol, string $interval): void
    {
        $duplicateOpenTimeItems = Kline::query()
            ->where('interval', $interval)
            ->where('symbol', $symbol)
            ->groupBy('open_time')
            ->havingRaw('COUNT(open_time) > 1')
            ->pluck('id');
        Kline::query()
            ->whereIn('id', $duplicateOpenTimeItems)
            ->delete();
    }

    public function getExchangeInfo(string $symbol = null, string $permissions = null): array
    {
        $query = ExchangeSymbols::query();
        if ($symbol) {
            $query->where('symbol', $symbol);
        }
        $symbols = $query->get();
        if ($symbols->isEmpty()) {
            $info = $this->exchange->getExchangeInfo($symbol, $permissions);
            if ($info['symbols'] && is_array($info['symbols'])) {
                $symbolsData = [];
                foreach ($info['symbols'] as $symbol) {
                    $minPrice = 0;
                    $maxPrice = 0;
                    $minQty = 0;
                    $maxQty = 0;
                    $minOrderPrice = 0;
                    if ($symbol['filters'] && is_array($symbol['filters'])) {
                        foreach ($symbol['filters'] as $filter) {
                            if ($filter['filterType'] == 'PRICE_FILTER') {
                                $minPrice = $filter['minPrice'];
                                $maxPrice = $filter['maxPrice'];
                            }
                            if ($filter['filterType'] == 'LOT_SIZE') {
                                $minQty = $filter['minQty'];
                                $maxQty = $filter['maxQty'];
                            }
                            if ($filter['filterType'] == 'NOTIONAL') {
                                $minOrderPrice = $filter['minNotional'];
                            }
                        }
                    }
                    $symbolsData[] = [
                        'symbol' => $symbol['symbol'],
                        'status' => $symbol['status'],
                        'base_asset' => $symbol['baseAsset'],
                        'base_asset_precision' => $symbol['baseAssetPrecision'],
                        'quote_asset' => $symbol['quoteAsset'],
                        'quote_asset_precision' => $symbol['quoteAssetPrecision'],
                        'min_price' => $minPrice,
                        'max_price' => $maxPrice,
                        'min_qty' => $minQty,
                        'max_qty' => $maxQty,
                        'min_order_price' => $minOrderPrice
                    ];
                }
                $queryInsert = ExchangeSymbols::query();
                $queryInsert->insert($symbolsData);
                return $symbolsData;
            }
            return [];
        } else {

            return $symbols->toArray();
        }
    }

    public function getAllSymbols(string $quoteAsset = null, string $permissions = null): array
    {
        $query = ExchangeSymbols::query();
        $query->select('symbol');
        $query->where('status', 'TRADING');
        if ($quoteAsset) {
            $query->where('quote_asset', $quoteAsset);
        }
        $symbols = $query->get();
        return $symbols->toArray();
    }

    public function getSymbols($data): AnonymousResourceCollection
    {
        $query = ExchangeSymbols::query();
        $query->select('symbol');
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }
        return SymbolsResource::collection($query->paginate($per_page));
    }

    public function getTimeframes(): array
    {
        return self::TIMEFRAMES;
    }

    public function getSymbolInfo(string $symbol): array
    {
        // TODO: Implement getSymbolInfo() method.
        return [];
    }
}
