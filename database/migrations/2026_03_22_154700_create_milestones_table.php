<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('year');
            $table->string('title');
            $table->string('titleKm')->nullable();
            $table->text('description')->nullable();
            $table->text('descriptionKm')->nullable();
            $table->string('image')->nullable();
            $table->integer('sortOrder')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
