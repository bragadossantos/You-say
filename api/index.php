<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

// Suppress raw deprecation notices from leaking into HTTP output
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Setup temporary directories for Vercel's read-only serverless filesystem
$directories = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Direct storage paths to writable /tmp
putenv('LARAVEL_STORAGE_PATH=/tmp/storage');
$_ENV['LARAVEL_STORAGE_PATH'] = '/tmp/storage';
$_SERVER['LARAVEL_STORAGE_PATH'] = '/tmp/storage';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Fallback APP_KEY if not configured in Vercel UI
if (!getenv('APP_KEY')) {
    $fallbackKey = 'base64:YQIVvJZdc+Q5iRdwCrp2D4XseJ+raTIPb1KJJpZS/no=';
    putenv("APP_KEY={$fallbackKey}");
    $_ENV['APP_KEY'] = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
}

// Ensure HTTPS detection behind Vercel edge proxy
if ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')) {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';
$_SERVER['SESSION_DRIVER'] = 'cookie';

putenv('SESSION_SECURE_COOKIE=true');
$_ENV['SESSION_SECURE_COOKIE'] = 'true';
$_SERVER['SESSION_SECURE_COOKIE'] = 'true';

// Prevent SCRIPT_NAME from prepending /api to base routes
if (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] === '/api/index.php') {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
}

// Handle SQLite database in /tmp for serverless runtime if no external cloud DB is configured
$connection = getenv('DB_CONNECTION') ?: 'sqlite';
if ($connection === 'sqlite') {
    $srcDb = __DIR__ . '/../database/database.sqlite';
    $dstDb = '/tmp/database.sqlite';
    $needsMigration = false;

    if (!file_exists($dstDb) || filesize($dstDb) === 0) {
        if (file_exists($srcDb) && filesize($srcDb) > 0) {
            copy($srcDb, $dstDb);
        } else {
            touch($dstDb);
            $needsMigration = true;
        }
    }
    putenv("DB_DATABASE={$dstDb}");
    $_ENV['DB_DATABASE'] = $dstDb;
    $_SERVER['DB_DATABASE'] = $dstDb;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Automatically run migrations and seeders if starting from scratch without pre-seeded db
if ($connection === 'sqlite' && !empty($needsMigration)) {
    try {
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->call('migrate', ['--force' => true, '--seed' => true]);
    } catch (\Throwable $e) {
        error_log('Initial SQLite migration notice: ' . $e->getMessage());
    }
}

$app->handleRequest(Request::capture());
