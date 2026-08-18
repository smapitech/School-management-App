<?php

declare(strict_types=1);

require __DIR__ . '/offline-env.php';

$publicRoot = realpath(dirname(__DIR__) . '/public');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($publicRoot !== false) {
    $assetPath = realpath($publicRoot . $requestPath);
    if ($assetPath !== false && is_file($assetPath) && str_starts_with($assetPath, $publicRoot . DIRECTORY_SEPARATOR)) {
        return false;
    }
}

require dirname(__DIR__) . '/public/index.php';

