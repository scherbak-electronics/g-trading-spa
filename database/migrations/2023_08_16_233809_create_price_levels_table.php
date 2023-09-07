<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_levels', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 18, 8);
            $table->string('type', 32)->nullable();
            $table->string('direction', 8)->nullable();  // ['long', 'short']
            $table->string('title', 100)->nullable();
            $table->unsignedBigInteger('homework_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_levels');
    }
};
