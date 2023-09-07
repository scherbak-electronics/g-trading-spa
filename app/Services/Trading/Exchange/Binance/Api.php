<?php

namespace App\Services\Trading\Exchange\Binance;

use App\Contracts\ExchangeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Api implements ExchangeInterface
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.binance.com',
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function getKlineData(string $symbol, string $interval, int $startTime = null, int $endTime = null, int $limit = null): array
    {
        $response = $this->client->request('GET', '/api/v3/klines', [
            'query' => [
                'symbol' => $symbol,
                'interval' => $interval,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'limit' => $limit
            ],
        ]);
        $klinesRaw = json_decode($response->getBody(), true);
        return array_map(function($item) use ($symbol, $interval) {
            return [
                'symbol' => $symbol,
                'interval' => $interval,
                'open_time' => $item[0],
                'open' => $item[1],
                'high' => $item[2],
                'low' => $item[3],
                'close' => $item[4],
                'volume' => $item[5],
                'close_time' => $item[6],
                'quote_volume' => $item[7],
                'trades' => $item[8],
                'buy_base_volume' => $item[9],
                'buy_quote_volume' => $item[10]
            ];
        }, $klinesRaw);
    }

    public function getServerTime(): array
    {
        $response = $this->client->request('GET', '/api/v3/time');
        return json_decode($response->getBody(), true);
    }

    public function getExchangeInfo(string $symbol = null, string $permissions = null): array
    {
        $response = $this->client->request('GET', '/api/v3/exchangeInfo');
        return json_decode($response->getBody(), true);
    }

    public function getAllSymbols(string $quoteAsset, string $permissions): array
    {
        // TODO: Implement getAllSymbols() method.
        return [];
    }

    public function getSymbolInfo(string $symbol): array
    {
        // TODO: Implement getSymbolInfo() method.
        return [];
    }
}
