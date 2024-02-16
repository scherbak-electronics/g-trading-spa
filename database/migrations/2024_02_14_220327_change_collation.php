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
        Schema::table('variable_decimals', function (Blueprint $table) {
            $table->string('name')->charset('ascii')->collation('ascii_bin')->change();
        });

        Schema::table('variable_big_uints', function (Blueprint $table) {
            $table->string('name')->charset('ascii')->collation('ascii_bin')->change();
        });

        Schema::table('variable_texts', function (Blueprint $table) {
            $table->string('name')->charset('ascii')->collation('ascii_bin')->change();
        });

        Schema::table('exchange_orders', function (Blueprint $table) {
            $table->string('type')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('side')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('symbol')->charset('ascii')->collation('ascii_bin')->change();
        });

        Schema::table('exchange_symbols', function (Blueprint $table) {
            $table->string('permissions', 512)->charset('ascii')->collation('ascii_bin')->change();
            $table->string('order_types')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('quote_asset')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('base_asset')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('status')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('symbol')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('min_price')->charset('ascii')->collation('ascii_bin')->change();
        });

        Schema::table('exchange_klines', function (Blueprint $table) {
            $table->string('interval')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('symbol')->charset('ascii')->collation('ascii_bin')->change();
        });

        Schema::table('exchange_tickers', function (Blueprint $table) {
            $table->string('symbol')->charset('ascii')->collation('ascii_bin')->change();
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->string('state')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('direction')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('strategy_code')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('symbol')->charset('ascii')->collation('ascii_bin')->change();
        });

        Schema::table('bars', function (Blueprint $table) {
            $table->string('type')->charset('ascii')->collation('ascii_bin')->change();
        });

        Schema::table('price_levels', function (Blueprint $table) {
            $table->string('type')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('direction')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('title')->charset('ascii')->collation('ascii_bin')->change();
        });

        Schema::table('homework', function (Blueprint $table) {
            $table->string('timeframe')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('strategy')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('direction')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('symbol')->charset('ascii')->collation('ascii_bin')->change();
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
