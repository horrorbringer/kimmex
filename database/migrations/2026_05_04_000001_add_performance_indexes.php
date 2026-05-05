<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add missing indexes on columns that are commonly used in WHERE clauses
 * across the frontend pages. These indexes dramatically speed up filtered
 * queries, especially whereHas subqueries against category tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Documents: slug lookups, active/public filtering, category joins
        Schema::table('documents', function (Blueprint $table) {
            $table->index('slug', 'idx_documents_slug');
            $table->index(['isActive', 'isPublic'], 'idx_documents_active_public');
            $table->index('document_category_id', 'idx_documents_category_id');
        });

        // Document Categories: active filtering (used in whereHas)
        Schema::table('document_categories', function (Blueprint $table) {
            $table->index('isActive', 'idx_document_categories_active');
        });

        // Projects: slug lookups, active filtering
        Schema::table('projects', function (Blueprint $table) {
            $table->index('slug', 'idx_projects_slug');
            $table->index('isActive', 'idx_projects_active');
        });

        // News Articles: slug lookups, active filtering
        Schema::table('news_articles', function (Blueprint $table) {
            $table->index('slug', 'idx_news_articles_slug');
            $table->index('isActive', 'idx_news_articles_active');
        });

        // Services: active filtering, slug lookups
        Schema::table('services', function (Blueprint $table) {
            $table->index('slug', 'idx_services_slug');
            $table->index('isActive', 'idx_services_active');
        });

        // Job Postings: active filtering, slug lookups
        Schema::table('job_postings', function (Blueprint $table) {
            $table->index('slug', 'idx_job_postings_slug');
            $table->index('isActive', 'idx_job_postings_active');
        });

        // Project Categories: active filtering
        Schema::table('project_categories', function (Blueprint $table) {
            $table->index('isActive', 'idx_project_categories_active');
        });

        // Milestones: active + sort order
        Schema::table('milestones', function (Blueprint $table) {
            $table->index(['isActive', 'sortOrder'], 'idx_milestones_active_sort');
        });

        // Org Units: active + parent + order (for org chart tree queries)
        Schema::table('org_units', function (Blueprint $table) {
            $table->index(['isActive', 'parentId', 'orderIndex'], 'idx_org_units_active_parent_order');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('idx_documents_slug');
            $table->dropIndex('idx_documents_active_public');
            $table->dropIndex('idx_documents_category_id');
        });

        Schema::table('document_categories', function (Blueprint $table) {
            $table->dropIndex('idx_document_categories_active');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_slug');
            $table->dropIndex('idx_projects_active');
        });

        Schema::table('news_articles', function (Blueprint $table) {
            $table->dropIndex('idx_news_articles_slug');
            $table->dropIndex('idx_news_articles_active');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('idx_services_slug');
            $table->dropIndex('idx_services_active');
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex('idx_job_postings_slug');
            $table->dropIndex('idx_job_postings_active');
        });

        Schema::table('project_categories', function (Blueprint $table) {
            $table->dropIndex('idx_project_categories_active');
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->dropIndex('idx_milestones_active_sort');
        });

        Schema::table('org_units', function (Blueprint $table) {
            $table->dropIndex('idx_org_units_active_parent_order');
        });
    }
};
