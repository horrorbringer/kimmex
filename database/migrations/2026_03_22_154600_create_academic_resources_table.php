<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('titleKm')->nullable();
            $table->string('slug')->unique();
            $table->string('type'); // e.g., 'CASE_STUDY', 'ENGINEERING_STANDARD', 'SAFETY_MANUAL'
            $table->text('content')->nullable();
            $table->text('contentKm')->nullable();
            $table->string('fileUrl')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('isFeatured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_resources');
    }
};
