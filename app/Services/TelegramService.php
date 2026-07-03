<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramService
{
    protected ?string $token;
    protected bool $enabled;

    public function __construct()
    {
        $settings = SystemSetting::get('integration_settings', []);
        $this->token = $settings['telegram_bot_token'] ?? null;
        $this->enabled = (bool) ($settings['telegram_enabled'] ?? false);
    }

    /**
     * Send a formatted message to a specific Telegram Chat ID
     */
    public function sendMessage(string $chatId, string $message): bool
    {
        if (!$this->enabled || !$this->token || !$chatId) {
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
            
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram Notification Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a document (file) to a specific Telegram Chat ID
     */
    public function sendDocument(string $chatId, string $filePath, string $caption = ''): bool
    {
        if (!$this->enabled || !$this->token || !$chatId || !file_exists($filePath)) {
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$this->token}/sendDocument";
            
            $response = Http::attach(
                'document', 
                file_get_contents($filePath), 
                basename($filePath)
            )->post($url, [
                'chat_id' => $chatId,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram Document Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a document from any configured Laravel filesystem disk.
     */
    public function sendStoredDocument(string $chatId, string $disk, string $path, string $caption = ''): bool
    {
        if (!$this->enabled || !$this->token || !$chatId) {
            return false;
        }

        try {
            if (! Storage::disk($disk)->exists($path)) {
                return false;
            }
        } catch (\Throwable) {
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$this->token}/sendDocument";

            $response = Http::attach(
                'document',
                Storage::disk($disk)->get($path),
                basename($path)
            )->post($url, [
                'chat_id' => $chatId,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram Stored Document Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a notification for a new Job Application
     */
    public function notifyJobApplication(array $data): bool
    {
        $settings = SystemSetting::get('integration_settings', []);
        $chatId = $settings['telegram_jobs_chat_id'] ?? null;

        if (!$chatId) return false;

        $message = "💼 *NEW JOB APPLICATION*\n\n"
                 . "👤 *Name:* {$data['name']}\n"
                 . "📧 *Email:* {$data['email']}\n"
                 . "📱 *Phone:* " . ($data['phone'] ?? 'N/A') . "\n"
                 . "🎯 *Position:* {$data['position']}\n\n"
                 . "🔗 _Please check the admin panel for details._";

        if (!empty($data['file_disk']) && !empty($data['file_path'])) {
            return $this->sendStoredDocument($chatId, $data['file_disk'], $data['file_path'], $message);
        }

        if (!empty($data['file_path']) && file_exists($data['file_path'])) {
            return $this->sendDocument($chatId, $data['file_path'], $message);
        }

        return $this->sendMessage($chatId, $message);
    }

    /**
     * Send a notification for a new Contact Inquiry
     */
    public function notifyInquiry(array $data): bool
    {
        $settings = SystemSetting::get('integration_settings', []);
        $chatId = $settings['telegram_inquiries_chat_id'] ?? null;

        if (!$chatId) return false;

        $message = "📧 *NEW CONTACT INQUIRY*\n\n"
                 . "👤 *From:* {$data['name']}\n"
                 . "✉️ *Email:* {$data['email']}\n"
                 . "📝 *Subject:* " . ($data['subject'] ?? 'No Subject') . "\n\n"
                 . "💬 *Message:*\n_{$data['message']}_";

        if (!empty($data['file_disk']) && !empty($data['file_path'])) {
            return $this->sendStoredDocument($chatId, $data['file_disk'], $data['file_path'], $message);
        }

        if (!empty($data['file_path']) && file_exists($data['file_path'])) {
            return $this->sendDocument($chatId, $data['file_path'], $message);
        }

        return $this->sendMessage($chatId, $message);
    }
}
