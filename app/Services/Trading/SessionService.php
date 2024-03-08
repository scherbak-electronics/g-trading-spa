<?php

namespace App\Services\Trading;

use App\Models\Trading\Session;

class SessionService
{
    public function __construct() {
    }

    public function handleSocketTickers(array $tickers): void
    {
        $activeSessions = $this->getActiveSessions();
        foreach ($activeSessions as $activeSession) {
            foreach ($tickers as $ticker) {
                if ($activeSession['symbol'] === $ticker['s']) {
                    // logic
                }
            }
        }
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

    public function getActiveSessions(): array
    {
        $query = Session::query();
        return [
            [
                'id' => 1,
                'symbol' => 'BTCUSDT',
                'user_id' => 1,
                'state' => 'new'
            ]
        ];
    }
}
