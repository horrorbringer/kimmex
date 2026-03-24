<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_categories', function (Blueprint $column) {
            $column->foreignId('parent_id')->nullable()->after('id')->constrained('project_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('project_categories', function (Blueprint $column) {
            $column->dropConstrainedForeignId('parent_id');
        });
    }
};
