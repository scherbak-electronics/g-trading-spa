<?php

namespace App\Services\Exchange;

use App\Models\Exchange\Local\ExchangeStorage;
use App\Services\Exchange\Binance\Api;

class KlineDownloader
{
    public function __construct(
        protected Api $api,
        protected ExchangeStorage $db
    ) {
    }

    public function downloadMissing()
    {

    }
}
