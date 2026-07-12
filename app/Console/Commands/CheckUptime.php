<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckUptime extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'uptime:check';

    /**
     * The console command description.
     */
    protected $description = 'Check the /health endpoint and send Telegram alerts on status changes';

    private const CACHE_KEY = 'uptime:last_status';

    public function handle(): int
    {
        $siteUrl = config('app.url');
        $healthUrl = rtrim($siteUrl, '/') . '/health';

        try {
            $response = Http::timeout(15)->get($healthUrl);
            $statusCode = $response->status();
            $body = $response->json();

            $currentStatus = ($statusCode === 200 && ($body['status'] ?? null) === 'healthy')
                ? 'healthy'
                : 'unhealthy';

            $checks = $body['checks'] ?? [];
            $reportedStatus = $body['status'] ?? 'unknown';
        } catch (\Throwable $e) {
            $currentStatus = 'unhealthy';
            $statusCode = 0;
            $reportedStatus = 'unreachable';
            $checks = [];

            Log::error('Uptime check failed to reach health endpoint', [
                'error' => $e->getMessage(),
            ]);
        }

        $previousStatus = Cache::get(self::CACHE_KEY, 'healthy');

        // Transition: healthy → unhealthy (send DOWN alert)
        if ($currentStatus === 'unhealthy' && $previousStatus === 'healthy') {
            $this->sendDownAlert($siteUrl, $statusCode, $reportedStatus, $checks);
            $this->warn("Site is DOWN — alert sent.");
        }

        // Transition: unhealthy → healthy (send RECOVERED alert)
        if ($currentStatus === 'healthy' && $previousStatus === 'unhealthy') {
            $this->sendRecoveredAlert($siteUrl);
            $this->info("Site has RECOVERED — notification sent.");
        }

        if ($currentStatus === 'healthy' && $previousStatus === 'healthy') {
            $this->info("Site is healthy.");
        }

        if ($currentStatus === 'unhealthy' && $previousStatus === 'unhealthy') {
            $this->warn("Site is still down — no duplicate alert sent.");
        }

        // Store current status for next run
        Cache::put(self::CACHE_KEY, $currentStatus);

        return self::SUCCESS;
    }

    /**
     * Send a Telegram alert that the site is down.
     */
    private function sendDownAlert(string $siteUrl, int $statusCode, string $reportedStatus, array $checks): void
    {
        $failingChecks = collect($checks)
            ->where('status', 'fail')
            ->pluck('name')
            ->implode(', ');

        $message = "🚨 *SITE DOWN ALERT*\n\n"
            . "🌐 *Site:* {$siteUrl}\n"
            . "📊 *HTTP Status:* {$statusCode}\n"
            . "🔴 *Health Status:* {$reportedStatus}\n"
            . "❌ *Failing Checks:* " . ($failingChecks ?: 'N/A') . "\n"
            . "🕐 *Timestamp:* " . now()->toDateTimeString() . "\n\n"
            . "_Alerts are suppressed until the site recovers._";

        $this->sendTelegramAlert($message);

        Log::warning('Uptime check: site is DOWN', [
            'site' => $siteUrl,
            'http_status' => $statusCode,
            'health_status' => $reportedStatus,
            'failing_checks' => $failingChecks,
        ]);
    }

    /**
     * Send a Telegram alert that the site has recovered.
     */
    private function sendRecoveredAlert(string $siteUrl): void
    {
        $message = "✅ *SITE RECOVERED*\n\n"
            . "🌐 *Site:* {$siteUrl}\n"
            . "🟢 *Health Status:* healthy\n"
            . "🕐 *Recovered At:* " . now()->toDateTimeString() . "\n\n"
            . "_All checks are passing again._";

        $this->sendTelegramAlert($message);

        Log::info('Uptime check: site has RECOVERED', [
            'site' => $siteUrl,
        ]);
    }

    /**
     * Send a message via TelegramService to the alerts chat.
     */
    private function sendTelegramAlert(string $message): void
    {
        $settings = SystemSetting::get('integration_settings', []);
        $chatId = $settings['telegram_alerts_chat_id']
            ?? $settings['telegram_inquiries_chat_id']
            ?? null;

        if (!$chatId) {
            $this->error('No Telegram chat ID configured for uptime alerts.');
            Log::warning('Uptime check: no Telegram chat ID configured for alerts.');
            return;
        }

        $telegram = app(TelegramService::class);
        $sent = $telegram->sendMessage($chatId, $message);

        if (!$sent) {
            $this->error('Failed to send Telegram alert.');
        }
    }
}
