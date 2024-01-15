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
        Schema::create('exchange_tickers', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 32)->unique();
            $table->decimal('price_change', 18, 8);
            $table->decimal('price_change_percent', 18, 8);
            $table->decimal('last_price', 18, 8);
            $table->decimal('open', 18, 8);
            $table->decimal('high', 18, 8);
            $table->decimal('low', 18, 8);
            $table->decimal('volume', 18, 8);
            $table->decimal('quote_volume', 18, 8);
            $table->unsignedBigInteger('open_time');
            $table->unsignedBigInteger('close_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_tickers');
    }
};
