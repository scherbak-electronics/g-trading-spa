<?php

namespace App\Contracts;

interface ExchangeInterface
{
    const TIMEFRAMES = ['1s', '1m', '3m', '5m', '15m', '30m', '1h', '2h', '4h', '6h', '8h', '12h', '1d', '3d', '1w', '1M'];
    public function getKlineData(string $symbol, string $interval, int $startTime, int $endTime, int $limit): array;
    public function getServerTime(): array;
    public function getExchangeInfo(string $symbol, string $permissions): array;
    public function getAllSymbols(string $quoteAsset, string $permissions): array;
    public function getSymbolInfo(string $symbol): array;
}
