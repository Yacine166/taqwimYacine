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
        Schema::create('exceptions', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->enum('old_month', range(1, 12, 1));
            $table->enum('old_day', range(1, 31, 1));
            $table->enum('new_month', range(1, 12, 1));
            $table->enum('new_day', range(1, 31, 1));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exceptions');
    }
};
