<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('fileUrl');
            $table->string('fileSize')->nullable();
            $table->string('fileType')->nullable();
            $table->string('thumbnailUrl')->nullable();
            $table->string('category');
            $table->uuid('departmentId')->nullable();
            $table->boolean('isPublic')->default(true);
            $table->integer('downloadCount')->default(0);
            $table->timestamps();

            $table->foreign('departmentId')->references('id')->on('departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
