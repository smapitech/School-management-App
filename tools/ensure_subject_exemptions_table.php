<?php
// Ensure student_subject_exemptions exists using the project's configured database.
declare(strict_types=1);

$root = rtrim($argv[1] ?? dirname(__DIR__), '/');
$configFile = $root . '/config/database.php';
if (!is_file($configFile)) {
    fwrite(STDERR, "Missing config/database.php\n");
    exit(1);
}
$config = require $configFile;
$driver = strtolower((string) ($config['driver'] ?? 'sqlite'));
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
if ($driver === 'mysql') {
    $mysql = $config['mysql'] ?? [];
    $charset = $mysql['charset'] ?? 'utf8mb4';
    $port = (int) ($mysql['port'] ?? 3306);
    $socket = trim((string) ($mysql['socket'] ?? ''));
    $dsn = $socket !== ''
        ? "mysql:unix_socket={$socket};dbname={$mysql['database']};charset={$charset}"
        : "mysql:host={$mysql['host']};port={$port};dbname={$mysql['database']};charset={$charset}";
    $pdo = new PDO($dsn, $mysql['username'] ?? '', $mysql['password'] ?? '', $options);
    $pdo->exec("CREATE TABLE IF NOT EXISTS student_subject_exemptions (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        student_id BIGINT UNSIGNED NOT NULL,
        class_name VARCHAR(120) NOT NULL,
        section VARCHAR(50) NOT NULL DEFAULT '',
        subject_id BIGINT UNSIGNED NOT NULL,
        school_term VARCHAR(120) NOT NULL DEFAULT '',
        school_session VARCHAR(120) NOT NULL DEFAULT '',
        reason VARCHAR(255) NOT NULL DEFAULT '',
        created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY student_subject_scope_unique (student_id, class_name, section, subject_id, school_term, school_session),
        KEY idx_subject_exemptions_student (student_id),
        KEY idx_subject_exemptions_subject (subject_id),
        KEY idx_subject_exemptions_scope (class_name, section, school_term, school_session)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} else {
    $path = $config['sqlite']['path'] ?? $root . '/storage/database.sqlite';
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $pdo = new PDO('sqlite:' . $path, null, null, $options);
    $pdo->exec("CREATE TABLE IF NOT EXISTS student_subject_exemptions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        student_id INTEGER NOT NULL,
        class_name TEXT NOT NULL,
        section TEXT NOT NULL DEFAULT '',
        subject_id INTEGER NOT NULL,
        school_term TEXT NOT NULL DEFAULT '',
        school_session TEXT NOT NULL DEFAULT '',
        reason TEXT NOT NULL DEFAULT '',
        created_by INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL DEFAULT '',
        UNIQUE(student_id, class_name, section, subject_id, school_term, school_session)
    );
    CREATE INDEX IF NOT EXISTS idx_subject_exemptions_student ON student_subject_exemptions (student_id);
    CREATE INDEX IF NOT EXISTS idx_subject_exemptions_subject ON student_subject_exemptions (subject_id);
    CREATE INDEX IF NOT EXISTS idx_subject_exemptions_scope ON student_subject_exemptions (class_name, section, school_term, school_session);");
}
echo "student_subject_exemptions table is ready using {$driver}.\n";
