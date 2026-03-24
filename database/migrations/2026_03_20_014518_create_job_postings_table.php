<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('titleKm')->nullable();
            $table->string('slug')->unique();
            $table->uuid('departmentId')->nullable();
            $table->string('location')->default('Phnom Penh');
            $table->string('locationKm')->nullable();
            $table->string('type')->default('FULL_TIME');
            $table->text('summary')->nullable();
            $table->text('summaryKm')->nullable();
            $table->text('requirements')->nullable();
            $table->text('requirementsKm')->nullable();
            $table->text('benefits')->nullable();
            $table->text('benefitsKm')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamp('closingDate')->nullable();
            $table->string('experience')->default('2-3 Years');
            $table->string('experienceKm')->nullable();
            $table->string('salary')->default('Negotiable');
            $table->string('salaryKm')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('responsibilitiesKm')->nullable();
            $table->timestamps();

            $table->foreign('departmentId')->references('id')->on('departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
