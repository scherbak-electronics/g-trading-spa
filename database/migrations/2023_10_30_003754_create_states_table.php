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
        Schema::create('exchange_states', function (Blueprint $table) {
            $table->id();
            $table->string('param_name', 256)->unique();
            $table->text('value')->nullable();
            $table->decimal('decimal_value', 18, 8)->nullable();
            $table->unsignedBigInteger('bigint_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
