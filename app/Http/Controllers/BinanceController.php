<?php

namespace App\Http\Controllers;

use App\Contracts\TradingLogicInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BinanceController extends Controller
{
    protected TradingLogicInterface $binanceApiService;

    public function __construct(TradingLogicInterface $binanceApiService)
    {
        $this->binanceApiService = $binanceApiService;
    }

    public function index(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $interval = $request->query('interval');
        $startTime = $request->query('start_time');
        $endTime = $request->query('end_time');
        $klineData = $this->binanceApiService->getKlineData($symbol, $interval, $startTime, $endTime);
        // Process your logic here and return the data as JSON
        $data = [
            'message' => 'Hello from BinanceController',
            'timestamp' => now(),
            'kline_data' => $klineData
            // Add more data as needed
        ];
        return response()->json($data);
    }
}
