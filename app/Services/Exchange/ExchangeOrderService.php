<?php
namespace App\Services\Exchange;
use App\Contracts\Exchange\ApiInterface;
use App\Models\Exchange\Local\ExchangeState;
use App\Models\Exchange\Local\ExchangeStorage;
use App\Models\Exchange\Order;
use App\Services\Exchange\Binance\Api as ApiSpot;
use App\Services\Exchange\Binance\ApiFutures;

class ExchangeOrderService
{
    public function __construct(
        protected ExchangeState $state,
        protected ExchangeStorage $db
    ){}

    public function sendOrder(Order $order)
    {
        if ($order->isFutures) {
            $api = app(ApiFutures::class);
        } else {
            $api = app(ApiSpot::class);
        }
        return $api->createOrder($order);
    }
}
