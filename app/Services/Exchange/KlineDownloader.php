<?php

namespace App\Services\Exchange;

use App\Contracts\Exchange\ServiceInterface as ExchangeServiceInterface;
use App\Http\Resources\SymbolsResource;
use App\Services\Exchange\Binance\Api;
use App\Services\Exchange\Binance\Local\State;
use App\Services\Exchange\Binance\Local\Storage;
use App\Utilities\Data;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Exchange\Symbol;

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
