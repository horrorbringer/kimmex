<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--no-compress : Skip gzip compression}';

    protected $description = 'Export the database to a SQL backup file with optional gzip compression';

    /**
     * Number of rows to fetch at a time during PHP-based export.
     */
    protected int $chunkSize = 1000;

    /**
     * Maximum number of backups to retain.
     */
    protected int $maxBackups = 5;

    public function handle(): int
    {
        $backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($backupDir);

        $timestamp = now()->format('Y-m-d_His');
        $filename = "kimmex_backup_{$timestamp}.sql";
        $filepath = "{$backupDir}/{$filename}";

        $this->info('Starting database backup...');

        // Attempt mysqldump first, fall back to PHP-based export
        $success = $this->tryMysqldump($filepath);

        if (! $success) {
            $this->warn('mysqldump not available. Using PHP-based export...');
            $success = $this->phpBasedExport($filepath);
        }

        if (! $success) {
            $this->error('Backup failed.');

            return self::FAILURE;
        }

        // Compress with gzip if available and not disabled
        $finalPath = $filepath;
        if (! $this->option('no-compress') && $this->canGzip()) {
            $this->info('Compressing backup with gzip...');
            $finalPath = $this->compressFile($filepath);
        }

        // Clean up old backups (keep only last 5)
        $this->cleanupOldBackups($backupDir);

        // Output results
        $size = File::size($finalPath);
        $humanSize = $this->humanFileSize($size);

        $this->newLine();
        $this->info('✅ Backup completed successfully!');
        $this->table(
            ['Property', 'Value'],
            [
                ['File', basename($finalPath)],
                ['Path', $finalPath],
                ['Size', $humanSize],
                ['Created', now()->format('Y-m-d H:i:s')],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * Attempt to use mysqldump for the backup.
     */
    protected function tryMysqldump(string $filepath): bool
    {
        $config = config('database.connections.'.config('database.default'));

        if (! in_array($config['driver'] ?? '', ['mysql', 'mariadb'])) {
            $this->warn("Driver '{$config['driver']}' is not MySQL/MariaDB. Skipping mysqldump.");

            return false;
        }

        // Check if mysqldump is available
        $checkCmd = PHP_OS_FAMILY === 'Windows' ? 'where mysqldump 2>NUL' : 'which mysqldump 2>/dev/null';
        exec($checkCmd, $output, $returnCode);

        if ($returnCode !== 0) {
            return false;
        }

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';
        $database = $config['database'] ?? '';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';

        $cmd = sprintf(
            'mysqldump --host=%s --port=%s --user=%s%s --single-transaction --routines --triggers %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? ' --password='.escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        exec($cmd, $dumpOutput, $exitCode);

        if ($exitCode !== 0) {
            $this->warn('mysqldump returned exit code '.$exitCode);
            // Clean up partial file
            if (File::exists($filepath)) {
                File::delete($filepath);
            }

            return false;
        }

        $this->info('Database exported via mysqldump.');

        return true;
    }

    /**
     * PHP-based database export using PDO.
     */
    protected function phpBasedExport(string $filepath): bool
    {
        try {
            $pdo = DB::connection()->getPdo();
            $driver = config('database.connections.'.config('database.default').'.driver');

            $handle = fopen($filepath, 'w');
            if (! $handle) {
                $this->error("Cannot open file for writing: {$filepath}");

                return false;
            }

            // Write header
            $database = config('database.connections.'.config('database.default').'.database');
            fwrite($handle, "-- Kimmex Database Backup\n");
            fwrite($handle, '-- Generated: '.now()->format('Y-m-d H:i:s')."\n");
            fwrite($handle, "-- Database: {$database}\n");
            fwrite($handle, "-- Method: PHP PDO Export\n");
            fwrite($handle, "-- -----------------------------------------------\n\n");

            if (in_array($driver, ['mysql', 'mariadb'])) {
                fwrite($handle, "SET NAMES utf8mb4;\n");
                fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n");
                fwrite($handle, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n");
            }

            // Get all tables
            $tables = $this->getTables($pdo, $driver);
            $this->info('Found '.count($tables).' tables to export.');

            $bar = $this->output->createProgressBar(count($tables));
            $bar->start();

            foreach ($tables as $table) {
                fwrite($handle, "-- -----------------------------------------------\n");
                fwrite($handle, "-- Table: {$table}\n");
                fwrite($handle, "-- -----------------------------------------------\n\n");

                // Export table structure
                $this->exportTableStructure($pdo, $driver, $table, $handle);

                // Export table data
                $this->exportTableData($pdo, $driver, $table, $handle);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            if (in_array($driver, ['mysql', 'mariadb'])) {
                fwrite($handle, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
            }

            fwrite($handle, "\n-- Backup completed.\n");
            fclose($handle);

            $this->info('Database exported via PHP-based method.');

            return true;
        } catch (\Throwable $e) {
            $this->error("PHP export failed: {$e->getMessage()}");
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            if (File::exists($filepath)) {
                File::delete($filepath);
            }

            return false;
        }
    }

    /**
     * Get list of all tables in the database.
     */
    protected function getTables(\PDO $pdo, string $driver): array
    {
        if (in_array($driver, ['mysql', 'mariadb'])) {
            $stmt = $pdo->query('SHOW TABLES');

            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }

        if ($driver === 'sqlite') {
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");

            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }

        if ($driver === 'pgsql') {
            $stmt = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");

            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }

        return [];
    }

    /**
     * Export CREATE TABLE statement.
     */
    protected function exportTableStructure(\PDO $pdo, string $driver, string $table, $handle): void
    {
        if (in_array($driver, ['mysql', 'mariadb'])) {
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $createSql = $row['Create Table'] ?? $row['Create View'] ?? '';
            fwrite($handle, $createSql.";\n\n");

            return;
        }

        if ($driver === 'sqlite') {
            fwrite($handle, "DROP TABLE IF EXISTS \"{$table}\";\n");
            $stmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name=".$pdo->quote($table));
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row && $row['sql']) {
                fwrite($handle, $row['sql'].";\n\n");
            }

            return;
        }

        if ($driver === 'pgsql') {
            // For PostgreSQL, use a simplified approach
            fwrite($handle, "-- Structure for table {$table} (PostgreSQL)\n");
            fwrite($handle, "DROP TABLE IF EXISTS \"{$table}\" CASCADE;\n");

            $stmt = $pdo->query('
                SELECT column_name, data_type, character_maximum_length, is_nullable, column_default
                FROM information_schema.columns
                WHERE table_name = '.$pdo->quote($table)."
                AND table_schema = 'public'
                ORDER BY ordinal_position
            ");
            $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (! empty($columns)) {
                $colDefs = [];
                foreach ($columns as $col) {
                    $def = "\"{$col['column_name']}\" {$col['data_type']}";
                    if ($col['character_maximum_length']) {
                        $def .= "({$col['character_maximum_length']})";
                    }
                    if ($col['is_nullable'] === 'NO') {
                        $def .= ' NOT NULL';
                    }
                    if ($col['column_default'] !== null) {
                        $def .= " DEFAULT {$col['column_default']}";
                    }
                    $colDefs[] = $def;
                }
                fwrite($handle, "CREATE TABLE \"{$table}\" (\n    ".implode(",\n    ", $colDefs)."\n);\n\n");
            }
        }
    }

    /**
     * Export table data as INSERT statements with chunking.
     */
    protected function exportTableData(\PDO $pdo, string $driver, string $table, $handle): void
    {
        $quote = in_array($driver, ['mysql', 'mariadb']) ? '`' : '"';
        $countStmt = $pdo->query("SELECT COUNT(*) FROM {$quote}{$table}{$quote}");
        $totalRows = (int) $countStmt->fetchColumn();

        if ($totalRows === 0) {
            fwrite($handle, "-- No data in table {$table}\n\n");

            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'])) {
            fwrite($handle, "LOCK TABLES `{$table}` WRITE;\n");
        }

        $offset = 0;
        while ($offset < $totalRows) {
            $stmt = $pdo->query("SELECT * FROM {$quote}{$table}{$quote} LIMIT {$this->chunkSize} OFFSET {$offset}");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                break;
            }

            $columns = array_keys($rows[0]);
            $quotedColumns = array_map(fn ($col) => "{$quote}{$col}{$quote}", $columns);
            $columnList = implode(', ', $quotedColumns);

            $valueSets = [];
            foreach ($rows as $row) {
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = $pdo->quote($value);
                    }
                }
                $valueSets[] = '('.implode(', ', $values).')';
            }

            fwrite($handle, "INSERT INTO {$quote}{$table}{$quote} ({$columnList}) VALUES\n");
            fwrite($handle, implode(",\n", $valueSets).";\n");

            $offset += $this->chunkSize;
        }

        if (in_array($driver, ['mysql', 'mariadb'])) {
            fwrite($handle, "UNLOCK TABLES;\n");
        }

        fwrite($handle, "\n");
    }

    /**
     * Check if gzip compression is available.
     */
    protected function canGzip(): bool
    {
        return function_exists('gzopen');
    }

    /**
     * Compress a file using gzip.
     */
    protected function compressFile(string $filepath): string
    {
        $gzPath = $filepath.'.gz';
        $fp = fopen($filepath, 'rb');
        $gz = gzopen($gzPath, 'wb9');

        while (! feof($fp)) {
            gzwrite($gz, fread($fp, 524288)); // 512KB chunks
        }

        fclose($fp);
        gzclose($gz);

        // Remove uncompressed file
        File::delete($filepath);

        return $gzPath;
    }

    /**
     * Remove old backups, keeping only the most recent ones.
     */
    protected function cleanupOldBackups(string $backupDir): void
    {
        $files = collect(File::glob("{$backupDir}/kimmex_backup_*"))
            ->sortByDesc(fn ($file) => File::lastModified($file))
            ->values();

        if ($files->count() <= $this->maxBackups) {
            return;
        }

        $toDelete = $files->slice($this->maxBackups);
        foreach ($toDelete as $file) {
            File::delete($file);
            $this->line('  Removed old backup: '.basename($file));
        }

        $this->info('Cleaned up '.$toDelete->count().' old backup(s).');
    }

    /**
     * Format file size in human-readable form.
     */
    protected function humanFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = (float) $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2).' '.$units[$i];
    }
}
