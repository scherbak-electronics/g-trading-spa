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
        Schema::create('futures_active_symbols', function (Blueprint $table) {
            $table->charset = 'ascii';
            $table->collation = 'ascii_bin';
            $table->id();
            $table->string('symbol', 32)->unique();
            $table->unsignedBigInteger('last_update_time')->nullable();
            $table->string('using_by', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futures_active_symbols');
    }
};
