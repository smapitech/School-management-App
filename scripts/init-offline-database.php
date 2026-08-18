<?php

declare(strict_types=1);

require __DIR__ . '/offline-env.php';
require dirname(__DIR__) . '/app/bootstrap.php';

$config = require dirname(__DIR__) . '/config/database.php';
$config['driver'] = 'sqlite';
$config['sqlite']['path'] = (string) ($_ENV['DB_SQLITE_PATH'] ?? (dirname(__DIR__) . '/storage/offline-testing.sqlite'));

$database = new \App\Database($config);
$database->migrate();

echo "Offline test database ready at " . $config['sqlite']['path'] . PHP_EOL;

