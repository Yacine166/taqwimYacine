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
      Schema::create('beta_users', function (Blueprint $table) {
          $table->id();
          $table->string("username");
          $table->string("email");
          $table->string("phone");
          $table->string("organization_name");
          $table->string("activity_sector");
          $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beta_users');
    }
};
