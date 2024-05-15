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
        Schema::table('exchange_orders', function (Blueprint $table) {
            $table->string('positionSide', 8)->nullable()->charset('ascii')->collation('ascii_bin');
            $table->boolean('reduceOnly')->nullable();
            $table->boolean('closePosition')->nullable();
            $table->decimal('activatePrice', 18, 8)->nullable();
            $table->decimal('callbackRate', 18, 8)->nullable();
            $table->string('workingType', 16)->nullable()->charset('ascii')->collation('ascii_bin');
            $table->boolean('priceProtect')->nullable();
            $table->string('priceMatch', 16)->nullable()->charset('ascii')->collation('ascii_bin');
            $table->unsignedBigInteger('goodTillDate')->nullable();
            $table->decimal('cumQty', 18, 8)->nullable();
            $table->decimal('cumQuote', 18, 8)->nullable();
            $table->decimal('avgPrice', 18, 8)->nullable();
            $table->string('origType', 32)->nullable()->charset('ascii')->collation('ascii_bin');
            $table->unsignedBigInteger('timestamp')->nullable();
            $table->unsignedBigInteger('recvWindow')->nullable();
            $table->boolean('isFutures');

            $table->string('timeInForce', 8)->nullable()->charset('ascii')->collation('ascii_bin')->change();;
            $table->string('newClientOrderId', 64)->nullable()->charset('ascii')->collation('ascii_bin')->change();;
            $table->string('newOrderRespType', 16)->nullable()->charset('ascii')->collation('ascii_bin')->change();;
            $table->string('selfTradePreventionMode', 32)->nullable()->charset('ascii')->collation('ascii_bin')->change();;
            $table->string('status', 32)->nullable()->charset('ascii')->collation('ascii_bin')->change();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
