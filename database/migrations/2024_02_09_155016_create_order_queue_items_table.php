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
        Schema::create('order_queue_items', function (Blueprint $table) {
            $table->id();
            $table->integer('exchange_order_id')->nullable();
            $table->string('status', 32);
            $table->unsignedBigInteger('time_ms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_queue_items');
    }
};
