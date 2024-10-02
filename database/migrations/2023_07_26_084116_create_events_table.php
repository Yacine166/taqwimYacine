<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('events', function (Blueprint $table) {
      $table->id();
      $table->foreignId('category_id')->constrained();
      $table->string('name');
      $table->string('name_fr');
      $table->string('name_ar');
      $table->text('details');
      $table->text('details_fr');
      $table->text('details_ar');
      $table->enum('cycle',range(1, 12, 1))->default(1);
      $table->enum('cycle_month',["monthly",...range(1,12,1)])->default("monthly");
      $table->enum('cycle_day', range(1, 31, 1));
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('events');
  }
};