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
    public array $selectedBulk = [];
    public string $password = '';
    public string $totpCode = '';
    public bool $unlocked = false;
    public ?string $output = null;
    public ?string $executedCommand = null;
    public ?string $executedAt = null;

    /**
     * Bulk command presets — run multiple commands in sequence.
     */
    public static function bulkPresets(): array
    {
        return [
            'deploy' => [
                'label' => '🚀 Deploy (clear + migrate + cache)',
                'commands' => ['config:clear', 'route:clear', 'view:clear', 'migrate', 'config:cache', 'view:cache', 'filament:assets'],
            ],
            'clear_all' => [
                'label' => '🧹 Clear Everything',
                'commands' => ['config:clear', 'route:clear', 'view:clear', 'event:clear', 'filament:clear-cached-components'],
            ],
            'optimize' => [
                'label' => '⚡ Optimize (cache all)',
                'commands' => ['config:cache', 'route:cache', 'view:cache', 'filament:cache-components'],
            ],
            'maintenance' => [
                'label' => '🔧 Maintenance (backup + prune + sitemap)',
                'commands' => ['backup:database', 'analytics:prune --days=90', 'sitemap:generate'],
            ],
        ];
    }

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
            'filament:clear-cached-components' => '🧹 Clear Filament cache',

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
            'queue:work --stop-when-empty' => '📨 Process all pending jobs',
            'queue:retry all' => '🔄 Retry all failed jobs',
            'queue:flush' => '🗑️ Delete all failed jobs',
            'queue:clear' => '🗑️ Clear all pending jobs',

            // Maintenance
            'down' => '🚧 Put app in maintenance mode',
            'up' => '✅ Bring app out of maintenance mode',

            // Weekly Digest
            'digest:send' => '📬 Send weekly digest email',

            // Info
            'about' => 'ℹ️ Show application info',
            'route:list --columns=method,uri,name' => '📋 List all routes',

            // Backup
            'backup:database' => '💾 Backup database to SQL file',

            // Uptime
            'uptime:check' => '🏓 Run uptime health check',
        ];
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([]);
    }

    /**
     * Verify admin password + 2FA code to unlock the console.
     */
    public function unlock(): void
    {
        if (empty($this->password)) {
            Notification::make()->danger()->title(__('Password is required.'))->send();
            return;
        }

        $user = auth()->user();

        // Step 1: Verify password
        if (!Hash::check($this->password, $user->password)) {
            Notification::make()->danger()->title(__('Incorrect password.'))->send();

            Log::warning('Artisan Console: failed unlock attempt (wrong password)', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip' => request()->ip(),
            ]);

            $this->password = '';
            $this->totpCode = '';
            return;
        }

        // Step 2: Verify 2FA TOTP code (if MFA is set up)
        $secret = $user->getAppAuthenticationSecret();

        if ($secret) {
            if (empty($this->totpCode)) {
                Notification::make()->danger()->title(__('2FA code is required.'))->send();
                $this->totpCode = '';
                return;
            }

            $appAuth = \Filament\Auth\MultiFactor\App\AppAuthentication::make();
            if (!$appAuth->verifyCode($this->totpCode, $secret)) {
                Notification::make()->danger()->title(__('Invalid 2FA code.'))->send();

                Log::warning('Artisan Console: failed unlock attempt (wrong 2FA)', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'ip' => request()->ip(),
                ]);

                $this->totpCode = '';
                return;
            }
        }

        $this->unlocked = true;
        $this->password = '';
        $this->totpCode = '';

        Notification::make()->success()->title(__('Console unlocked.'))->send();

        Log::info('Artisan Console: unlocked (password + 2FA verified)', [
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

            // Auto-reload page after cache-clearing commands to avoid stale Livewire state
            if (in_array($artisanCommand, ['view:clear', 'optimize:clear', 'config:clear', 'cache:clear', 'filament:clear-cached-components'])) {
                $this->js('setTimeout(() => window.location.reload(), 800)');
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

    /**
     * Execute a bulk preset (multiple commands in sequence).
     */
    public function executeBulk(string $presetKey): void
    {
        if (!$this->unlocked) {
            Notification::make()->danger()->title(__('Console is locked.'))->send();
            return;
        }

        $presets = static::bulkPresets();
        if (!isset($presets[$presetKey])) {
            Notification::make()->danger()->title(__('Invalid preset.'))->send();
            return;
        }

        $preset = $presets[$presetKey];
        $user = auth()->user();
        $results = [];
        $failed = 0;

        Log::info('Artisan Console: executing bulk preset', [
            'preset' => $presetKey,
            'commands' => $preset['commands'],
            'user_id' => $user->id,
            'ip' => request()->ip(),
        ]);

        foreach ($preset['commands'] as $cmd) {
            try {
                $parts = str_getcsv($cmd, ' ');
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

                if (in_array($artisanCommand, ['migrate', 'db:seed'])) {
                    $params['--force'] = true;
                }

                $exitCode = Artisan::call($artisanCommand, $params);
                $output = trim(Artisan::output());
                $status = $exitCode === 0 ? '✅' : '⚠️';
                $results[] = "{$status} {$cmd}" . ($output ? "\n   {$output}" : '');

                if ($exitCode !== 0) {
                    $failed++;
                }
            } catch (\Throwable $e) {
                $results[] = "❌ {$cmd}\n   ERROR: {$e->getMessage()}";
                $failed++;
            }
        }

        $this->output = implode("\n\n", $results);
        $this->executedCommand = $preset['label'];
        $this->executedAt = now()->format('H:i:s');

        if ($failed === 0) {
            Notification::make()->success()
                ->title(__('Bulk preset completed'))
                ->body(__(':count commands executed successfully.', ['count' => count($preset['commands'])]))
                ->send();
        } else {
            Notification::make()->warning()
                ->title(__('Bulk preset completed with errors'))
                ->body(__(':failed of :total commands failed.', ['failed' => $failed, 'total' => count($preset['commands'])]))
                ->send();
        }

        // Auto-reload page after bulk presets (they typically clear caches)
        $this->js('setTimeout(() => window.location.reload(), 800)');

        Log::info('Artisan Console: bulk preset completed', [
            'preset' => $presetKey,
            'failed' => $failed,
            'user_id' => $user->id,
        ]);
    }

    #[Computed]
    public function latestBackupFile(): ?string
    {
        $backupDir = storage_path('app/backups');

        if (!is_dir($backupDir)) {
            return null;
        }

        $files = glob("{$backupDir}/kimmex_backup_*");
        if (empty($files)) {
            return null;
        }

        // Sort by modification time descending
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        return basename($files[0]);
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
