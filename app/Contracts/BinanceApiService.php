<?php

namespace App\Contracts;

interface BinanceApiService
{
    public function getKlineData(string $symbol, string $interval, int $startTime, int $endTime): array;
}
