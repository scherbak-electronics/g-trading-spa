<?php

namespace App\Services\Trading;

use App\Contracts\Exchange\ServiceInterface as ExchangeServiceInterface;
use App\Http\Resources\SymbolsResource;
use App\Services\Exchange\Binance\Api;
use App\Services\Exchange\Binance\Local\State;
use App\Services\Exchange\Binance\Local\Storage;
use App\Utilities\Data;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Exchange\Symbol;

class SessionService
{
    public function __construct(
        protected Api $api,
        protected State $state,
        protected Storage $db
    ) {
    }

    public function getSession(int $id): array
    {
        return [
            'id' => 1,
            'symbol' => 'BTCUSDT',
            'user_id' => 1,
            'state' => 'new'
        ];
    }
}
