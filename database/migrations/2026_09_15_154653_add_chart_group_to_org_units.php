<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('org_units', function (Blueprint $table) {
            $table->string('chart_group')->default('main')->after('type');
            $table->index('chart_group');
        });
    }

    public function down(): void
    {
        Schema::table('org_units', function (Blueprint $table) {
            $table->dropIndex(['chart_group']);
            $table->dropColumn('chart_group');
        });
    }
};
