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
        'orderId',
        'clientOrderId',
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
        'transactTime',
        'orderListId',
        'origQty',
        'executedQty',
        'cummulativeQuoteQty',
        'status',
        'workingTime',
        'fills',
    ];
    protected $table = 'exchange_orders';
    public $timestamps = false;
}
