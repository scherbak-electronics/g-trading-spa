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
        Schema::table('exchange_symbols', function (Blueprint $table) {
            $table->decimal('min_price', 60, 30);
            $table->decimal('max_price', 60, 30);
            $table->decimal('min_qty', 60, 30);
            $table->decimal('max_qty', 60, 30);
            $table->decimal('min_order_price', 60, 30);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_symbols', function (Blueprint $table) {
            //
        });
    }
};
