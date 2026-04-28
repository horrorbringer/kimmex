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
        Schema::create('methodology_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('title');
            $table->json('description');
            $table->string('icon')->default('lucide-settings');
            $table->integer('orderIndex')->default(0);
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('methodology_steps');
    }
};
