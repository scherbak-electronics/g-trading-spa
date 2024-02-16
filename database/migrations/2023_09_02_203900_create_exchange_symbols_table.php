<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new  class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exchange_symbols', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 32)->unique();
            $table->string('status', 32);
            $table->string('base_asset', 32);
            $table->tinyInteger('base_asset_precision');
            $table->string('quote_asset', 32);
            $table->tinyInteger('quote_asset_precision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_symbols');
    }
};

