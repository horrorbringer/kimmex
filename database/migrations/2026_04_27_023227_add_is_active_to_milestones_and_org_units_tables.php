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
        Schema::table('milestones', function (Blueprint $table) {
            $table->boolean('isActive')->default(true);
        });
        Schema::table('org_units', function (Blueprint $table) {
            $table->boolean('isActive')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn('isActive');
        });
        Schema::table('org_units', function (Blueprint $table) {
            $table->dropColumn('isActive');
        });
    }
};
