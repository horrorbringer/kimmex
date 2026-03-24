<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'services' => ['title', 'summary', 'description'],
            'projects' => ['title', 'location', 'description'],
            'testimonials' => ['clientName' => 'clientNameKm', 'clientRole' => 'clientRoleKm', 'content' => 'contentKm'],
            'job_postings' => ['title', 'location', 'summary', 'requirements', 'benefits', 'experience', 'salary', 'responsibilities'],
            'news_articles' => ['title', 'excerpt', 'content', 'authorName', 'readTime'],
            'departments' => ['name', 'description'],
            'org_units' => ['title'],
            'documents' => ['title', 'description'],
            'partners' => ['name'],
        ];

        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) continue;

            // Migrate data to temporary array before making structural changes
            $rows = DB::table($table)->get();
            $tableData = [];

            foreach ($rows as $row) {
                $translations = [];
                $rowId = $row->id;
                
                // For each field, build the JSON translation object
                foreach ($columns as $baseField => $kmField) {
                    // Normalize: if the value is numerically indexed, it means base name == field pair key
                    if (is_numeric($baseField)) {
                        $actualBaseField = $kmField;
                        $actualKmField = $kmField . 'Km';
                    } else {
                        $actualBaseField = $baseField;
                        $actualKmField = $kmField;
                    }

                    $enValue = $row->$actualBaseField ?? '';
                    $kmValue = property_exists($row, $actualKmField) ? ($row->$actualKmField ?? '') : '';

                    $translations[$actualBaseField] = json_encode([
                        'en' => $enValue,
                        'km' => $kmValue ?: $enValue // Fallback KM to EN if empty
                    ]);
                }
                $tableData[$rowId] = $translations;
            }

            // Change columns to json and drop Km columns
            Schema::table($table, function (Blueprint $tableAlter) use ($columns) {
                foreach ($columns as $baseField => $kmField) {
                    $actualBaseField = is_numeric($baseField) ? $kmField : $baseField;
                    $actualKmField = is_numeric($baseField) ? $kmField . 'Km' : $kmField;

                    // Drop Km column if it exists
                    if (Schema::hasColumn($tableAlter->getTable(), $actualKmField)) {
                        $tableAlter->dropColumn($actualKmField);
                    }

                    // Change base column to json (longText or json depending on DB)
                    $tableAlter->json($actualBaseField)->nullable()->change();
                }
            });

            // Restore migrated data as JSON strings
            foreach ($tableData as $id => $fields) {
                DB::table($table)->where('id', $id)->update($fields);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversing JSON back to two columns is complex and usually not needed for a refactor of this size.
        // If needed, it would reverse the logic above. Skipping for now as this is a one-way architectural shift.
    }
};
