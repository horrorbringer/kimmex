<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_categories', function (Blueprint $column) {
            $column->id();
            $column->json('name');
            $column->string('slug')->unique();
            $column->json('description')->nullable();
            $column->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_categories');
    }
};
