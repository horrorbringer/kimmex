<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function sendNotification(string $message): bool
    {
        $settings = SystemSetting::get('integration_settings', []);

        if (!($settings['telegram_enabled'] ?? false)) {
            return false;
        }

        $botToken = $settings['telegram_bot_token'] ?? null;
        $chatId = $settings['telegram_chat_id'] ?? null;

        if (!$botToken || !$chatId) {
            Log::warning('Telegram bot token or chat ID not configured.');
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram notification failed: ' . $e->getMessage());
            return false;
        }
    }
}
