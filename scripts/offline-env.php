<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);
$storagePath = $basePath . '/storage';
$dbPath = $storagePath . '/offline-testing.sqlite';

$overrides = [
    'APP_ENV' => 'local',
    'APP_DEBUG' => 'true',
    'APP_INSTALLED' => 'true',
    'APP_SHOW_SAMPLE_ACCOUNTS' => 'true',
    'APP_SESSION_SECURE' => 'false',
    'DB_CONNECTION' => 'sqlite',
    'DB_SQLITE_PATH' => $dbPath,
    'HTTP_HOST' => '127.0.0.1',
    'SERVER_NAME' => '127.0.0.1',
];

foreach ($overrides as $key => $value) {
    if (function_exists('putenv')) {
        putenv($key . '=' . $value);
    }

    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

