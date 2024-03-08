<?php

namespace App\Services\Trading;

use \App\Services\Exchange\ExchangeService;

class PriceWatchService
{
    public function __construct(
        protected readonly ExchangeService $exchange
    ) {
    }

    public function tick(): void
    {
        $price = $this->getPrice();
    }
    public function getPrice(): float
    {
        return 0;
    }
}
