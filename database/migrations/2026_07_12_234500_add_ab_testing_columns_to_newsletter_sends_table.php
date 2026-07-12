<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_sends', function (Blueprint $table) {
            $table->string('subject_a')->nullable()->after('custom_intro');
            $table->string('subject_b')->nullable()->after('subject_a');
            $table->boolean('is_ab_test')->default(false)->after('subject_b');
            $table->unsignedInteger('ab_test_percentage')->default(20)->after('is_ab_test');
            $table->string('winning_subject')->nullable()->after('ab_test_percentage');
            $table->timestamp('ab_completed_at')->nullable()->after('winning_subject');
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_sends', function (Blueprint $table) {
            $table->dropColumn([
                'subject_a',
                'subject_b',
                'is_ab_test',
                'ab_test_percentage',
                'winning_subject',
                'ab_completed_at',
            ]);
        });
    }
};
