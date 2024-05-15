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
        Schema::table('exchange_futures_klines', function (Blueprint $table) {
            $table->string('interval')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('symbol')->charset('ascii')->collation('ascii_bin')->change();
        });
        Schema::table('sessions', function (Blueprint $table) {
            $table->string('default_timeframe')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('small_timeframe')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('big_timeframe')->charset('ascii')->collation('ascii_bin')->change();
            $table->string('current_timeframe')->charset('ascii')->collation('ascii_bin')->change();
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
