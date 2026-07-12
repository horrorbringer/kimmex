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
        Schema::create('news_article_project', function (Blueprint $table) {
            $table->uuid('news_article_id');
            $table->uuid('project_id');

            $table->primary(['news_article_id', 'project_id']);

            $table->foreign('news_article_id')
                ->references('id')
                ->on('news_articles')
                ->cascadeOnDelete();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_article_project');
    }
};
