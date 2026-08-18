<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);
$envFile = $basePath . '/.env';

$loadEnvironmentFile = static function (string $path): void {
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (str_starts_with($line, 'export ')) {
            $line = trim(substr($line, 7));
        }

        $position = strpos($line, '=');
        if ($position === false) {
            continue;
        }

        $key = trim(substr($line, 0, $position));
        $value = trim(substr($line, $position + 1));

        if ($key === '') {
            continue;
        }

        if ($value !== '') {
            $first = $value[0];
            $last = substr($value, -1);

            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
                if ($first === '"') {
                    $value = stripcslashes($value);
                }
            } else {
                $inlineComment = strpos($value, ' #');
                if ($inlineComment !== false) {
                    $value = rtrim(substr($value, 0, $inlineComment));
                }
            }
        }

        if (function_exists('putenv') && getenv($key) === false) {
            putenv($key . '=' . $value);
        }

        $_ENV[$key] = $_ENV[$key] ?? $value;
        $_SERVER[$key] = $_SERVER[$key] ?? $value;
    }
};

$loadEnvironmentFile($envFile);

require __DIR__ . '/helpers.php';

$timezone = trim((string) env('APP_TIMEZONE', 'Africa/Lagos'));
if ($timezone !== '') {
    if (!@date_default_timezone_set($timezone)) {
        date_default_timezone_set('Africa/Lagos');
    }
}

$debug = app_debug();
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

$sessionPath = dirname(__DIR__) . '/storage/sessions';

if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0775, true);
}

if (session_status() === PHP_SESSION_NONE) {
    $secureTransport = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443
        || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https'
        || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '')) === 'on';

    $secureSession = env(
        'APP_SESSION_SECURE',
        $secureTransport
    );

    if (!is_bool($secureSession)) {
        $secureSession = filter_var($secureSession, FILTER_VALIDATE_BOOL);
    }

    $secureSession = $secureSession && $secureTransport;

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => (bool) $secureSession,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_save_path($sessionPath);
    session_start();
}

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

if (!is_dir(dirname(__DIR__) . '/storage')) {
    mkdir(dirname(__DIR__) . '/storage', 0775, true);
}
