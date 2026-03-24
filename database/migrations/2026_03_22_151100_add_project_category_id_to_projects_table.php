<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $column) {
            $column->foreignId('project_category_id')->nullable()->constrained('project_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $column) {
            $column->dropConstrainedForeignId('project_category_id');
        });
    }
};
