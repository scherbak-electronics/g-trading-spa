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
        Schema::table('sessions', function (Blueprint $table) {
            $table->decimal('current_price', 18, 8)->nullable();
            $table->decimal('main_level_price', 18, 8)->nullable();
            $table->decimal('entry_point_price', 18, 8)->nullable();
            $table->decimal('take_profit_price', 18, 8)->nullable();
            $table->unsignedBigInteger('take_profit_timeout')->nullable();
            $table->decimal('stop_loss_price', 18, 8)->nullable();
            $table->decimal('trailing_delta', 18, 8)->nullable();
            $table->unsignedBigInteger('stop_loss_safe_time')->nullable();
            $table->string('default_timeframe', 3);
            $table->string('small_timeframe', 3);
            $table->string('big_timeframe', 3);
            $table->string('current_timeframe', 3);
            $table->renameColumn('direction', 'side');
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
