<?php

namespace App\Models\Trading;

use App\Console\View;
use App\Models\Exchange\Order;
use App\Services\Exchange\ExchangeService;
use App\Services\Trading\OrderQueueService;
use App\Services\Trading\SessionService;
use App\Utilities\Data;
use Illuminate\Support\Facades\Log;

class Logic
{
    protected int $sessionId;
    protected Session $session;
    protected bool $isMarketOrdersOnly;
    protected string $prevState;
    protected int $accountUpdatesCounter;
    protected int $orderUpdatesCounter;
    protected int $tickSpeedCounter;
    protected int $startTime;
    protected int   $eventTimestamps;
    public function __construct(
        protected readonly SessionService $sessionService,
        protected readonly ExchangeService $exchangeService,
        protected int $userId,
        protected string $mode,
        protected View &$view
    ) {
        $this->prevState = '';
        $this->orderUpdatesCounter = 0;
        $this->accountUpdatesCounter = 0;
    }

    public function setSessionId(int $sessionId): static
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    public function socketEventHandler(array $event): void
    {
        if (!empty($event[0]['e']) && $event[0]['e'] === '24hrTicker') {
            $this->loadSession();
            $ticker = $this->getSessionTicker($event);
            if ($ticker) {
                $this->processSession($ticker);
                $this->view->setData('event_type', $event[0]['e'])
                    ->setData('items_count', count($event))
                    ->setData('mode', $this->mode === 'f' ? 'Futures' : 'Spot')
                    ->setData('Price', $ticker['c'])
                    ->setSession($this->session);
            }

            // Increment the event counter
            $this->tickSpeedCounter++;

            // Initialize the start time if not already set
            if ($this->startTime === null) {
                $this->startTime = Data::getTimestampInMilliseconds();
            }

            // Calculate elapsed time in minutes
            $currentTime = Data::getTimestampInMilliseconds();
            $elapsedTimeInMinutes = ($currentTime - $this->startTime) / 60000.0; // 60000 milliseconds in a minute

            if ($elapsedTimeInMinutes > 0) {
                $eventsPerMinute = $this->tickSpeedCounter / $elapsedTimeInMinutes;
                echo "Current Speed: {$eventsPerMinute} events per minute\n";
            }
            // Record the event timestamp
            $currentTimestamp = Data::getTimestampInMilliseconds();
            self::$eventTimestamps[] = $currentTimestamp;

            // Remove timestamps older than one hour
            $oneHourAgo = $currentTimestamp - 3600000; // 3600000 milliseconds in one hour
            while (!empty(self::$eventTimestamps) && self::$eventTimestamps[0] < $oneHourAgo) {
                array_shift(self::$eventTimestamps); // Remove the oldest event timestamp
            }

            // Calculate the average speed for the last hour
            if (!empty(self::$eventTimestamps)) {
                $elapsedTimeInHours = ($currentTimestamp - self::$eventTimestamps[0]) / 3600000.0;
                $eventCount = count(self::$eventTimestamps);
                $eventsPerHour = $eventCount / $elapsedTimeInHours;
                echo "Average Speed (last hour): {$eventsPerHour} events per hour\n";
            }
        }
        if (!empty($event['e']) && $event['e'] === 'ACCOUNT_UPDATE') {
            $this->view->setData('event_type', $event['e']);
            // todo: update session with position information like PNL, liquidation price, etc...
            $this->accountUpdatesCounter++;
            Log::info('ACCOUNT_UPDATE event: ', ['data' => json_encode($event, JSON_PRETTY_PRINT)]);
        }
        if (!empty($event['e']) && $event['e'] === 'ORDER_TRADE_UPDATE') {
            $this->view->setData('event_type', $event['e']);
            // todo: update session with position information like PNL, liquidation price, etc...
            $this->orderUpdatesCounter++;
            Log::info('ORDER_TRADE_UPDATE event: ', ['data' => json_encode($event, JSON_PRETTY_PRINT)]);
        }
        $this->view->setData('account_updates: ', $this->accountUpdatesCounter);
        $this->view->setData('order_updates: ', $this->orderUpdatesCounter);
        $this->view->render();
    }

    public function getSessionService(): SessionService
    {
        return $this->sessionService;
    }

    public function getExchangeService(): ExchangeService
    {
        return $this->exchangeService;
    }

    private function loadSession(): void
    {
        if ($this->sessionId) {
            $this->session = $this->sessionService->getSession($this->sessionId);
        }
    }

