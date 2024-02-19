<?php

namespace App\Models\App;

use App\Models\Variable\BigUInt;
use App\Models\Variable\Text;

class State
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
}
