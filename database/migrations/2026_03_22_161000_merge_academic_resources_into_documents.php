<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the redundant academic_resources table
        Schema::dropIfExists('academic_resources');

        // Enhance the documents table for premium unified management
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('isPublic');
            }
            
            // Ensure category can hold the new academic types
            // (Current category is string, which is fine for multi-purpose)
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });

        // We don't restore the academic_resources table here as we are intentional about the merger
    }
};
