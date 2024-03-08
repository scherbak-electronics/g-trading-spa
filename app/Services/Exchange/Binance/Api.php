<?php

namespace App\Services\Exchange\Binance;

use App\Contracts\Exchange\ApiInterface;
use App\Models\Exchange\Local\ExchangeState;
use App\Models\Exchange\Order;
use App\Utilities\Data;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use App\Models\Exchange\Ticker;

class Api implements ApiInterface
{
    const SECURE_ENDPOINTS = [
        '/api/v3/order',
        '/api/v3/openOrders',
        '/api/v3/order/cancelReplace',
        '/api/v3/allOrders',
        '/api/v3/order/oco',
        '/api/v3/orderList',
        '/api/v3/allOrderList',
        '/api/v3/openOrderList',
        '/api/v3/sor/order',
        '/api/v3/sor/order/test',
        '/api/v3/account'
    ];
    protected Client $client;
    protected APIClientSecure $clientSecure;

    public function __construct(protected ExchangeState $state)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.binance.com',
        ]);
        $this->clientSecure = new APIClientSecure([
            'baseURL'   => 'https://api.binance.com', // API base URL
            'key'   => env('BINANCE_API_KEY'),    // API Key
            'secret' => env('BINANCE_SECRET'),    // API Secret
        ]);
    }

    public function getKlineData(string $symbol, string $interval, int $startTime = null, int $endTime = null, int $limit = null): array
    {
        $klinesRaw = $this->request('GET', '/api/v3/klines', [
            'symbol' => $symbol,
            'interval' => $interval,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'limit' => $limit
        ]);
        if (empty($klinesRaw)) {
            return [];
        }
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
        $response = $this->request('GET', '/api/v3/time');
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function getExchangeInfo(string $symbol = null, string $permissions = null): array
    {
        $response = $this->request('GET', '/api/v3/exchangeInfo');
        if (empty($response)) {
            return [];
        }
        return $response;
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

    public function getTicker24h(string $symbol = null, array $symbols = [], string $type = 'FULL'): array
    {
        $tickers = $this->request('GET', '/api/v3/ticker/24hr');
        if (empty($tickers)) {
            return [];
        }
        $res = [];
        if (is_array($tickers)) {
            if (empty($tickers[0]) || !is_array($tickers[0])) {
                $res[] = Ticker::convertRestData($tickers);
            } else {
                foreach ($tickers as $ticker) {
                    $res[] = Ticker::convertRestData($ticker);
                }
            }
        }

        return $res;
    }
    
    public function getLastBar(string $symbol, string $interval): array
    {
        $klines = $this->getKlineData($symbol, $interval, null, null, 1);
        if (empty($klines)) {
            return [];
        }
        return $klines[0];
    }

    public function getPriceTicker(string $symbol = null, array $symbols = []): array
    {
        if (empty($symbols)) {
            $symbols = null;
        }
        $response = $this->request('GET', '/api/v3/ticker/price', [
            'symbol' => $symbol,
            'symbols' => $symbols
        ]);
        if (!$response) {
            return [];
        }
        return $response;
    }



    private function request(string $method, string $uri, array $params = []): null | array
    {
        $tries = 100;
        for ($i = $tries; $i >= 0; $i--) {
            if ($i == 0) {
                Log::channel('trading')->error('API still busy after '.$tries.' trys.');
                $this->state->setApiBusy('no');
                return null;
            }
            if ($this->state->isApiBusy()) {
                usleep(20000);
            } else {
                //Log::channel('trading')->info('occupy API now!');
                $this->state->setApiBusy('yes');
                break;
            }
        }
        $lastRequestTime = $this->state->getLastRequestTime();
        if ($lastRequestTime) {
            $tries = 10;
            $timeMargin = 200;
            for ($i = $tries; $i >= 0; $i--) {
                if ($i == 0) {
                    Log::channel('trading')->error('Last request time error: no trys left');
                    return null;
                }
                $nowTime = Data::getTimestampInMilliseconds();
                if (($nowTime - $lastRequestTime) >= $timeMargin) {
                    break;
                }
                usleep(10000);
            }
        }
        $response = null;
        try {
            if ($this->isEndpointSecure($uri)) {
                $response = $this->clientSecure->signRequest($method, $uri, $params);
            } else {
                $responseInterface = $this->client->request($method, $uri, ['query' => $params]);
                $response = json_decode($responseInterface->getBody(), true);
            }
        } catch (GuzzleException $e) {
            Log::channel('trading')->error('HTTP error: ' . $e->getMessage());
        }catch (\Exception $e) {
            Log::channel('trading')->error('An unexpected exception occurred: '.$e->getMessage());
        } catch (\Throwable $e) {
            // Catch any other errors, including fatal ones
            Log::channel('trading')->error('An unexpected error occurred: '.$e->getMessage());
        }
        $this->state->setLastRequestTime(Data::getTimestampInMilliseconds());
        $this->state->setApiBusy('no');
        return $response;
    }

    private function isEndpointSecure(string $endpoint = ''): bool
    {
        if (in_array($endpoint, self::SECURE_ENDPOINTS)) {
            return true;
        }
        return false;
    }

    public function getOpenOrders(string $symbol = ''): array
    {
        $params = [];
        if (!empty($symbol)) {
            $params['symbol'] = $symbol;
        }
        $response = $this->request('GET', '/api/v3/openOrders', $params);
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function getAllOrders(string $symbol, int $orderId = null, int $startTime = null, int $endTime = null, int $limit = null): array
    {
        $params = [];
        if (empty($symbol)) {
            return [];
        } else {
            $params['symbol'] = $symbol;
        }
        if (!empty($orderId)) {
            $params['orderId'] = $orderId;
        }
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        if (!empty($limit)) {
            $params['limit'] = $limit;
        }
        $response = $this->request('GET', '/api/v3/allOrders', $params);
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function createOrder(Order $order): array
    {
        $params = [];
        if (empty($order->symbol) || empty($order->side) || empty($order->type)) {
            Log::channel('trading')->error('Order required fields validation failed.');
            return [];
        }
        foreach ($order->getFillable() as $fieldName) {
            if (!empty($order->$fieldName)) {
                $params[$fieldName] = $order->$fieldName;
            }
        }
        $response = $this->request('POST', '/api/v3/allOrders', $params);
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function getOrder(string $symbol, int $orderId = null, string $origClientOrderId = ''): array
    {
        $params = [];
        if (empty($symbol) || (empty($orderId) && empty($origClientOrderId))) {
            Log::channel('trading')->error('Order required fields validation failed.');
            return [];
        }
        $params['symbol'] = $symbol;
        if (!empty($orderId)) {
            $params['orderId'] = $orderId;
        } else {
            $params['origClientOrderId'] = $origClientOrderId;
        }
        $response = $this->request('GET', '/api/v3/order', $params);
        if (empty($response)) {
            return [];
        }
        return $response;
    }
}
