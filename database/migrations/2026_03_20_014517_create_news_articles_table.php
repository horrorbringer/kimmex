<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('titleKm')->nullable();
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->text('excerptKm')->nullable();
            $table->longText('content');
            $table->longText('contentKm')->nullable();
            $table->string('coverImage')->nullable();
            $table->timestamp('publishedAt')->useCurrent();
            $table->string('category');
            $table->json('tags')->nullable();
            $table->uuid('authorId')->nullable();
            $table->boolean('isFeatured')->default(false);
            $table->string('metaTitle')->nullable();
            $table->text('metaDescription')->nullable();
            $table->string('authorName')->nullable();
            $table->string('authorNameKm')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('isTrending')->default(false);
            $table->string('readTime')->nullable();
            $table->string('readTimeKm')->nullable();
            $table->string('year')->nullable();
            $table->timestamps();

            $table->foreign('authorId')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
