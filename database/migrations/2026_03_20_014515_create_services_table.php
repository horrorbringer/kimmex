<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('titleKm')->nullable();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('summary')->nullable();
            $table->text('summaryKm')->nullable();
            $table->text('description')->nullable();
            $table->text('descriptionKm')->nullable();
            $table->string('image')->nullable();
            $table->json('features')->nullable();
            $table->integer('orderIndex')->default(0);
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
