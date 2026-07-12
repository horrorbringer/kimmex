<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

class LogViewer extends Page
{
    protected string $view = 'filament.pages.log-viewer';

    public string $search = '';
    public string $levelFilter = 'all';
    public int $page = 1;
    public int $perPage = 25;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-bug-ant';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Administration');
    }

    public static function getNavigationLabel(): string
    {
        return __('Log Viewer');
    }

    public static function getNavigationSort(): ?int
    {
        return 98;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([]);
    }

    protected function getLogPath(): string
    {
        return storage_path('logs/laravel.log');
    }

    /**
     * Read the last N lines of a file efficiently without loading entire file.
     */
    protected function readLastLines(string $filePath, int $maxLines = 5000): array
    {
        if (!file_exists($filePath) || filesize($filePath) === 0) {
            return [];
        }

        $file = new \SplFileObject($filePath, 'r');
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();

        if ($totalLines === 0) {
            return [];
        }

        $startLine = max(0, $totalLines - $maxLines);
        $lines = [];

        $file->seek($startLine);
        while (!$file->eof()) {
            $line = $file->current();
            if ($line !== false && trim($line) !== '') {
                $lines[] = rtrim($line, "\r\n");
            }
            $file->next();
        }

        return $lines;
    }

    /**
     * Parse raw log lines into structured entries.
     */
    protected function parseLogEntries(array $lines): array
    {
        $entries = [];
        $currentEntry = null;
        $pattern = '/^\[(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})\]\s+\w+\.(\w+):\s(.*)$/';

        foreach ($lines as $line) {
            if (preg_match($pattern, $line, $matches)) {
                // Save previous entry
                if ($currentEntry !== null) {
                    $entries[] = $currentEntry;
                }

                $currentEntry = [
                    'timestamp' => $matches[1],
                    'level' => strtoupper($matches[2]),
                    'message' => $matches[3],
                    'stackTrace' => '',
                ];
            } elseif ($currentEntry !== null) {
                // Append to stack trace
                $currentEntry['stackTrace'] .= ($currentEntry['stackTrace'] ? "\n" : '') . $line;
            }
        }

        // Don't forget the last entry
        if ($currentEntry !== null) {
            $entries[] = $currentEntry;
        }

        return $entries;
    }

    #[Computed]
    public function logEntries(): array
    {
        $logPath = $this->getLogPath();

        if (!file_exists($logPath)) {
            return [];
        }

        $lines = $this->readLastLines($logPath, 5000);
        $entries = $this->parseLogEntries($lines);

        // Reverse so most recent is first
        $entries = array_reverse($entries);

        // Filter by level
        if ($this->levelFilter !== 'all') {
            $entries = array_filter($entries, function ($entry) {
                return strtolower($entry['level']) === strtolower($this->levelFilter);
            });
            $entries = array_values($entries);
        }

        // Filter by search term
        if (!empty($this->search)) {
            $searchLower = strtolower($this->search);
            $entries = array_filter($entries, function ($entry) use ($searchLower) {
                return str_contains(strtolower($entry['message']), $searchLower)
                    || str_contains(strtolower($entry['stackTrace']), $searchLower)
                    || str_contains(strtolower($entry['timestamp']), $searchLower);
            });
            $entries = array_values($entries);
        }

        return $entries;
    }

    #[Computed]
    public function totalEntries(): int
    {
        return count($this->logEntries);
    }

    #[Computed]
    public function totalPages(): int
    {
        return max(1, (int) ceil($this->totalEntries / $this->perPage));
    }

    #[Computed]
    public function paginatedEntries(): array
    {
        $offset = ($this->page - 1) * $this->perPage;

        return array_slice($this->logEntries, $offset, $this->perPage);
    }

    #[Computed]
    public function logFileSize(): string
    {
        $logPath = $this->getLogPath();

        if (!file_exists($logPath)) {
            return '0 B';
        }

        $bytes = filesize($logPath);

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function updatedLevelFilter(): void
    {
        $this->page = 1;
    }

    public function setLevel(string $level): void
    {
        $this->levelFilter = $level;
        $this->page = 1;
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function nextPage(): void
    {
        if ($this->page < $this->totalPages) {
            $this->page++;
        }
    }

    public function refresh(): void
    {
        unset($this->logEntries, $this->totalEntries, $this->totalPages, $this->paginatedEntries, $this->logFileSize);

        Notification::make()
            ->success()
            ->title(__('Log refreshed'))
            ->send();
    }

    public function clearLog(): void
    {
        $logPath = $this->getLogPath();

        if (!file_exists($logPath)) {
            Notification::make()
                ->warning()
                ->title(__('Log file does not exist'))
                ->send();
            return;
        }

        $user = auth()->user();

        // Log who cleared it before truncating
        Log::info('Log file cleared by admin', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip' => request()->ip(),
            'file_size_before' => filesize($logPath),
        ]);

        // Truncate the file
        file_put_contents($logPath, '');

        unset($this->logEntries, $this->totalEntries, $this->totalPages, $this->paginatedEntries, $this->logFileSize);
        $this->page = 1;

        Notification::make()
            ->success()
            ->title(__('Log file cleared'))
            ->send();
    }

    public function downloadLog()
    {
        $logPath = $this->getLogPath();

        if (!file_exists($logPath)) {
            Notification::make()
                ->warning()
                ->title(__('Log file does not exist'))
                ->send();
            return null;
        }

        return response()->download($logPath, 'laravel-' . now()->format('Y-m-d_H-i-s') . '.log');
    }
}
