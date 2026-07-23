<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('is_read');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_read']);
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
