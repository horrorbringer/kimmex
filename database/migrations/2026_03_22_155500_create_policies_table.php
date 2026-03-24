<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $blueprint) {
            $blueprint->uuid('id')->primary();
            $blueprint->json('title');
            $blueprint->string('slug')->unique();
            $blueprint->json('content')->nullable();
            $blueprint->string('icon')->nullable();
            $blueprint->integer('sort_order')->default(0);
            $blueprint->boolean('is_public')->default(true);
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
