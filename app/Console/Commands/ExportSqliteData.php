<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportSqliteData extends Command
{
    protected $signature = 'db:export-sqlite';
    protected $description = 'Export all SQLite tables to JSON files';

    public function handle(): void
    {
        $exportPath = base_path('exports');
        File::ensureDirectoryExists($exportPath);

        $tables = DB::select("
            SELECT name FROM sqlite_master 
            WHERE type='table' 
            AND name NOT LIKE 'sqlite_%'
            AND name != 'migrations'
        ");

        $this->info('Found ' . count($tables) . ' tables');
        $bar = $this->output->createProgressBar(count($tables));

        foreach ($tables as $table) {
            $name = $table->name;
            $data = DB::table($name)->get()->toArray();
            $data = array_map(fn($row) => (array) $row, $data);

            File::put(
                "{$exportPath}/{$name}.json",
                json_encode($data, JSON_PRETTY_PRINT)
            );

            $bar->advance();
            $this->line("  ✔ {$name} (" . count($data) . " rows)");
        }

        $bar->finish();
        $this->newLine();
        $this->info('Export complete → /exports/');
    }
}