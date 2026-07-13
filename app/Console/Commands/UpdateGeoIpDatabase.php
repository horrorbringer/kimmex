<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UpdateGeoIpDatabase extends Command
{
    protected $signature = 'geoip:update';
    protected $description = 'Download/update the MaxMind GeoLite2-Country database';

    /**
     * GeoLite2 free download URL (no license key needed for Country DB via this mirror).
     */
    protected string $downloadUrl = 'https://git.io/GeoLite2-Country.mmdb';

    /**
     * Alternative: use MaxMind's official URL with license key.
     * Set MAXMIND_LICENSE_KEY in .env to use it.
     */
    public function handle(): int
    {
        $targetPath = storage_path('app/geoip/GeoLite2-Country.mmdb');
        $licenseKey = config('services.maxmind.license_key');

        if ($licenseKey) {
            $url = "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key={$licenseKey}&suffix=tar.gz";
            $this->info('Downloading from MaxMind (with license key)...');
        } else {
            // Use GitHub mirror (maintained by P3TERX)
            $url = 'https://raw.githubusercontent.com/P3TERX/GeoLite.mmdb/download/GeoLite2-Country.mmdb';
            $this->info('Downloading GeoLite2-Country.mmdb from GitHub mirror...');
        }

        try {
            $tempFile = storage_path('app/geoip/GeoLite2-Country.mmdb.tmp');

            // Download with progress
            $ch = curl_init($url);
            $fp = fopen($tempFile, 'w');

            curl_setopt_array($ch, [
                CURLOPT_FILE => $fp,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_USERAGENT => 'Kimmex-GeoIP-Updater/1.0',
            ]);

            $success = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $fileSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
            curl_close($ch);
            fclose($fp);

            if (!$success || $httpCode !== 200 || $fileSize < 1000000) {
                @unlink($tempFile);
                $this->error("Download failed (HTTP {$httpCode}, size: {$fileSize} bytes)");
                return self::FAILURE;
            }

            // Validate it's a valid MMDB file (starts with specific bytes)
            $header = file_get_contents($tempFile, false, null, 0, 4);
            if (strlen($header) < 4) {
                @unlink($tempFile);
                $this->error('Downloaded file is invalid.');
                return self::FAILURE;
            }

            // Move temp file to final location
            if (file_exists($targetPath)) {
                @unlink($targetPath);
            }
            rename($tempFile, $targetPath);

            $sizeMb = round(filesize($targetPath) / 1024 / 1024, 1);
            $this->info("✅ GeoLite2-Country.mmdb updated ({$sizeMb} MB)");
            $this->info("   Location: {$targetPath}");

            // Clear geo-IP cache so new lookups use the fresh database
            \Illuminate\Support\Facades\Cache::flush();
            $this->info('   Cache cleared.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            @unlink($tempFile ?? '');
            $this->error("Failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
