<?php

use App\Models\Document;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (! Schema::hasColumn('documents', 'slug')) {
                $table->string('slug')->after('title')->nullable();
            }
        });

        // Populate existing records with slugs if any
        Document::all()->each(function ($doc) {
            if (empty($doc->slug)) {
                $doc->slug = Str::slug($doc->getTranslation('title', config('app.locale')));
                $doc->save();
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
