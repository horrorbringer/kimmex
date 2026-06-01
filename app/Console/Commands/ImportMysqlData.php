<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportMysqlData extends Command
{
    protected $signature = 'db:import-mysql';
    protected $description = 'Import all JSON exports into MySQL';

    public function handle(): void
    {
        $exportPath = base_path('exports');
        $files = File::glob("{$exportPath}/*.json");

        if (empty($files)) {
            $this->error('No export files found in /exports/');
            return;
        }

        $this->info('Found ' . count($files) . ' files to import');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            $tableName = pathinfo($file, PATHINFO_FILENAME);
            $rows = json_decode(File::get($file), true);

            if (empty($rows)) {
                $this->line("  ⚠ {$tableName} (empty, skipped)");
                $bar->advance();
                continue;
            }

            // Truncate before import to avoid duplicates
            DB::table($tableName)->truncate();

            // Insert in chunks of 500
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table($tableName)->insert($chunk);
            }

            $bar->advance();
            $this->line("  ✔ {$tableName} (" . count($rows) . " rows)");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $bar->finish();
        $this->newLine();
        $this->info('Import complete ✅');
    }
}