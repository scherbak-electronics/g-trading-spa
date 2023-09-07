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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('homework_id')->nullable();
            $table->string('symbol', 32);
            $table->string('strategy_code', 32)->nullable();
            $table->string('direction', 8)->nullable();  // ['long', 'short']
            $table->decimal('total_investment', 18, 8)->nullable();
            $table->decimal('total_profit', 18, 8)->nullable();
            $table->string('state', 32)->nullable();  // ['new', 'running', 'stopped', 'completed']
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
