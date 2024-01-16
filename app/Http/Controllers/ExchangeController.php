<?php

namespace App\Http\Controllers;

use App\Contracts\Exchange\ServiceInterface as ExchangeServiceInterface;
use App\Contracts\TradingLogicInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ExchangeController extends Controller
{
    public function __construct(protected readonly ExchangeServiceInterface $exchangeService)
    {}

    public function kline(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $interval = $request->query('interval');
        $klineData = $this->exchangeService->getKlineData($symbol, $interval);
        $data = ['kline_data' => $klineData];
        return response()->json($data);
    }

    public function info(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $info = $this->exchangeService->getExchangeInfo($symbol);
        $data = [
            'timestamp' => now(),
            'exchange_info' => $info
        ];
        return response()->json($data);
    }

    public function symbols(Request $request): AnonymousResourceCollection
    {
        return $this->exchangeService->getSymbols($request->all());
    }

    public function timeframes(Request $request): array
    {
        return $this->exchangeService->getTimeframes();
    }

    public function strategies(Request $request): array
    {
        return TradingLogicInterface::STRATEGIES;
    }

    public function symbolInfo(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $info = $this->exchangeService->getSymbolInfo($symbol);
        $data = [
            'timestamp' => now(),
            'symbol_info' => $info
        ];
        return response()->json($data);
    }

    public function ticker24h(Request $request): JsonResponse
    {
        $quoteAsset = '';
        if ($request->query('quoteAsset')) {
            $quoteAsset = $request->query('quoteAsset', '');
        }
        $sortBy = '';
        if ($request->query('sortBy')) {
            $sortBy = $request->query('sortBy', 'quote_volume');
        }
        $sortDir = '';
        if ($request->query('sortDir')) {
            $sortDir = $request->query('sortDir', 'desc');
        }
        $ticker24h = $this->exchangeService->getTicker24h($quoteAsset, $sortBy, $sortDir);
        $data = ['ticker24h' => $ticker24h];
        return response()->json($data);
    }

    public function priceTicker(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $ticker = $this->exchangeService->getPriceTicker($symbol);
        $data = [
            'timestamp' => now(),
            'ticker' => $ticker
        ];
        return response()->json($data);
    }

    public function updateLastBar(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $interval = $request->query('interval');
        $lastBar = $this->exchangeService->updateAndGetLastBar($symbol, $interval);

        $data = [
            'timestamp' => now(),
            'last_bar' => $lastBar

        ];
        return response()->json($data);
    }

    public function updateExchangeInfo(Request $request): JsonResponse
    {
        $this->exchangeService->updateExchangeInfo();
        $data = [
            'timestamp' => now(),
            'result' => 'ok'
        ];
        return response()->json($data);
    }

    public function getSymbolMinPrice(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $minPrice = $this->exchangeService->getSymbolMinPrice($symbol);
        $data = [
            'timestamp' => now(),
            'min_price' => $minPrice
        ];
        return response()->json($data);
    }

    public function getOpenOrders(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $orders = $this->exchangeService->getOpenOrders($symbol);
        $data = ['open_orders' => $orders];
        return response()->json($data);
    }

    public function getAllOrders(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $orderId = $request->query('orderId');
        $startTime = $request->query('startTime');
        $endTime = $request->query('endTime');
        $limit = $request->query('limit');
        $orders = $this->exchangeService->getAllOrders($symbol, $orderId, $startTime, $endTime, $limit);
        $data = ['all_orders' => $orders];
        return response()->json($data);
    }

    public function getOrder(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $orderId = $request->query('orderId');
        $origClientOrderId = $request->query('origClientOrderId');
        $order = $this->exchangeService->getOrder($symbol, $orderId, $origClientOrderId);
        $data = ['order' => $order];
        return response()->json($data);
    }
}
