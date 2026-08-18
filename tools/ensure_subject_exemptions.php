<?php

declare(strict_types=1);

$root = $argv[1] ?? dirname(__DIR__);
$root = rtrim($root, '/');

require $root . '/app/Database.php';

$config = require $root . '/config/database.php';
$database = new App\Database($config);
$database->migrate();

echo "Subject exemption migration checked successfully.\n";
