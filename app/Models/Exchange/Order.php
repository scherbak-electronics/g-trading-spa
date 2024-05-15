<?php

namespace App\Models\Exchange;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'symbol',
        'side',
        'type',
        'timeInForce',
        'quantity',
        'quoteOrderQty',
        'price',
        'newClientOrderId',
        'strategyId',
        'strategyType',
        'stopPrice',
        'trailingDelta',
        'icebergQty',
        'newOrderRespType',
        'selfTradePreventionMode',
        'recvWindow',
        'timestamp',
        'transactTime',
        'orderId',
        'orderListId',
        'origQty',
        'executedQty',
        'cummulativeQuoteQty',
        'status',
        'workingTime',
        'fills',
        'positionSide',
        'reduceOnly',
        'closePosition',
        'activatePrice',
        'callbackRate',
        'workingType',
        'priceProtect',
        'priceMatch',
        'goodTillDate',
        'cumQty',
        'cumQuote',
        'avgPrice',
        'origType',
        'isFutures'
    ];

    protected $table = 'exchange_orders';
    public $timestamps = false;
}
