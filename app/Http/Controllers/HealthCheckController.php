<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // Token protection
        $configuredToken = config('app.health_check_token');

        if ($configuredToken && $request->query('token') !== $configuredToken) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'Invalid or missing health check token.',
            ], 401);
        }

        $checks = $this->runChecks();

        $failedCount = collect($checks)->where('status', 'fail')->count();
        $totalCount = count($checks);

        if ($failedCount === 0) {
            $status = 'healthy';
        } elseif ($failedCount <= 2) {
            $status = 'degraded';
        } else {
            $status = 'unhealthy';
        }

        $httpStatus = $status === 'unhealthy' ? 503 : 200;

        return response()->json([
            'status' => $status,
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $httpStatus);
    }

    /**
     * Run all health checks and return results array.
     */
    public static function runChecks(): array
    {
        return [
            self::checkDatabase(),
            self::checkDiskSpace(),
            self::checkStorageWritable(),
            self::checkCache(),
            self::checkMailConfig(),
            self::checkQueueConnection(),
            self::checkEnvironment(),
            self::checkPhpVersion(),
            self::checkLaravelVersion(),
        ];
    }

    private static function checkDatabase(): array
    {
        try {
            DB::select('SELECT 1');

            return [
                'name' => 'Database',
                'status' => 'pass',
                'message' => 'Database connection is working.',
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'Database',
                'status' => 'fail',
                'message' => 'Database connection failed: '.$e->getMessage(),
            ];
        }
    }

    private static function checkDiskSpace(): array
    {
        $freeBytes = disk_free_space(base_path());
        $freeGb = round($freeBytes / 1024 / 1024 / 1024, 2);

        if ($freeGb < 1) {
            return [
                'name' => 'Disk Space',
                'status' => 'fail',
                'message' => "Low disk space: {$freeGb} GB free.",
            ];
        }

        return [
            'name' => 'Disk Space',
            'status' => 'pass',
            'message' => "{$freeGb} GB free disk space available.",
        ];
    }

    private static function checkStorageWritable(): array
    {
        try {
            $testFile = 'health_check_'.uniqid().'.tmp';
            Storage::disk('local')->put($testFile, 'health_check_test');
            $content = Storage::disk('local')->get($testFile);
            Storage::disk('local')->delete($testFile);

            if ($content === 'health_check_test') {
                return [
                    'name' => 'Storage Writable',
                    'status' => 'pass',
                    'message' => 'Storage is writable and readable.',
                ];
            }

            return [
                'name' => 'Storage Writable',
                'status' => 'fail',
                'message' => 'Storage read/write mismatch.',
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'Storage Writable',
                'status' => 'fail',
                'message' => 'Storage test failed: '.$e->getMessage(),
            ];
        }
    }

    private static function checkCache(): array
    {
        try {
            $key = 'health_check_'.uniqid();
            Cache::put($key, 'ok', 10);
            $value = Cache::get($key);
            Cache::forget($key);

            if ($value === 'ok') {
                return [
                    'name' => 'Cache',
                    'status' => 'pass',
                    'message' => 'Cache put/get is working (driver: '.config('cache.default').').',
                ];
            }

            return [
                'name' => 'Cache',
                'status' => 'fail',
                'message' => 'Cache read returned unexpected value.',
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'Cache',
                'status' => 'fail',
                'message' => 'Cache test failed: '.$e->getMessage(),
            ];
        }
    }

    private static function checkMailConfig(): array
    {
        $mailer = config('mail.default');

        if (empty($mailer) || $mailer === 'log') {
            return [
                'name' => 'Mail Config',
                'status' => 'fail',
                'message' => "Mail mailer is '{$mailer}' — not configured for production.",
            ];
        }

        return [
            'name' => 'Mail Config',
            'status' => 'pass',
            'message' => "Mail mailer configured: {$mailer}.",
        ];
    }

    private static function checkQueueConnection(): array
    {
        $connection = config('queue.default');

        return [
            'name' => 'Queue Connection',
            'status' => 'pass',
            'message' => "Queue connection: {$connection}.",
        ];
    }

    private static function checkEnvironment(): array
    {
        $env = config('app.env');
        $debug = config('app.debug');

        $isProduction = $env === 'production';
        $debugInProduction = $isProduction && $debug;

        if ($debugInProduction) {
            return [
                'name' => 'Environment',
                'status' => 'fail',
                'message' => "Environment: {$env}, Debug: ON — debug should be disabled in production.",
            ];
        }

        return [
            'name' => 'Environment',
            'status' => 'pass',
            'message' => "Environment: {$env}, Debug: ".($debug ? 'ON' : 'OFF').'.',
        ];
    }

    private static function checkPhpVersion(): array
    {
        $version = PHP_VERSION;
        $minVersion = '8.2.0';

        if (version_compare($version, $minVersion, '<')) {
            return [
                'name' => 'PHP Version',
                'status' => 'fail',
                'message' => "PHP {$version} is below minimum required {$minVersion}.",
            ];
        }

        return [
            'name' => 'PHP Version',
            'status' => 'pass',
            'message' => "PHP {$version}.",
        ];
    }

    private static function checkLaravelVersion(): array
    {
        $version = app()->version();

        return [
            'name' => 'Laravel Version',
            'status' => 'pass',
            'message' => "Laravel {$version}.",
        ];
    }
}
