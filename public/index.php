<?php

declare(strict_types=1);

use App\App;

try {
    require dirname(__DIR__) . '/app/bootstrap.php';

    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $path = preg_replace('~/index\.php$~i', '', $path) ?? $path;
    $path = rtrim($path, '/');
    if ($path === '') {
        $path = '/';
    }
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($path === '/install' || app_installation_required()) {
        require dirname(__DIR__) . '/install/boot.php';
        return;
    }

    $app = new App();
    $app->run();
} catch (Throwable $throwable) {
    http_response_code(500);
    error_log((string) $throwable);

    $debug = function_exists('app_debug')
        ? app_debug()
        : filter_var((string) (getenv('APP_DEBUG') ?: 'false'), FILTER_VALIDATE_BOOL);

    if ($debug) {
        echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Application Error</title><style>body{font-family:Arial,sans-serif;padding:24px;background:#f8fafc;color:#0f172a}pre{white-space:pre-wrap;background:#fff;border:1px solid #cbd5e1;padding:16px;border-radius:8px}</style></head><body><h1>Application Error</h1><pre>' . htmlspecialchars((string) $throwable, ENT_QUOTES, 'UTF-8') . '</pre></body></html>';
        return;
    }

    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Service Unavailable</title><style>body{font-family:Arial,sans-serif;padding:32px;line-height:1.5;background:#f8fafc;color:#0f172a}main{max-width:640px;margin:8vh auto;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:32px;box-shadow:0 10px 30px rgba(15,23,42,.06)}h1{margin-top:0}</style></head><body><main><h1>Service Unavailable</h1><p>The application is temporarily unavailable. Please try again later.</p></main></body></html>';
}
