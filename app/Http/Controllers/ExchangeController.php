<?php

namespace App\Http\Controllers;

use App\Contracts\ExchangeInterface;
use App\Contracts\TradingLogicInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ExchangeController extends Controller
{
    protected ExchangeInterface $exchangeApi;

    public function __construct(ExchangeInterface $exchangeApi)
    {
        $this->exchangeApi = $exchangeApi;
    }

    public function kline(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $interval = $request->query('interval');
        $startTime = $request->query('start_time');
        $endTime = $request->query('end_time');
        $klineData = $this->exchangeApi->getKlineData($symbol, $interval, $startTime, $endTime);
        // Process your logic here and return the data as JSON
        $data = [
            'message' => 'Hello from ExchangeController',
            'timestamp' => now(),
            'kline_data' => $klineData
            // Add more data as needed
        ];
        return response()->json($data);
    }

    public function info(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $permissions = $request->query('permissions');

        $info = $this->exchangeApi->getExchangeInfo($symbol, $permissions);
        // Process your logic here and return the data as JSON
        $data = [
            'message' => 'Hello from ExchangeController',
            'timestamp' => now(),
            'exchange_info' => $info
            // Add more data as needed
        ];
        return response()->json($data);
    }

    public function symbols(Request $request)
    {
        return $this->exchangeApi->getSymbols($request->all());
    }

    public function timeframes(Request $request)
    {
        return $this->exchangeApi->getTimeframes();
    }

    public function strategies(Request $request)
    {
        return TradingLogicInterface::STRATEGIES;
    }

    public function symbolInfo(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $info = $this->exchangeApi->getSymbolInfo($symbol);
        // Process your logic here and return the data as JSON
        $data = [
            'message' => 'Hello from ExchangeController',
            'timestamp' => now(),
            'symbol_info' => $info
            // Add more data as needed
        ];
        return response()->json($data);
    }
}
