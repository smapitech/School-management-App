<?php

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/app/bootstrap.php';

use App\Database;
use App\Repository;

try {
    $database = new Database(require $root . '/config/database.php');
    $database->migrate();
    $repository = new Repository($database->pdo());

    foreach ($repository->manageableRoles() as $role) {
        $repository->resetRolePermissions($role);
    }

    echo "Role permissions repaired successfully.\n";
    echo "Admin, Teacher, Accountant, and Receptionist were restored to safe default module access.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, "Role permission repair failed: " . $exception->getMessage() . "\n");
    exit(1);
}
