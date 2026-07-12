<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('status')->default('DRAFT')->after('isActive');
        });

        // Migrate existing data: isActive=true → OPEN, isActive=false → CLOSED
        DB::table('job_postings')->where('isActive', true)->update(['status' => 'OPEN']);
        DB::table('job_postings')->where('isActive', false)->update(['status' => 'CLOSED']);

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex('idx_job_postings_active');
            $table->dropColumn('isActive');
            $table->index('status', 'idx_job_postings_status');
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->boolean('isActive')->default(true)->after('benefits');
        });

        DB::table('job_postings')->where('status', 'OPEN')->update(['isActive' => true]);
        DB::table('job_postings')->whereIn('status', ['CLOSED', 'FILLED', 'DRAFT'])->update(['isActive' => false]);

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
