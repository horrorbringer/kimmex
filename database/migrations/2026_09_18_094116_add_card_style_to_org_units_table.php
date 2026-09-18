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
        Schema::table('org_units', function (Blueprint $table) {
            $table->string('card_style')->nullable()->after('chart_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('org_units', function (Blueprint $table) {
            $table->dropColumn('card_style');
        });
    }
};
