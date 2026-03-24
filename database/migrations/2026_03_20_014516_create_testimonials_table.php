<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('clientName');
            $table->string('clientNameKm')->nullable();
            $table->string('clientRole')->nullable();
            $table->string('clientRoleKm')->nullable();
            $table->string('company')->nullable();
            $table->text('content');
            $table->text('contentKm')->nullable();
            $table->integer('rating')->default(5);
            $table->string('image')->nullable();
            $table->boolean('isFeatured')->default(false);
            $table->integer('orderIndex')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
