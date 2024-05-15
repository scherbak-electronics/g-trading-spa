<?php

namespace App\Services\Trading;

use App\Models\Exchange\Order;
use App\Models\Trading\OrderQueue;
use App\Models\Trading\OrderQueueItem;
use App\Services\Exchange\ExchangeOrderService;
use App\Services\Exchange\ExchangeService;
use App\Services\Exchange\ExchangeServiceFactory;
use App\Utilities\Data;
use Illuminate\Support\Facades\Log;

class OrderQueueService
{
    public function __construct(
        private readonly ExchangeOrderService $exchangeOrderService
    ){}

    public function placeOrder(Order $order, int $sessionId)
    {
        OrderQueueItem::create([
            'exchange_order_id' => $order->id,
            'session_id' => $sessionId,
            'status' => 'queued'
        ]);
    }

    public function processQueue($sessionId)
    {
        $queueItems = OrderQueueItem::where('status', 'queued')
            ->where('session_id', $sessionId)
            ->get();

        foreach ($queueItems as $item) {
            $order = $item->getOrder();
            $success = $this->exchangeOrderService->sendOrder($order);

            if ($success) {
                $item->update(['status' => 'sent']);
            } else {
                $item->update(['status' => 'failed']);
                break;
            }
        }
    }
}