    private function processSession($ticker): void
    {
        $this->session->current_price = (float)$ticker['c'];
        if ($this->prevState !== $this->session->state) {
            Log::info('Session state change: ', ['data' => $this->session->state]);
            $this->prevState = $this->session->state;
        }

        // TRADING LOGIC
        //
        // 1. check the status of the current session
        if ($this->session->state === 'wait_for_entry_point') {
            if ($this->isEntryPointCondition()) {
                $order = $this->getOpenPositionOrder();
                if (!$order) {
                    return;
                }
                $resp = $this->exchangeService->placeOrder($order);
                if (empty($resp)) {
                    return;
                }
                Log::info('Open position resp: ', ['data' => json_encode($resp, JSON_PRETTY_PRINT)]);
                if (!$this->isMarketOrdersOnly) {
                    $stopLossOrder = $this->getStopLossOrder();
                    $resp = $this->exchangeService->placeOrder($stopLossOrder);
                    if (empty($resp)) {
                        return;
                    }
                    Log::info('Stop loss resp: ', ['data' => json_encode($resp, JSON_PRETTY_PRINT)]);
                    $takeProfitOrder = $this->getTakeProfitOrder();
                    $resp = $this->exchangeService->placeOrder($takeProfitOrder);
                    if (empty($resp)) {
                        return;
                    }
                    Log::info('Take profit resp: ', ['data' => json_encode($resp, JSON_PRETTY_PRINT)]);
                }
                $this->sessionService->openPosition($this->session);
                Log::info('Session state after open position: ', ['data' => $this->session->state]);
            }
        }
        // 2. check the price according to session status and settings
        //    - update session setting and parameters according to price conditions or other things
        //      like timeouts or exchange indicators
        //      todo: implement session management to control session in real time
        //
        if ($this->session->state === 'position_opened') {
            if ($this->isTakeProfitCondition()) {
                if ($this->isMarketOrdersOnly) {
                    $order = $this->getClosePositionOrder();
                    if (!$order) {
                        return;
                    }
                    $resp = $this->exchangeService->placeOrder($order);
                    if (empty($resp)) {
                        return;
                    }
                    Log::info('Close position resp: ', ['data' => json_encode($resp, JSON_PRETTY_PRINT)]);
                }
                $this->sessionService->closePosition($this->session);
            } else {
                if ($this->isStopLossCondition()) {
                    // todo: set stop loss price timestamp, and then check is stop loss safe time is out
                    // if now_time > (stop_loss_price_time + stop_loss_safe_time) then we have to close position
                    if ($this->isMarketOrdersOnly) {
                        $order = $this->getClosePositionOrder();
                        if (!$order) {
                            return;
                        }
                        $resp = $this->exchangeService->placeOrder($order);
                        if (empty($resp)) {
                            return;
                        }
                        Log::info('Close position (stop loss) resp: ', ['data' => json_encode($resp, JSON_PRETTY_PRINT)]);
                    }
                    $this->sessionService->stopLoss($this->session);
                    return;
                }
            }
        }
        // 3. create (or not create) order(s) according to price and session settings
        //    - open or close position if specific conditions are met
        //      todo: implement order management and orders queue
        //
        if ($this->session->state === 'stop_loss_triggered') {

        }
    }

    private function getSessionTicker($event)
    {
        if (empty($this->session)) {
            return null;
        }
        foreach ($event as $ticker) {
            if ($ticker['s'] === $this->session->symbol) {
                return $ticker;
            }
        }
        return null;
    }

    private function isEntryPointCondition(): bool
    {
        if ($this->session->side === 'LONG') {
            if ($this->session->current_price >= $this->session->entry_point_price) {
                return true;
            }
        } elseif ($this->session->side === 'SHORT') {
            if ($this->session->current_price <= $this->session->entry_point_price) {
                return true;
            }
        }
        return false;
    }

    private function isTakeProfitCondition(): bool
    {
        if ($this->session->side === 'LONG') {
            if ($this->session->current_price >= $this->session->take_profit_price) {
                return true;
            }
        } elseif ($this->session->side === 'SHORT') {
            if ($this->session->current_price <= $this->session->take_profit_price) {
                return true;
            }
        }
        return false;
    }

    private function isStopLossCondition(): bool
    {
        if ($this->session->side === 'LONG') {
            if ($this->session->current_price <= $this->session->stop_loss_price) {
                return true;
            }
        } elseif ($this->session->side === 'SHORT') {
            if ($this->session->current_price >= $this->session->stop_loss_price) {
                return true;
            }
        }
        return false;
    }

    private function getOpenPositionOrder(): ?Order
    {
        if (!$this->exchangeService->validateQuantity($this->session->symbol, $this->session->quantity)) {
            return null;
        }
        $order = new Order();
        $order->symbol = $this->session->symbol;
        $order->side = $this->getOrderSide();
        $order->quantity = $this->exchangeService->roundQuantity(
            $this->session->symbol,
            $this->session->quantity
        );
        $order->type = 'MARKET';
        return $order;
    }

    private function getClosePositionOrder(): ?Order
    {
        if (!$this->exchangeService->validateQuantity($this->session->symbol, $this->session->quantity)) {
            return null;
        }
        $order = new Order();
        $order->symbol = $this->session->symbol;
        $order->quantity = $this->session->quantity;
        $order->side = $this->getStopOrderSide();
        $order->type = 'MARKET';
        return $order;
    }

    private function getTakeProfitOrder(): Order
    {
        $order = new Order();
        $order->symbol = $this->session->symbol;
        $order->stopPrice = $this->exchangeService->roundPrice(
            $this->session->symbol,
            $this->session->take_profit_price
        );
        $order->side = $this->getStopOrderSide();
        $order->closePosition = true;
        $order->type = 'TAKE_PROFIT_MARKET';
        return $order;
    }

    private function getStopLossOrder(): Order
    {
        $order = new Order();
        $order->symbol = $this->session->symbol;
        $order->side = $this->getStopOrderSide();
        $order->stopPrice = $this->exchangeService->roundPrice(
            $this->session->symbol,
            $this->session->stop_loss_price
        );
        $order->closePosition = true;
        $order->type = 'STOP_MARKET';
        return $order;
    }

    private function getStopOrderSide(): string
    {
        if ($this->session->side === 'LONG') {
            return 'SELL';
        } elseif ($this->session->side === 'SHORT') {
            return 'BUY';
        }
        return '';
    }

    private function getOrderSide(): string
    {
        if ($this->session->side === 'LONG') {
            return 'BUY';
        } elseif ($this->session->side === 'SHORT') {
            return 'SELL';
        }
        return '';
    }

    public function setIsMarketOrdersOnly(bool $value): static
    {
        $this->isMarketOrdersOnly = $value;
        return $this;
    }

    public function getIsMarketOrdersOnly(): bool
    {
        return $this->isMarketOrdersOnly;
    }
}

