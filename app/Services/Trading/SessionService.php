<?php

namespace App\Services\Trading;

use App\Models\Trading\Session;
use Illuminate\Support\Facades\Auth;

class SessionService
{
    public function __construct() {
    }

    public function create(array $data): array
    {
        $userId = auth()->id();
        if (empty($userId)) {
            return [];
        }
        $data['user_id'] = $userId;
        $data['default_timeframe'] = '1h';
        $data['small_timeframe'] = '15m';
        $data['big_timeframe'] = '1d';
        $data['current_timeframe'] = '1h';
        $data['side'] = 'long';
        $data['state'] = 'new';
        return Session::query()->create($data)->toArray();
    }

    public function getSession(int $id): array
    {
        $session = Session::find($id);
        if (empty($session)) {
            return [];
        }
        return $session->toArray();
    }

    public function getActiveSessions(): array
    {
        $query = Session::query();
        return $query->select('*')->get()->toArray();
    }

    public function start($id): array
    {
        return [];
    }

    public function stop($id): array
    {
        return [];
    }

    public function update(array $data): array
    {
        return [];
    }
}
