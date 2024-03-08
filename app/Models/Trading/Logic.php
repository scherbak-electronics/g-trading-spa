<?php

namespace App\Models\Trading;

use App\Services\Trading\SessionService;

class Logic
{
    public function __construct(protected readonly SessionService $sessionService) {
    }

    public function handleSocketTickers(array $tickers): array
    {
        $result = [];
        $activeSessions = $this->sessionService->getActiveSessions();
        $result['active_sessions'] = implode(' ', array_map(function($arr) {
            return $arr['symbol'];
        }, $activeSessions));
        foreach ($activeSessions as $activeSession) {
            foreach ($tickers as $ticker) {
                if ($activeSession['symbol'] === $ticker['s']) {
                    // logic
                    $result[$activeSession['symbol'] . '_session_last_price'] = $ticker['c'];
                }
            }
        }
        return $result;
    }
}
