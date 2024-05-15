<?php

namespace App\Models\Trading;

use App\Models\Exchange\Order;
use App\Services\Exchange\ExchangeService;
use App\Services\Trading\OrderQueueService;
use App\Services\Trading\SessionService;

class Logic
{
    public function __construct(
        protected readonly SessionService $sessionService,
        protected readonly ExchangeService $exchangeService,
        protected int $userId,
        protected string $mode
    ) {
    }

    public function socketEventHandler(array $event): array
    {
        $result = [];
        if (!empty($event[0]['e']) && $event[0]['e'] === '24hrTicker') {
            $result['event_type'] = $event[0]['e'];
            $activeSessions = $this->sessionService->getActiveSessions($this->userId);
            foreach ($activeSessions as $activeSession) {
                $result['Active Session'] = '';
                $result['Symbol'] = $activeSession->symbol;
                $result['State'] = $activeSession->state;
                $result['Entry Point Price'] = $activeSession->entry_point_price;

                foreach ($event as $ticker) {
                    if ($activeSession->symbol === $ticker['s']) {
                        $currentPrice = $ticker['c'];
                        // TRADING LOGIC
                        //
                        // 1. check the status of the current session
                        if ($activeSession->state === 'wait_for_entry_point') {
                            if ($activeSession->side === 'LONG') {
                                if ($currentPrice >= $activeSession->entry_point_price) {
                                    $order = new Order();
                                    $order->symbol = $activeSession->symbol;
                                    $order->side = 'LONG';
                                    $order->quantity = $activeSession->quantity;
                                    $order->type = 'MARKET';
                                    //$this->exchangeService->placeOrder($order);
                                    $activeSession->state = 'position_opened';
                                    $activeSession->save();
                                }
                            } elseif ($activeSession['side'] === 'SHORT') {
                                if ($currentPrice <= $activeSession->entry_point_price) {
                                    $order = new Order();
                                    $order->symbol = $activeSession->symbol;
                                    $order->side = 'SHORT';
                                    $order->quantity = $activeSession->quantity;
                                    $order->type = 'MARKET';
                                    //$this->exchangeService->placeOrder($order);
                                    $activeSession->state = 'position_opened';
                                    $activeSession->save();
                                }
                            }
                        }
                        // 2. check the price according to session status and settings
                        //    - update session setting and parameters according to price conditions or other things
                        //      like timeouts or exchange indicators
                        //      todo: implement session management to control session in real time
                        //
                        if ($activeSession->state === 'position_opened') {

                        }
                        // 3. create (or not create) order(s) according to price and session settings
                        //    - open or close position if specific conditions are met
                        //      todo: implement order management and orders queue
                        //
                        $result[$activeSession['symbol'] . '_session_last_price'] = $ticker['c'];
                    }
                }
            }
        }
        if (!empty($event['e']) && $event['e'] === 'ACCOUNT_UPDATE') {
            $result['event_type'] = $event['e'];
        }
        return $result;
    }

    public function getSessionService(): SessionService
    {
        return $this->sessionService;
    }

    public function getExchangeService(): ExchangeService
    {
        return $this->exchangeService;
    }
}

