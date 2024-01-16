<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        * field name                type    is mandatory
        * symbol	                STRING	YES
        * side	                    STRING	YES
        * type	                    STRING	YES
        * timeInForce	            STRING	NO
        * quantity	                DECIMAL	NO
        * quoteOrderQty	            DECIMAL	NO
        * price	                    DECIMAL	NO
        * newClientOrderId	        STRING	NO	A unique id among open orders. Automatically generated if not sent.
        * strategyId	            INT	    NO
        * strategyType	            INT	    NO	The value cannot be less than 1000000.
        * stopPrice	                DECIMAL	NO	Used with STOP_LOSS, STOP_LOSS_LIMIT, TAKE_PROFIT, and TAKE_PROFIT_LIMIT orders.
        * trailingDelta	            LONG	NO	Used with STOP_LOSS, STOP_LOSS_LIMIT, TAKE_PROFIT, and TAKE_PROFIT_LIMIT orders.
        * icebergQty	            DECIMAL	NO	Used with LIMIT, STOP_LOSS_LIMIT, and TAKE_PROFIT_LIMIT to create an iceberg order.
        * newOrderRespType	        STRING	NO	Set the response JSON. ACK, RESULT, or FULL; MARKET and LIMIT order types default to FULL, all other orders default to ACK.
        * selfTradePreventionMode	STRING	NO	The allowed enums is dependent on what is configured on the symbol. The possible supported values are EXPIRE_TAKER, EXPIRE_MAKER, EXPIRE_BOTH, NONE.
        * */
        Schema::create('exchange_orders', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 32);
            $table->string('side', 8);
            $table->string('type', 32);
            $table->integer('orderId')->nullable();
            $table->integer('clientOrderId')->nullable();
            $table->integer('origClientOrderId')->nullable();
            $table->string('timeInForce', 8)->nullable();
            $table->decimal('quantity', 18, 8)->nullable();
            $table->decimal('quoteOrderQty', 18, 8)->nullable();
            $table->decimal('price', 18, 8)->nullable();
            $table->string('newClientOrderId', 64)->nullable();
            $table->integer('strategyId')->nullable();
            $table->integer('strategyType')->nullable();
            $table->decimal('stopPrice', 18, 8)->nullable();
            $table->integer('trailingDelta')->nullable();
            $table->decimal('icebergQty', 18, 8)->nullable();
            $table->string('newOrderRespType', 16)->nullable();
            $table->string('selfTradePreventionMode', 32)->nullable();
            $table->unsignedBigInteger('transactTime')->nullable();
            $table->integer('orderListId')->nullable();
            $table->decimal('origQty', 18, 8)->nullable();
            $table->decimal('executedQty', 18, 8)->nullable();
            $table->decimal('cummulativeQuoteQty', 18, 8)->nullable();
            $table->string('status', 32)->nullable();
            $table->unsignedBigInteger('workingTime')->nullable();
            $table->text('fills')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_orders');
    }
};
