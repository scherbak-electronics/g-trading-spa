<?php
/*
 * Binance API Database Layer
 * Control flow in real time
 * */
namespace App\Models\Exchange\Local;

use App\Models\Variable\BigUInt;
use App\Models\Variable\Text;

class ExchangeState
{
    public function __construct(
        protected readonly BigUInt $varBigInt,
        protected readonly Text $varText
    ){}

    public function getLastRequestTime()
    {
        return $this->varBigInt->getValue('last_request_time', 0);
    }

    public function setLastRequestTime(int $requestTime): void
    {
        $this->varBigInt->setValue('last_request_time', $requestTime);
    }

    public function setApiBusy(string $value): void
    {
        $this->varText->setValue('api_busy', $value);
    }

    public function isApiBusy(): bool
    {
        if ($this->varText->getValue('api_busy') === 'yes') {
            return true;
        }
        return false;
    }

    public function getTicker24LastUpdateTime(): int
    {
        return $this->varBigInt->getValue('ticker24_last_update_time', 0);
    }

    public function setTicker24LastUpdateTime(int $time): void
    {
        $this->varBigInt->setValue('ticker24_last_update_time', $time);
    }

    public function getExchangeInfoLastUpdateTime(): int
    {
        return $this->varBigInt->getValue('exchange_info_last_update_time', 0);
    }

    public function setExchangeInfoLastUpdateTime(int $time): void
    {
        $this->varBigInt->setValue('exchange_info_last_update_time', $time);
    }
}
