<?php

namespace App\Services\Trading;

use App\Models\Exchange\Order;
use App\Models\Trading\OrderQueueItem;
use App\Services\Exchange\Service;
use App\Utilities\Data;
use Illuminate\Support\Facades\Log;

class OrderQueueService
{
    const PROCESSING_STATUSES = ['new'];

    private int $startTime;

    public function __construct(
        protected Service $exchangeService
    ) {
    }

    public function addOrderById(int $orderId): OrderQueueService
    {
        $query = OrderQueueItem::query();
        $query->insert([
            'exchange_order_id' => $orderId,
            'status' => 'new',
            'time_ms' => Data::getTimestampInMilliseconds()
        ]);
        return $this;
    }

    public function getNextOrderId(): int
    {
        return 1;
    }

    public function process(): void
    {
        $this->processStart();
        $processingItems = $this->getProcessingItems();
        foreach ($processingItems as $item) {
            $query = Order::query();
            $order = $query->find($item['exchange_order_id']);
            $orderData = $order->toArray();
            Log::channel('order_queue')->info('Order found. id: ' . $orderData['id']);
            $query = OrderQueueItem::query();
            $query->where('id', $item['id']);
            $query->update(['status' => 'processed']);
        }
        $this->processStop();
    }

    private function processStart(): void
    {
        $this->startTime = microtime(true);
        Log::channel('order_queue')->info('Order queue items processing start.');
    }

    private function processStop(): void
    {
        $endTime = microtime(true);
        $executionTime = ($endTime - $this->startTime) * 1000;
        Log::channel('order_queue')->info('Order queue items processing stop. Exec. time is ' . $executionTime . ' ms.');
    }

    private function getProcessingItems(): array
    {
        $query = OrderQueueItem::query();
        $query->whereIn('status', self::PROCESSING_STATUSES);
        $query->orderBy('time_ms', 'desc');
        $items = $query->get();
        return $items->toArray();
    }
}
