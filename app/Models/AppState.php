<?php

namespace App\Models;

use App\Models\Variable\Text;

class AppState
{
    public function __construct(
        protected readonly Text $varText
    ){}

    public function getCurrentViewingSymbol()
    {
        return $this->varText->getValue('current_viewing_symbol');
    }

    public function setCurrentViewingSymbol(string $symbol): void
    {
        $this->varText->setValue('current_viewing_symbol', $symbol);
    }

    public function getCurrentViewingInterval()
    {
        return $this->varText->getValue('current_viewing_interval');
    }

    public function setCurrentViewingInterval(string $interval): void
    {
        $this->varText->setValue('current_viewing_interval', $interval);
    }

    public function getActiveSymbols(): array
    {
        $text = $this->varText->getValue('active_symbols');
        if (empty($text)) {
            return [];
        }
        return json_decode($text);
    }

    public function setActiveSymbols(array $symbols): void
    {
        $this->varText->setValue('active_symbols', json_encode($symbols));
    }

    public function getActiveMarkets(): array
    {
        $text = $this->varText->getValue('active_markets');
        if (empty($text)) {
            return [];
        }
        return json_decode($text);
    }

    public function setActiveMarkets(array $markets): void
    {
        $this->varText->setValue('active_markets', json_encode($markets));
    }
}
