<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // New narrative fields (Translatable)
            $table->text('background')->nullable();
            $table->text('backgroundKm')->nullable();
            $table->text('objectives')->nullable();
            $table->text('objectivesKm')->nullable();
            $table->text('designConcept')->nullable();
            $table->text('designConceptKm')->nullable();
            $table->text('scopeContributions')->nullable();
            $table->text('scopeContributionsKm')->nullable();
            $table->text('engineeringNarrative')->nullable();
            $table->text('engineeringNarrativeKm')->nullable();
            
            // Key Facts
            $table->string('timeline')->nullable(); // e.g., "Jan 2024 - Dec 2025"
            $table->string('scale')->nullable();    // e.g., "50,000 sqm"
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'background', 'backgroundKm',
                'objectives', 'objectivesKm',
                'designConcept', 'designConceptKm',
                'scopeContributions', 'scopeContributionsKm',
                'engineeringNarrative', 'engineeringNarrativeKm',
                'timeline', 'scale'
            ]);
        });
    }
};
