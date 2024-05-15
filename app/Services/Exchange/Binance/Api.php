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


    protected string $url = 'https://api.binance.com';
    protected string $urlGetKlines = '/api/v3/klines';
    protected string $urlGetTime = '/api/v3/time';
    protected string $urlGetExchangeInfo = '/api/v3/exchangeInfo';
    protected string $urlGetTicker24 = '/api/v3/ticker/24hr';
    protected string $urlGetTickerPrice = '/api/v3/ticker/price';
    protected string $urlGetOpenOrders = '/api/v3/openOrders';
    protected string $urlGetAllOrders = '/api/v3/allOrders';
    protected string $urlCreateOrder = '';
    protected string $urlGetOrder = '/api/v3/order';
    protected string $urlGetAccountInformation = '/api/v3/account';

    protected array $secureEndpoints = [
        '/api/v3/openOrders', '/api/v3/allOrders', '/api/v3/order', '/api/v3/account'
    ];

    protected array $endpoints = [
        'get_klines' => ['method' => 'GET', 'uri' => '/api/v3/klines', 'is_secure' => false],
        'get_time' => ['method' => 'GET', 'uri' => '/api/v3/time', 'is_secure' => false],
        'get_exchange_info' => ['method' => 'GET', 'uri' => '/api/v3/exchangeInfo', 'is_secure' => false],
        'get_ticker24' => ['method' => 'GET', 'uri' => '/api/v3/ticker/24hr', 'is_secure' => false],
        'get_ticker_price' => ['method' => 'GET', 'uri' => '/api/v3/ticker/price', 'is_secure' => false],
        'get_all_orders' => ['method' => 'GET', 'uri' => '/api/v3/allOrders', 'is_secure' => true],
        'get_open_orders' => ['method' => 'GET', 'uri' => '/api/v3/openOrders', 'is_secure' => true],
        'create_order' => ['method' => 'POST', 'uri' => '/api/v3/order', 'is_secure' => true],
        'get_order' => ['method' => 'GET', 'uri' => '/api/v3/order', 'is_secure' => true],
        'get_account_info' => ['method' => 'GET', 'uri' => '/api/v3/account', 'is_secure' => true]
    ];

    protected Client $client;
    protected APIClientSecure $clientSecure;

    public function __construct(protected ExchangeState $state)
    {
        $this->client = new Client([
            'base_uri' => $this->url,
        ]);
        $this->clientSecure = new APIClientSecure([
            'baseURL'   => $this->url,
            'key'   => env('BINANCE_API_KEY'),
            'secret' => env('BINANCE_SECRET'),
        ]);
    }

    public function isFutures(): bool
    {
        return false;
    }

    public function getKlineData(string $symbol, string $interval, int $startTime = null, int $endTime = null, int $limit = null): array
    {
        $klinesRaw = $this->request($this->endpoints['get_klines'], [
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
        $response = $this->request($this->endpoints['get_time']);
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function getExchangeInfo(string $symbol = null, string $permissions = null): array
    {
        $response = $this->request($this->endpoints['get_exchange_info']);
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function getTicker24h(): array
    {
        $tickers = $this->request($this->endpoints['get_ticker24']);
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

    public function getPriceTicker(string $symbol = null): array
    {
        $response = $this->request($this->endpoints['get_ticker_price'], [
            'symbol' => $symbol
        ]);
        if (!$response) {
            return [];
        }
        return $response;
    }

    protected function request(array $endpoint, array $params = []): null | array
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
            $tries = 50;
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
            if ($endpoint['is_secure']) {
                $response = $this->clientSecure->signRequest($endpoint['method'], $endpoint['uri'], $params);
            } else {
                $responseInterface = $this->client->request($endpoint['method'], $endpoint['uri'], ['query' => $params]);
                if ($endpoint['uri'] === $this->endpoints['get_exchange_info']) {
                    Log::channel('exchange_info')->info($responseInterface->getBody());
                }

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

    public function getOpenOrders(string $symbol = ''): array
    {
        $params = [];
        if (!empty($symbol)) {
            $params['symbol'] = $symbol;
        }
        $response = $this->request($this->endpoints['get_open_orders'], $params);
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
        $response = $this->request($this->endpoints['get_all_orders'], $params);
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
        $response = $this->request($this->endpoints['create_order'], $params);
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
        $response = $this->request($this->endpoints['get_order'], $params);
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function getAccountInformation($omitZeroBalances = true): array
    {
        $params['omitZeroBalances'] = $omitZeroBalances;
        $response = $this->request($this->endpoints['get_account_info'], $params);
        if (empty($response)) {
            return [];
        }
        return $response;
    }
}
