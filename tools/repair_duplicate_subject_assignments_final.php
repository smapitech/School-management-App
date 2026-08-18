<?php

declare(strict_types=1);

/**
 * Final duplicate subject-assignment repair for Smapis custom school management.
 * Works with MySQL and SQLite, but is designed for the live aaPanel MySQL site.
 */

$root = dirname(__DIR__);

$envPath = $root . '/.env';
$env = [];
if (is_readable($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_starts_with($line, 'export ')) {
            $line = trim(substr($line, 7));
        }
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }
        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));
        if ($value !== '') {
            $first = $value[0];
            $last = substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
                if ($first === '"') {
                    $value = stripcslashes($value);
                }
            }
        }
        $env[$key] = $value;
    }
}

$get = static fn (string $key, mixed $default = ''): mixed => $env[$key] ?? getenv($key) ?: $default;
$driver = strtolower((string) $get('DB_CONNECTION', 'mysql'));

if ($driver === 'sqlite') {
    if (!extension_loaded('pdo_sqlite')) {
        fwrite(STDERR, "pdo_sqlite is not available in this CLI PHP. Use the aaPanel PHP binary or run from the web app.\n");
        exit(2);
    }
    $path = (string) $get('DB_SQLITE_PATH', $root . '/storage/database.sqlite');
    $pdo = new PDO('sqlite:' . $path, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} else {
    if (!extension_loaded('pdo_mysql')) {
        fwrite(STDERR, "pdo_mysql is not available in this CLI PHP binary.\n");
        fwrite(STDERR, "Run with aaPanel PHP, for example: /www/server/php/82/bin/php tools/repair_duplicate_subject_assignments_final.php\n");
        exit(3);
    }

    $host = (string) $get('DB_HOST', '127.0.0.1');
    $port = (int) $get('DB_PORT', 3306);
    $database = (string) $get('DB_DATABASE', '');
    $username = (string) $get('DB_USERNAME', '');
    $password = (string) $get('DB_PASSWORD', '');
    $charset = (string) $get('DB_CHARSET', 'utf8mb4');
    $socket = trim((string) $get('DB_SOCKET', ''));

    if ($database === '' || $username === '') {
        fwrite(STDERR, "DB_DATABASE or DB_USERNAME is empty. Please check .env.\n");
        exit(4);
    }

    $dsn = $socket !== ''
        ? "mysql:unix_socket={$socket};dbname={$database};charset={$charset}"
        : "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

$pdo->beginTransaction();
try {
    $before = (int) $pdo->query('SELECT COUNT(*) FROM subject_assignments')->fetchColumn();

    if ($driver === 'sqlite') {
        $pdo->exec("UPDATE subject_assignments
            SET subject_id = (
                SELECT MIN(s2.id)
                FROM subjects s1
                JOIN subjects s2 ON LOWER(TRIM(s2.subject_name)) = LOWER(TRIM(s1.subject_name))
                WHERE s1.id = subject_assignments.subject_id
            )
            WHERE subject_id IN (SELECT id FROM subjects WHERE TRIM(subject_name) <> '')");

        $pdo->exec("DELETE FROM subject_assignments
            WHERE id NOT IN (
                SELECT MIN(id)
                FROM subject_assignments
                GROUP BY LOWER(TRIM(class_name)), LOWER(TRIM(section)), subject_id
            )");
    } else {
        $pdo->exec('DROP TEMPORARY TABLE IF EXISTS tmp_subject_canonical');
        $pdo->exec("CREATE TEMPORARY TABLE tmp_subject_canonical AS
            SELECT LOWER(TRIM(subject_name)) AS subject_key, MIN(id) AS canonical_id
            FROM subjects
            WHERE TRIM(subject_name) <> ''
            GROUP BY LOWER(TRIM(subject_name))");

        $pdo->exec("UPDATE subject_assignments sa
            JOIN subjects s ON s.id = sa.subject_id
            JOIN tmp_subject_canonical c ON c.subject_key = LOWER(TRIM(s.subject_name))
            SET sa.subject_id = c.canonical_id
            WHERE sa.subject_id <> c.canonical_id");

        $pdo->exec("DELETE sa1 FROM subject_assignments sa1
            JOIN subject_assignments sa2
                ON LOWER(TRIM(sa1.class_name)) = LOWER(TRIM(sa2.class_name))
                AND LOWER(TRIM(sa1.section)) = LOWER(TRIM(sa2.section))
                AND sa1.subject_id = sa2.subject_id
                AND sa1.id > sa2.id");
    }

    $after = (int) $pdo->query('SELECT COUNT(*) FROM subject_assignments')->fetchColumn();
    $removed = max(0, $before - $after);
    $pdo->commit();

    echo "Duplicate subject assignment repair completed.\n";
    echo "Before: {$before}\n";
    echo "After: {$after}\n";
    echo "Removed duplicate row(s): {$removed}\n";
    exit(0);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, "Repair failed: " . $exception->getMessage() . PHP_EOL);
    exit(5);
}
