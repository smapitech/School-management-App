<?php

declare(strict_types=1);

$environment = function_exists('app_environment') ? app_environment() : 'local';
$defaultDriver = $environment === 'production' ? 'mysql' : 'sqlite';
$getEnv = static function (string $key, mixed $default = null): mixed {
    if (function_exists('env')) {
        return env($key, $default);
    }

    return $default;
};

return [
    /*
     * Use "sqlite" for local testing.
     * Use "mysql" on aaPanel/cPanel after importing database/schema_mysql.sql.
     */
    'driver' => strtolower((string) $getEnv('DB_CONNECTION', $defaultDriver)),

    'sqlite' => [
        'path' => (string) $getEnv('DB_SQLITE_PATH', dirname(__DIR__) . '/storage/database.sqlite'),
    ],

    'mysql' => [
        'host' => (string) $getEnv('DB_HOST', '127.0.0.1'),
        'port' => (int) $getEnv('DB_PORT', 3306),
        'database' => (string) $getEnv('DB_DATABASE', 'cpanel_db_name'),
        'username' => (string) $getEnv('DB_USERNAME', 'cpanel_db_user'),
        'password' => (string) $getEnv('DB_PASSWORD', 'cpanel_db_password'),
        'charset' => (string) $getEnv('DB_CHARSET', 'utf8mb4'),
        'socket' => (string) $getEnv('DB_SOCKET', ''),
    ],
];
