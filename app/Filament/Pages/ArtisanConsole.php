<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

class ArtisanConsole extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.artisan-console';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-command-line';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Administration');
    }

    public static function getNavigationLabel(): string
    {
        return __('Artisan Console');
    }

    public static function getNavigationSort(): ?int
    {
        return 99;
    }

    /**
     * Only admins can access this page.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    // Form state
    public ?string $command = null;
    public string $password = '';
    public bool $unlocked = false;
    public ?string $output = null;
    public ?string $executedCommand = null;
    public ?string $executedAt = null;

    /**
     * Whitelisted commands that can be executed.
     */
    public static function allowedCommands(): array
    {
        return [
            // Cache & Optimization
            'optimize:clear' => '🧹 Clear all caches (config, route, view, event)',
            'config:cache' => '⚡ Cache configuration',
            'route:cache' => '⚡ Cache routes',
            'view:cache' => '⚡ Cache Blade views',
            'view:clear' => '🧹 Clear compiled views',
            'cache:clear' => '🧹 Clear application cache',
            'event:clear' => '🧹 Clear cached events',

            // Filament
            'filament:assets' => '🎨 Publish Filament assets',
            'filament:cache-components' => '⚡ Cache Filament components',
            'filament:optimize-clear' => '🧹 Clear Filament cache',

            // Database
            'migrate' => '🗃️ Run database migrations',
            'migrate:status' => '📋 Show migration status',
            'db:seed' => '🌱 Run database seeders',

            // Storage & Links
            'storage:link' => '🔗 Create storage symlink',

            // Sitemap
            'sitemap:generate' => '🗺️ Regenerate sitemap',

            // Cleanup
            'analytics:prune --days=90' => '🧹 Prune page views older than 90 days',
            'log:clear' => '🧹 Clear Laravel log file',

            // Queue
            'queue:work --once' => '📨 Process next queue job',
            'queue:retry all' => '🔄 Retry all failed jobs',
            'queue:flush' => '🗑️ Delete all failed jobs',

            // Maintenance
            'down' => '🚧 Put app in maintenance mode',
            'up' => '✅ Bring app out of maintenance mode',

            // Info
            'about' => 'ℹ️ Show application info',
            'route:list --columns=method,uri,name' => '📋 List all routes',
        ];
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([]);
    }

    /**
     * Verify admin password to unlock the console.
     */
    public function unlock(): void
    {
        if (empty($this->password)) {
            Notification::make()->danger()->title(__('Password is required.'))->send();
            return;
        }

        $user = auth()->user();

        if (!Hash::check($this->password, $user->password)) {
            Notification::make()->danger()->title(__('Incorrect password.'))->send();

            Log::warning('Artisan Console: failed unlock attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip' => request()->ip(),
            ]);

            $this->password = '';
            return;
        }

        $this->unlocked = true;
        $this->password = '';

        Notification::make()->success()->title(__('Console unlocked.'))->send();

        Log::info('Artisan Console: unlocked', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * Lock the console (re-lock).
     */
    public function lock(): void
    {
        $this->unlocked = false;
        $this->output = null;
        $this->executedCommand = null;
        $this->command = null;
    }

    /**
     * Execute the selected artisan command.
     */
    public function execute(): void
    {
        if (!$this->unlocked) {
            Notification::make()->danger()->title(__('Console is locked. Enter your password first.'))->send();
            return;
        }

        if (!$this->command) {
            Notification::make()->warning()->title(__('Please select a command.'))->send();
            return;
        }

        // Verify command is in whitelist
        if (!array_key_exists($this->command, static::allowedCommands())) {
            Notification::make()->danger()->title(__('Command not allowed.'))->send();
            return;
        }

        $user = auth()->user();

        Log::info('Artisan Console: executing command', [
            'command' => $this->command,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip' => request()->ip(),
        ]);

        try {
            // Parse command and arguments
            $parts = str_getcsv($this->command, ' ');
            $artisanCommand = $parts[0];
            $params = [];

            foreach (array_slice($parts, 1) as $part) {
                if (str_starts_with($part, '--')) {
                    $param = ltrim($part, '-');
                    if (str_contains($param, '=')) {
                        [$key, $value] = explode('=', $param, 2);
                        $params["--{$key}"] = $value;
                    } else {
                        $params["--{$param}"] = true;
                    }
                } else {
                    $params[] = $part;
                }
            }

            // Force flag for migrations/seeders
            if (in_array($artisanCommand, ['migrate', 'db:seed'])) {
                $params['--force'] = true;
            }

            $exitCode = Artisan::call($artisanCommand, $params);
            $this->output = trim(Artisan::output());
            $this->executedCommand = $this->command;
            $this->executedAt = now()->format('H:i:s');

            if ($exitCode === 0) {
                Notification::make()->success()->title(__('Command executed successfully.'))->send();
            } else {
                Notification::make()->warning()->title(__('Command finished with exit code: :code', ['code' => $exitCode]))->send();
            }

            Log::info('Artisan Console: command completed', [
                'command' => $this->command,
                'exit_code' => $exitCode,
                'user_id' => $user->id,
            ]);
        } catch (\Throwable $e) {
            $this->output = "ERROR: {$e->getMessage()}";

            Notification::make()->danger()->title(__('Command failed.'))->body($e->getMessage())->send();

            Log::error('Artisan Console: command failed', [
                'command' => $this->command,
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
        }
    }

    #[Computed]
    public function recentLogs(): array
    {
        // Get recent artisan console activity from the log
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return [];
        }

        $lines = array_slice(file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), -200);
        $entries = [];

        foreach ($lines as $line) {
            if (str_contains($line, 'Artisan Console:')) {
                $entries[] = $line;
            }
        }

        return array_slice(array_reverse($entries), 0, 10);
    }
}
