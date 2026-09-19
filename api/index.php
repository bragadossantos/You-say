<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

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

// Prevent SCRIPT_NAME from prepending /api to base routes
if (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] === '/api/index.php') {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
}

// Handle SQLite database in /tmp for serverless runtime if no external cloud DB is configured
$connection = getenv('DB_CONNECTION') ?: 'sqlite';
if ($connection === 'sqlite') {
    $dbPath = '/tmp/database.sqlite';
    $firstRun = !file_exists($dbPath);
    if ($firstRun) {
        touch($dbPath);
    }
    putenv("DB_DATABASE={$dbPath}");
    $_ENV['DB_DATABASE'] = $dbPath;
    $_SERVER['DB_DATABASE'] = $dbPath;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Automatically run migrations and seeders on fresh SQLite in /tmp
if ($connection === 'sqlite' && !empty($firstRun)) {
    try {
        $kernel = $app->make(Kernel::class);
        $kernel->call('migrate', ['--force' => true, '--seed' => true]);
    } catch (\Throwable $e) {
        error_log('Initial SQLite migration notice: ' . $e->getMessage());
    }
}

$app->handleRequest(Request::capture());
