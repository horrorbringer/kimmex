<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('url');
            $table->string('caption')->nullable();
            $table->uuid('projectId');
            $table->timestamps();

            $table->foreign('projectId')->references('id')->on('projects')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_images');
    }
};
