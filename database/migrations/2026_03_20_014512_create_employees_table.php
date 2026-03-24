<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->text('bio')->nullable();
            $table->text('bioKm')->nullable();
            $table->string('experience')->nullable();
            $table->string('location')->nullable();
            $table->string('locationKm')->nullable();
            $table->string('specialization')->nullable();
            $table->string('specializationKm')->nullable();
            $table->string('role')->nullable();
            $table->string('roleKm')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
