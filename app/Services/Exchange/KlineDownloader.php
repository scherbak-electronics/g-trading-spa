<?php

namespace App\Services\Exchange;

use App\Models\Exchange\Local\Storage;
use App\Services\Exchange\Binance\Api;

class KlineDownloader
{
    public function __construct(
        protected Api $api,
        protected Storage $db
    ) {
    }

    public function downloadMissing()
    {

    }
}
