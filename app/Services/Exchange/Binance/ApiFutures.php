<?php

namespace App\Services\Exchange\Binance;

use Illuminate\Support\Facades\Log;

class ApiFutures extends Api
{
    protected string $url = 'https://fapi.binance.com';
    protected array $endpoints = [
        'get_klines' => ['method' => 'GET', 'uri' => '/fapi/v1/klines', 'is_secure' => false],
        'get_time' => ['method' => 'GET', 'uri' => '/fapi/v1/time', 'is_secure' => false],
        'get_exchange_info' => ['method' => 'GET', 'uri' => '/fapi/v1/exchangeInfo', 'is_secure' => false],
        'get_ticker24' => ['method' => 'GET', 'uri' => '/fapi/v1/ticker/24hr', 'is_secure' => false],
        'get_ticker_price' => ['method' => 'GET', 'uri' => '/fapi/v2/ticker/price', 'is_secure' => false],
        'get_all_orders' => ['method' => 'GET', 'uri' => '/fapi/v1/allOrders', 'is_secure' => true],
        'create_order' => ['method' => 'POST', 'uri' => '/fapi/v1/order', 'is_secure' => true],
        'get_order' => ['method' => 'GET', 'uri' => '/fapi/v1/order', 'is_secure' => true],
        'set_leverage' => ['method' => 'POST', 'uri' => '/fapi/v1/leverage', 'is_secure' => true],
        'set_margin_type' => ['method' => 'POST', 'uri' => '/fapi/v1/marginType', 'is_secure' => true],
        'get_balance' => ['method' => 'GET', 'uri' => '/fapi/v2/balance', 'is_secure' => true],
        'get_account_info' => ['method' => 'GET', 'uri' => '/fapi/v2/account', 'is_secure' => true],
        'get_position_info' => ['method' => 'GET', 'uri' => '/fapi/v2/positionRisk', 'is_secure' => true],
        'get_listen_key' => ['method' => 'POST', 'uri' => '/fapi/v1/listenKey', 'is_secure' => true]
    ];

    public function isFutures(): bool
    {
        return true;
    }
    public function setLeverage(string $symbol, int $leverage): array
    {
        $params = [];
        if (empty($symbol) || empty($leverage)) {
            Log::channel('trading')->error('Leverage required fields validation failed.');
            return [];
        }

        $params['symbol'] = $symbol;
        $params['leverage'] = $leverage;

        $response = $this->request($this->endpoints['set_leverage'], $params);
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function setMarginType(string $symbol, string $marginType): array
    {
        $params = [];
        if (empty($symbol) || empty($marginType)) {
            Log::channel('trading')->error('Margin type required fields validation failed.');
            return [];
        }

        $params['symbol'] = $symbol;
        $params['marginType'] = $marginType;

        $response = $this->request($this->endpoints['set_margin_type'], $params);
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function getAccountInformation($omitZeroBalances = true): array
    {
        $response = $this->request($this->endpoints['get_account_info']);
        if (empty($response)) {
            return [];
        }
        return $response;
    }
    public function getBalance()
    {
        $response = $this->request($this->endpoints['get_balance']);

        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function getPositionInfo($symbol)
    {
        $response = $this->request($this->endpoints['get_position_info']);
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function getListenKey()
    {
        $response = $this->request($this->endpoints['get_listen_key']);
        if (empty($response)) {
            return [];
        }
        return $response;
    }
}
