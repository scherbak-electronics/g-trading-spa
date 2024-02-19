<?php

namespace App\Services\Trading;

use App\Models\Trading\Session;

class SessionService
{
    public function __construct() {
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
            'id' => 1,
            'symbol' => 'BTCUSDT',
            'user_id' => 1,
            'state' => 'new'
        ];
    }
}
