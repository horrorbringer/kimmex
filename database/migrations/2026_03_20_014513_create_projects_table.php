<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('titleKm')->nullable();
            $table->string('slug')->unique();
            $table->string('category');
            $table->string('status')->default('ONGOING');
            $table->string('location')->nullable();
            $table->string('locationKm')->nullable();
            $table->string('client')->nullable();
            $table->timestamp('completionDate')->nullable();
            $table->text('description')->nullable();
            $table->text('descriptionKm')->nullable();
            $table->string('heroImage')->nullable();
            $table->boolean('isFeatured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
