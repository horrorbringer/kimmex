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
        Schema::table('services', function (Blueprint $table) {
            $table->string('metaTitle')->nullable();
            $table->text('metaDescription')->nullable();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('metaTitle')->nullable();
            $table->text('metaDescription')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['metaTitle', 'metaDescription']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['metaTitle', 'metaDescription']);
        });
    }
};
