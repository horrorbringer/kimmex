<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('jobId');
            $table->string('applicantName');
            $table->string('email');
            $table->string('phone');
            $table->string('resumeUrl');
            $table->text('coverLetter')->nullable();
            $table->string('status')->default('PENDING');
            $table->timestamp('submittedAt')->useCurrent();
            $table->timestamps();

            $table->foreign('jobId')->references('id')->on('job_postings')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
