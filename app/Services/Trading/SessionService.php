<?php

namespace App\Services\Trading;

use App\Models\Trading\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SessionService
{
    public function __construct() {
    }

    public function create(array $data): ?Session
    {
        $data['default_timeframe'] = '1h';
        $data['small_timeframe'] = '15m';
        $data['big_timeframe'] = '1d';
        $data['current_timeframe'] = '1h';
        $data['side'] = 'LONG';
        // ['new', 'active', 'stopped', 'completed']
        $data['status'] = 'new';
        // ['wait_for_entry_point', 'position_opened']
        $data['state'] = 'wait_for_entry_point';
        return Session::query()->create($data);
    }

    public function getSession(int $id): ?Session
    {
        return Session::find($id);
    }

    public function getActiveSessions($userId): Collection
    {
        return Session::where('status', 'active')
            ->where('user_id', $userId)
            ->get();
    }

    public function getUserSessions($userId): Collection
    {
        return Session::where('user_id', $userId)->get();
    }

    public function start($id): ?Session
    {
        $session = $this->getSession($id);
        if ($session) {
            $session->status = 'active';
        }
        return $session;
    }

    public function stop($id): ?Session
    {
        $session = $this->getSession($id);
        if ($session) {
            $session->status = 'stopped';
        }
        return $session;
    }

    public function update(array $data): bool|int
    {
        return Session::where('id', $data['id'])->update($data);
    }
}
