<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('klines', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 32);
            $table->string('interval', 3);                       //1s 1m 3m 5m 15m 30m 1h 2h 4h 6h 8h 12h 1d 3d 1w 1M
            $table->unsignedBigInteger('open_time');                     //1499040000000,      // Kline open time
            $table->decimal('open', 18, 8);                 //"0.01634790",       // Open price
            $table->decimal('high', 18, 8);                 //"0.80000000",       // High price
            $table->decimal('low', 18, 8);                  //"0.01575800",       // Low price
            $table->decimal('close', 18, 8);                //"0.01577100",       // Close price
            $table->decimal('volume', 18, 8);               //"148976.11427815",  // Volume
            $table->unsignedBigInteger('close_time');                   //1499644799999,      // Kline Close time
            $table->decimal('quote_volume', 18, 8);         //"2434.19055334",    // Quote asset volume
            $table->unsignedInteger('trades');                          //308,                // Number of trades
            $table->decimal('buy_base_volume', 18, 8);      //"1756.87402397",    // Taker buy base asset volume
            $table->decimal('buy_quote_volume', 18, 8);     //"28.46694368",      // Taker buy quote asset volume
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klines');
    }
};
