<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add foreign key to documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('document_category_id')->nullable()->after('category')->constrained('document_categories')->nullOnDelete();
        });

        // Seed default categories
        $categories = [
            ['name' => json_encode(['en' => 'Case Study', 'km' => 'ករណីសិក្សា']), 'slug' => 'case-study', 'icon' => 'heroicon-o-academic-cap', 'sort_order' => 1],
            ['name' => json_encode(['en' => 'Engineering Standard', 'km' => 'ស្តង់ដារវិស្វកម្ម']), 'slug' => 'engineering-standard', 'icon' => 'heroicon-o-cog-6-tooth', 'sort_order' => 2],
            ['name' => json_encode(['en' => 'Safety Manual', 'km' => 'សៀវភៅសុវត្ថិភាព']), 'slug' => 'safety-manual', 'icon' => 'heroicon-o-shield-check', 'sort_order' => 3],
            ['name' => json_encode(['en' => 'Legal Document', 'km' => 'ឯកសារច្បាប់']), 'slug' => 'legal-document', 'icon' => 'heroicon-o-scale', 'sort_order' => 4],
            ['name' => json_encode(['en' => 'Technical Manual', 'km' => 'សៀវភៅបច្ចេកទេស']), 'slug' => 'technical-manual', 'icon' => 'heroicon-o-wrench-screwdriver', 'sort_order' => 5],
            ['name' => json_encode(['en' => 'Company Policy', 'km' => 'គោលនយោបាយក្រុមហ៊ុន']), 'slug' => 'company-policy', 'icon' => 'heroicon-o-document-text', 'sort_order' => 6],
            ['name' => json_encode(['en' => 'Project Report', 'km' => 'របាយការណ៍គម្រោង']), 'slug' => 'project-report', 'icon' => 'heroicon-o-clipboard-document-list', 'sort_order' => 7],
            ['name' => json_encode(['en' => 'Training Material', 'km' => 'សម្ភារៈបណ្តុះបណ្តាល']), 'slug' => 'training-material', 'icon' => 'heroicon-o-book-open', 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            $category['created_at'] = now();
            $category['updated_at'] = now();
            \Illuminate\Support\Facades\DB::table('document_categories')->insert($category);
        }
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['document_category_id']);
            $table->dropColumn('document_category_id');
        });

        Schema::dropIfExists('document_categories');
    }
};
