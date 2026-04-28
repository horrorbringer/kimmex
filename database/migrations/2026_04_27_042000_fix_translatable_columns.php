<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'projects' => [
                'background',
                'objectives',
                'designConcept',
                'scopeContributions',
                'engineeringNarrative'
            ],
            'milestones' => [
                'title',
                'description'
            ],
        ];

        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table))
                continue;

            // Migrate data to temporary array before making structural changes
            $rows = DB::table($table)->get();
            $tableData = [];

            foreach ($rows as $row) {
                $translations = [];
                $rowId = $row->id;

                foreach ($columns as $baseField) {
                    $actualKmField = $baseField . 'Km';

                    $enValue = $row->$baseField ?? '';
                    $kmValue = property_exists($row, $actualKmField) ? ($row->$actualKmField ?? '') : '';

                    // Only convert to JSON if it's not already JSON (to avoid double encoding)
                    $isJson = false;
                    if (is_string($enValue)) {
                        $decoded = json_decode($enValue, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $isJson = true;
                        }
                    }

                    if (!$isJson) {
                        $translations[$baseField] = json_encode([
                            'en' => $enValue,
                            'km' => $kmValue ?: $enValue
                        ]);
                    }
                }
                if (!empty($translations)) {
                    $tableData[$rowId] = $translations;
                }
            }

            // Change columns to json and drop Km columns
            Schema::table($table, function (Blueprint $tableAlter) use ($columns, $table) {
                foreach ($columns as $baseField) {
                    $actualKmField = $baseField . 'Km';

                    // Drop Km column if it exists
                    if (Schema::hasColumn($table, $actualKmField)) {
                        $tableAlter->dropColumn($actualKmField);
                    }

                    // Change base column to json
                    if (DB::getDriverName() === 'pgsql') {
                        $tableAlter->dropColumn($baseField);
                        $tableAlter->json($baseField)->nullable();
                    } else {
                        $tableAlter->json($baseField)->nullable()->change();
                    }
                }
            });

            // Restore migrated data
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
        // One-way migration
    }
};
