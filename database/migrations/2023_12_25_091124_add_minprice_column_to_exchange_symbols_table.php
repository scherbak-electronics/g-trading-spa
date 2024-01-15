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
            $table->string('min_price', 16);
            $table->dropColumn('quote_asset_precision');
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
