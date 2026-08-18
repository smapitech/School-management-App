<?php

declare(strict_types=1);

try {
    $bootstrapPath = dirname(__DIR__) . '/app/bootstrap.php';

    if (!function_exists('app_base_path') && is_file($bootstrapPath)) {
        try {
            require $bootstrapPath;
        } catch (Throwable $bootstrapThrowable) {
            error_log('[Installer bootstrap] ' . $bootstrapThrowable->getMessage());
        }
    }

    if (!function_exists('app_base_path')) {
        if (!function_exists('app_base_path')) {
            function app_base_path(): string
            {
                return dirname(__DIR__);
            }
        }

        if (!function_exists('app_env_path')) {
            function app_env_path(): string
            {
                return app_base_path() . '/.env';
            }
        }

        if (!function_exists('e')) {
            function e(mixed $value): string
            {
                return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
            }
        }

        if (!function_exists('env')) {
            function env(string $key, mixed $default = null): mixed
            {
                $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

                if ($value === false || $value === null) {
                    return $default;
                }

                if (is_string($value)) {
                    $trimmed = trim($value);
                    $normalized = strtolower($trimmed);

                    return match ($normalized) {
                        'true', '(true)', 'yes', 'on' => true,
                        'false', '(false)', 'no', 'off' => false,
                        'null', '(null)' => null,
                        default => $trimmed,
                    };
                }

                return $value;
            }
        }

        if (!function_exists('app_environment')) {
            function app_environment(): string
            {
                $environment = trim((string) env('APP_ENV', 'local'));

                return $environment !== '' ? strtolower($environment) : 'local';
            }
        }

        if (!function_exists('app_debug')) {
            function app_debug(): bool
            {
                $debug = env('APP_DEBUG', app_environment() !== 'production');

                if (is_bool($debug)) {
                    return $debug;
                }

                return filter_var($debug, FILTER_VALIDATE_BOOL);
            }
        }

        if (!function_exists('csrf_token')) {
            function csrf_token(): string
            {
                if (empty($_SESSION['_csrf_token'])) {
                    $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
                }

                return $_SESSION['_csrf_token'];
            }
        }

        if (!function_exists('csrf_field')) {
            function csrf_field(): string
            {
                return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
            }
        }

        if (!function_exists('csrf_verify')) {
            function csrf_verify(?string $token): bool
            {
                return is_string($token) && hash_equals($_SESSION['_csrf_token'] ?? '', $token);
            }
        }

        if (!function_exists('app_installed')) {
            function app_installed(): bool
            {
                if (!is_file(app_env_path())) {
                    return false;
                }

                $installed = env('APP_INSTALLED', null);
                if ($installed === null) {
                    return app_environment() !== 'production';
                }

                if (is_bool($installed)) {
                    return $installed;
                }

                return filter_var($installed, FILTER_VALIDATE_BOOL);
            }
        }

        $envPath = app_env_path();
        if (is_file($envPath) && is_readable($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES);
            if ($lines !== false) {
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
                        }
                    }
                    if (function_exists('putenv') && getenv($key) === false) {
                        putenv($key . '=' . $value);
                    }
                    $_ENV[$key] = $_ENV[$key] ?? $value;
                    $_SERVER[$key] = $_SERVER[$key] ?? $value;
                }
            }
        }

        $sessionPath = app_base_path() . '/storage/sessions';
        if (!is_dir($sessionPath)) {
            @mkdir($sessionPath, 0775, true);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_save_path($sessionPath);
            session_start();
        }
    }

    final class StandaloneInstaller
    {
        private string $basePath;
        private string $viewPath;

        public function __construct(?string $basePath = null)
        {
            $this->basePath = $basePath ?? app_base_path();
            $this->viewPath = $this->basePath . '/resources/views';
        }

        public function handle(string $method, string $path): void
        {
            $path = $this->normalizePath($path);

            if (app_installation_complete() && $path === '/install') {
                $this->redirect('/login');
            }

            if ($method === 'POST') {
                $this->process();
                return;
            }

            $this->renderForm([
                'title' => 'Installation Wizard',
                'requirements' => $this->requirements(),
                'errors' => $_SESSION['installer_errors'] ?? [],
                'old' => $_SESSION['installer_old'] ?? $this->defaultValues(),
            ]);

            unset($_SESSION['installer_errors'], $_SESSION['installer_old']);
        }

        private function process(): void
        {
            if (!csrf_verify($_POST['_csrf_token'] ?? null)) {
                $this->storeFormState(['Invalid security token.'], $_POST);
                $this->renderForm([
                    'title' => 'Installation Wizard',
                    'requirements' => $this->requirements(),
                    'errors' => $_SESSION['installer_errors'] ?? [],
                    'old' => $_SESSION['installer_old'] ?? $this->defaultValues(),
                ]);
                return;
            }

            $input = $this->collectInput($_POST);
            $errors = $this->validate($input);

            if ($errors !== []) {
                $this->storeFormState($errors, $input);
                $this->renderForm([
                    'title' => 'Installation Wizard',
                    'requirements' => $this->requirements(),
                    'errors' => $_SESSION['installer_errors'] ?? [],
                    'old' => $_SESSION['installer_old'] ?? $this->defaultValues(),
                ]);
                return;
            }

            try {
                $connection = $this->connectDatabase($input);
                $pdo = $connection['pdo'];
                $input['db_host'] = $connection['db_host'];
                $input['db_socket'] = $connection['db_socket'];
                $input['db_port'] = (string) $connection['db_port'];
                $freshInstall = !$this->tableExists($pdo, 'users');
                $this->importSchema($pdo, $this->basePath . '/database/schema_mysql.sql', $freshInstall);
                $this->seedInstallationData($pdo, $input);
                $this->ensureWritableDirectories();
                $this->writeEnvironmentFile($input);
            } catch (Throwable $throwable) {
                $this->storeFormState([$throwable->getMessage()], $input);
                $this->renderForm([
                    'title' => 'Installation Wizard',
                    'requirements' => $this->requirements(),
                    'errors' => $_SESSION['installer_errors'] ?? [],
                    'old' => $_SESSION['installer_old'] ?? $this->defaultValues(),
                ]);
                return;
            }

            unset($_SESSION['installer_errors'], $_SESSION['installer_old']);
            $this->redirect('/login?installed=1');
        }

        private function collectInput(array $source): array
        {
            return [
                'school_name' => trim((string) ($source['school_name'] ?? '')),
                'school_short_name' => trim((string) ($source['school_short_name'] ?? '')),
                'superadmin_name' => trim((string) ($source['superadmin_name'] ?? '')),
                'superadmin_email' => trim((string) ($source['superadmin_email'] ?? '')),
                'superadmin_password' => (string) ($source['superadmin_password'] ?? ''),
                'superadmin_password_confirmation' => (string) ($source['superadmin_password_confirmation'] ?? ''),
                'db_host' => trim((string) ($source['db_host'] ?? '127.0.0.1')),
                'db_port' => trim((string) ($source['db_port'] ?? '3306')),
                'db_name' => trim((string) ($source['db_name'] ?? '')),
                'db_user' => trim((string) ($source['db_user'] ?? '')),
                'db_password' => (string) ($source['db_password'] ?? ''),
                'db_charset' => trim((string) ($source['db_charset'] ?? 'utf8mb4')),
                'db_socket' => trim((string) ($source['db_socket'] ?? '')),
                'app_timezone' => trim((string) ($source['app_timezone'] ?? 'Africa/Lagos')),
            ];
        }

        private function validate(array $input): array
        {
            $errors = [];

            foreach (['school_name', 'school_short_name', 'superadmin_name', 'superadmin_email', 'superadmin_password', 'superadmin_password_confirmation', 'db_host', 'db_port', 'db_name', 'db_user'] as $field) {
                if (trim((string) ($input[$field] ?? '')) === '') {
                    $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                }
            }

            if ($input['superadmin_password'] !== $input['superadmin_password_confirmation']) {
                $errors[] = 'Superadmin passwords do not match.';
            }

            if (!filter_var($input['superadmin_email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid superadmin email is required.';
            }

            if (!preg_match('/^[A-Za-z0-9_]+$/', $input['db_name'])) {
                $errors[] = 'Database name may only contain letters, numbers, and underscores.';
            }

            if (!preg_match('/^[A-Za-z0-9_.-]+$/', $input['db_host'])) {
                $errors[] = 'Database host contains invalid characters.';
            }

            if ((int) $input['db_port'] <= 0 || (int) $input['db_port'] > 65535) {
                $errors[] = 'Database port must be between 1 and 65535.';
            }

            if (trim($input['app_timezone']) === '') {
                $errors[] = 'Application timezone is required.';
            } elseif (@timezone_open($input['app_timezone']) === false) {
                $errors[] = 'Application timezone is not valid.';
            }

            return array_values(array_unique($errors));
        }

        private function requirements(): array
        {
            return [
                ['label' => 'PHP 8.1+', 'ok' => version_compare(PHP_VERSION, '8.1.0', '>=')],
                ['label' => 'pdo_mysql extension', 'ok' => extension_loaded('pdo_mysql')],
                ['label' => 'mbstring extension', 'ok' => extension_loaded('mbstring')],
                ['label' => 'openssl extension', 'ok' => extension_loaded('openssl')],
                ['label' => 'fileinfo extension', 'ok' => extension_loaded('fileinfo')],
                ['label' => 'Writable storage folder', 'ok' => is_writable($this->basePath . '/storage') || @mkdir($this->basePath . '/storage', 0775, true)],
                ['label' => 'Writable uploads folder', 'ok' => is_writable($this->basePath . '/public/uploads') || @mkdir($this->basePath . '/public/uploads', 0775, true)],
            ];
        }

        private function connectDatabase(array $input): array
        {
            $lastThrowable = null;
            $charset = $this->sanitizeCharset((string) ($input['db_charset'] ?? 'utf8mb4'));

            foreach ($this->databaseConnectionCandidates($input) as $candidate) {
                try {
                    $dsn = $this->buildMysqlDsnFromCandidate($candidate, true, $input['db_name'], $charset);
                    $pdo = new PDO($dsn, $input['db_user'], $input['db_password'], $this->pdoOptions());

                    return [
                        'pdo' => $pdo,
                        'db_host' => $candidate['host'],
                        'db_port' => $candidate['port'],
                        'db_socket' => $candidate['socket'],
                    ];
                } catch (Throwable $throwable) {
                    $lastThrowable = $throwable;
                }

                try {
                    $serverDsn = $this->buildMysqlDsnFromCandidate($candidate, false, $input['db_name'], $charset);
                    $server = new PDO($serverDsn, $input['db_user'], $input['db_password'], $this->pdoOptions());
                    $this->createDatabaseIfNeeded($server, $input['db_name']);

                    $dsn = $this->buildMysqlDsnFromCandidate($candidate, true, $input['db_name'], $charset);
                    $pdo = new PDO($dsn, $input['db_user'], $input['db_password'], $this->pdoOptions());

                    return [
                        'pdo' => $pdo,
                        'db_host' => $candidate['host'],
                        'db_port' => $candidate['port'],
                        'db_socket' => $candidate['socket'],
                    ];
                } catch (Throwable $throwable) {
                    $lastThrowable = $throwable;
                }
            }

            $message = $lastThrowable?->getMessage() ?? 'Unable to connect to the database.';
            throw new RuntimeException($this->augmentDatabaseErrorMessage($message, $input));
        }

        private function pdoOptions(): array
        {
            return [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
        }

        private function createDatabaseIfNeeded(PDO $pdo, string $databaseName): void
        {
            $safeName = $this->quoteIdentifier($databaseName);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS {$safeName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }

        private function databaseConnectionCandidates(array $input): array
        {
            $socket = $this->sanitizeSocket((string) $input['db_socket']);
            $host = $this->sanitizeHost($input['db_host']);
            $port = (int) $input['db_port'];

            if ($socket !== '') {
                return [[
                    'host' => $host,
                    'port' => $port,
                    'socket' => $socket,
                ]];
            }

            $candidates = [];
            $seen = [];
            $addCandidate = static function (string $candidateHost) use (&$candidates, &$seen, $port): void {
                $candidateHost = trim($candidateHost);
                if ($candidateHost === '' || isset($seen[$candidateHost])) {
                    return;
                }

                $seen[$candidateHost] = true;
                $candidates[] = [
                    'host' => $candidateHost,
                    'port' => $port,
                    'socket' => '',
                ];
            };

            $addCandidate($host);

            if (in_array($host, ['127.0.0.1', 'localhost'], true)) {
                $addCandidate($host === '127.0.0.1' ? 'localhost' : '127.0.0.1');
            }

            return $candidates !== [] ? $candidates : [[
                'host' => '127.0.0.1',
                'port' => $port,
                'socket' => '',
            ]];
        }

        private function buildMysqlDsnFromCandidate(array $candidate, bool $withDatabase, string $databaseName, string $charset): string
        {
            $charset = $this->sanitizeCharset($charset);
            $socket = trim((string) ($candidate['socket'] ?? ''));
            $host = trim((string) ($candidate['host'] ?? '127.0.0.1'));
            $port = (int) ($candidate['port'] ?? 3306);

            if ($socket !== '') {
                $dsn = "mysql:unix_socket={$socket}";
            } else {
                $dsn = "mysql:host={$host};port={$port}";
            }

            if ($withDatabase) {
                $dsn .= ';dbname=' . $databaseName;
            }

            return $dsn . ';charset=' . $charset;
        }

        private function augmentDatabaseErrorMessage(string $message, array $input): string
        {
            $database = trim((string) ($input['db_name'] ?? ''));
            $hint = ' Check the MySQL username/password in aaPanel, make sure the user is linked to the database with full privileges, and try DB host "localhost" if you used 127.0.0.1, or "127.0.0.1" if you used localhost.';

            if (stripos($message, 'Access denied') !== false || stripos($message, '1045') !== false) {
                return $message . $hint;
            }

            if (stripos($message, 'Unknown database') !== false || stripos($message, '1049') !== false) {
                return $message . ' The database name ' . ($database !== '' ? $database : 'provided') . ' may not exist yet.' . $hint;
            }

            return $message . $hint;
        }

        private function sanitizeHost(string $host): string
        {
            $host = trim(str_replace(["\0", ';'], '', $host));

            return $host !== '' ? $host : '127.0.0.1';
        }

        private function sanitizeCharset(string $charset): string
        {
            $charset = trim($charset);

            return preg_match('/^[A-Za-z0-9_]+$/', $charset) === 1 ? $charset : 'utf8mb4';
        }

        private function sanitizeSocket(string $socket): string
        {
            return trim(str_replace(["\0", ';'], '', $socket));
        }

        private function quoteIdentifier(string $value): string
        {
            return '`' . str_replace('`', '``', $value) . '`';
        }

        private function importSchema(PDO $pdo, string $schemaPath, bool $includeInserts): void
        {
            $sql = file_get_contents($schemaPath);
            if ($sql === false || trim($sql) === '') {
                throw new RuntimeException('Unable to read the schema file.');
            }

            foreach ($this->splitSqlStatements($sql) as $statement) {
                $trimmed = ltrim($statement);
                if ($trimmed === '') {
                    continue;
                }

                if (!$includeInserts && preg_match('/^INSERT\s+/i', $trimmed) === 1) {
                    continue;
                }

                $pdo->exec($statement);
            }
        }

        private function splitSqlStatements(string $sql): array
        {
            $statements = [];
            $buffer = '';
            $length = strlen($sql);
            $inSingle = false;
            $inDouble = false;
            $inBacktick = false;

            for ($i = 0; $i < $length; $i++) {
                $char = $sql[$i];
                $prev = $i > 0 ? $sql[$i - 1] : '';

                if ($char === "'" && !$inDouble && !$inBacktick && $prev !== '\\') {
                    $inSingle = !$inSingle;
                } elseif ($char === '"' && !$inSingle && !$inBacktick && $prev !== '\\') {
                    $inDouble = !$inDouble;
                } elseif ($char === '`' && !$inSingle && !$inDouble) {
                    $inBacktick = !$inBacktick;
                }

                if ($char === ';' && !$inSingle && !$inDouble && !$inBacktick) {
                    $trimmed = trim($buffer);
                    if ($trimmed !== '') {
                        $statements[] = $trimmed;
                    }
                    $buffer = '';
                    continue;
                }

                $buffer .= $char;
            }

            $trimmed = trim($buffer);
            if ($trimmed !== '') {
                $statements[] = $trimmed;
            }

            return $statements;
        }

        private function tableExists(PDO $pdo, string $table): bool
        {
            $statement = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
            $statement->execute([$table]);

            return (int) $statement->fetchColumn() > 0;
        }

        private function seedInstallationData(PDO $pdo, array $input): void
        {
            $this->upsertSetting($pdo, 'school_name', $input['school_name']);
            $this->upsertSetting($pdo, 'school_short_name', $input['school_short_name']);
            $this->upsertSetting($pdo, 'academic_year', date('Y'));

            $passwordHash = password_hash($input['superadmin_password'], PASSWORD_DEFAULT);

            if ($this->tableExists($pdo, 'users')) {
                $countStatement = $pdo->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
                $countStatement->execute(['superadmin']);

                if ((int) $countStatement->fetchColumn() > 0) {
                    $statement = $pdo->prepare('UPDATE users SET name = ?, email = ?, password = ?, status = ? WHERE role = ? ORDER BY id ASC LIMIT 1');
                    $statement->execute([
                        $input['superadmin_name'],
                        $input['superadmin_email'],
                        $passwordHash,
                        'Active',
                        'superadmin',
                    ]);
                } else {
                    $statement = $pdo->prepare('INSERT INTO users (name, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?)');
                    $statement->execute([
                        $input['superadmin_name'],
                        $input['superadmin_email'],
                        $passwordHash,
                        'superadmin',
                        'Active',
                        date('Y-m-d'),
                    ]);
                }
            }

            if ($this->tableExists($pdo, 'website_settings')) {
                $count = (int) $pdo->query('SELECT COUNT(*) FROM website_settings')->fetchColumn();

                if ($count > 0) {
                    $pdo->prepare('UPDATE website_settings SET active_template = ?, site_title = ?, footer_text = ?, updated_at = NOW() ORDER BY id ASC LIMIT 1')
                        ->execute(['classic', $input['school_name'], 'Building knowledge, character, and confidence.']);
                } else {
                    $pdo->prepare('INSERT INTO website_settings (active_template, site_title, footer_text, created_at) VALUES (?, ?, ?, NOW())')
                        ->execute(['classic', $input['school_name'], 'Building knowledge, character, and confidence.']);
                }
            }
        }

        private function upsertSetting(PDO $pdo, string $key, string $value): void
        {
            $statement = $pdo->prepare(
                'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
            );
            $statement->execute([$key, $value]);
        }

        private function writeEnvironmentFile(array $input): void
        {
            $envPath = app_env_path();
            $content = [
                'APP_NAME="Smapis School Portal"',
                'APP_ENV="production"',
                'APP_KEY="' . $this->generateAppKey() . '"',
                'APP_INSTALLED="true"',
                'APP_DEBUG="false"',
                'APP_TIMEZONE="' . $this->escapeEnvValue($input['app_timezone']) . '"',
                'APP_SESSION_SECURE="true"',
                'APP_SHOW_SAMPLE_ACCOUNTS="false"',
                'DB_CONNECTION="mysql"',
                'DB_HOST="' . $this->escapeEnvValue($input['db_host']) . '"',
                'DB_PORT="' . (string) ((int) $input['db_port']) . '"',
                'DB_DATABASE="' . $this->escapeEnvValue($input['db_name']) . '"',
                'DB_USERNAME="' . $this->escapeEnvValue($input['db_user']) . '"',
                'DB_PASSWORD="' . $this->escapeEnvValue($input['db_password']) . '"',
                'DB_CHARSET="' . $this->escapeEnvValue($input['db_charset']) . '"',
                'DB_SOCKET="' . $this->escapeEnvValue($input['db_socket']) . '"',
            ];

            $written = file_put_contents($envPath, implode(PHP_EOL, $content) . PHP_EOL, LOCK_EX);
            if ($written === false) {
                throw new RuntimeException('Unable to write the .env file. Check file permissions.');
            }
        }

        private function escapeEnvValue(string $value): string
        {
            return addcslashes($value, "\\\"");
        }

        private function generateAppKey(): string
        {
            return 'base64:' . base64_encode(random_bytes(32));
        }

        private function ensureWritableDirectories(): void
        {
            foreach ([
                $this->basePath . '/storage',
                $this->basePath . '/storage/sessions',
                $this->basePath . '/public/uploads',
            ] as $directory) {
                if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
                    throw new RuntimeException('Unable to create or access: ' . $directory);
                }
            }
        }

        private function defaultValues(): array
        {
            return [
                'school_name' => 'Smapis School Portal',
                'school_short_name' => 'Smapis',
                'superadmin_name' => 'Super Admin',
                'superadmin_email' => 'admin@example.com',
                'superadmin_password' => '',
                'superadmin_password_confirmation' => '',
                'db_host' => '127.0.0.1',
                'db_port' => '3306',
                'db_name' => '',
                'db_user' => '',
                'db_password' => '',
                'db_charset' => 'utf8mb4',
                'db_socket' => '',
                'app_timezone' => 'Africa/Lagos',
            ];
        }

        private function storeFormState(array $errors, array $old): void
        {
            $_SESSION['installer_errors'] = $errors;
            $_SESSION['installer_old'] = array_merge($this->defaultValues(), $old);
        }

        private function renderForm(array $data): void
        {
            extract($data, EXTR_SKIP);
            $viewFile = $this->viewPath . '/install/wizard.php';

            if (is_file($viewFile)) {
                try {
                    require $viewFile;
                    return;
                } catch (Throwable $throwable) {
                    error_log('[Installer view] ' . $throwable->getMessage());
                }
            }

            $this->renderFallbackForm($data);
        }

        private function renderFallbackForm(array $data): void
        {
            $title = (string) ($data['title'] ?? 'Installation Wizard');
            $requirements = is_array($data['requirements'] ?? null) ? $data['requirements'] : [];
            $errors = is_array($data['errors'] ?? null) ? $data['errors'] : [];
            $old = is_array($data['old'] ?? null) ? $data['old'] : $this->defaultValues();

            ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | Smapis School Portal</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f4f7fb;color:#111827;margin:0;padding:24px}
        .wrap{max-width:1100px;margin:0 auto}
        .card{background:#fff;border:1px solid #dbe4f0;border-radius:12px;padding:20px;margin-bottom:18px}
        .grid{display:grid;grid-template-columns:1fr 1.1fr;gap:18px}
        .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
        .field{display:grid;gap:6px}
        .field.full{grid-column:1/-1}
        input{padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font:inherit}
        button{padding:12px 18px;border:0;border-radius:10px;background:#2563eb;color:#fff;font-weight:700;cursor:pointer}
        ul{padding-left:18px}
        .ok{color:#15803d}.bad{color:#b91c1c}
        .actions{display:flex;justify-content:flex-end}
        @media (max-width:960px){.grid,.form-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Smapis School Portal installation</h1>
        <p>Fill in your aaPanel MySQL details and the installer will create the database if needed, import the full schema, write the production .env file, and hand you off to the login page.</p>
    </div>
    <?php if (!empty($errors)): ?>
        <div class="card bad">
            <strong>Fix these issues:</strong>
            <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>
    <div class="grid">
        <div class="card">
            <h2>Server checks</h2>
            <ul>
                <?php foreach ($requirements as $item): ?>
                    <li><span class="<?= !empty($item['ok']) ? 'ok' : 'bad' ?>"><?= !empty($item['ok']) ? 'OK' : '!' ?></span> <?= e($item['label'] ?? '') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="card">
            <h2>Database and admin setup</h2>
            <form method="post" action="/install" class="form-grid">
                <?= csrf_field() ?>
                <div class="field full"><label>School Name</label><input name="school_name" value="<?= e($old['school_name'] ?? '') ?>" required></div>
                <div class="field"><label>School Short Name</label><input name="school_short_name" value="<?= e($old['school_short_name'] ?? '') ?>" required></div>
                <div class="field"><label>Timezone</label><input name="app_timezone" value="<?= e($old['app_timezone'] ?? 'Africa/Lagos') ?>" required></div>
                <div class="field"><label>Superadmin Name</label><input name="superadmin_name" value="<?= e($old['superadmin_name'] ?? '') ?>" required></div>
                <div class="field"><label>Superadmin Email</label><input type="email" name="superadmin_email" value="<?= e($old['superadmin_email'] ?? '') ?>" required></div>
                <div class="field"><label>Superadmin Password</label><input type="password" name="superadmin_password" value="" required></div>
                <div class="field"><label>Confirm Password</label><input type="password" name="superadmin_password_confirmation" value="" required></div>
                <div class="field"><label>MySQL Host</label><input name="db_host" value="<?= e($old['db_host'] ?? '127.0.0.1') ?>" required></div>
                <div class="field"><label>MySQL Port</label><input type="number" min="1" max="65535" name="db_port" value="<?= e($old['db_port'] ?? '3306') ?>" required></div>
                <div class="field"><label>Database Name</label><input name="db_name" value="<?= e($old['db_name'] ?? '') ?>" required></div>
                <div class="field"><label>Database User</label><input name="db_user" value="<?= e($old['db_user'] ?? '') ?>" required></div>
                <div class="field"><label>Database Password</label><input type="password" name="db_password" value=""></div>
                <div class="field"><label>Database Charset</label><input name="db_charset" value="<?= e($old['db_charset'] ?? 'utf8mb4') ?>"></div>
                <div class="field full"><label>DB Socket</label><input name="db_socket" value="<?= e($old['db_socket'] ?? '') ?>"></div>
                <div class="field full actions"><button type="submit">Install Now</button></div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
            <?php
        }

        private function normalizePath(string $path): string
        {
            $path = preg_replace('~/index\.php$~i', '', $path) ?? $path;
            $path = rtrim($path, '/');

            return $path !== '' ? $path : '/';
        }

        private function redirect(string $path): never
        {
            header('Location: ' . $path);
            exit;
        }
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/install', PHP_URL_PATH) ?: '/install';
    (new StandaloneInstaller())->handle($method, $path);
} catch (Throwable $throwable) {
    http_response_code(500);
    error_log((string) $throwable);

    $debug = function_exists('app_debug')
        ? app_debug()
        : filter_var((string) (getenv('APP_DEBUG') ?: 'false'), FILTER_VALIDATE_BOOL);

    if ($debug) {
        echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Installation Error</title><style>body{font-family:Arial,sans-serif;padding:24px;background:#f8fafc;color:#0f172a}pre{white-space:pre-wrap;background:#fff;border:1px solid #cbd5e1;padding:16px;border-radius:8px}</style></head><body><h1>Installation Error</h1><pre>' . htmlspecialchars((string) $throwable, ENT_QUOTES, 'UTF-8') . '</pre></body></html>';
        return;
    }

    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Installation Unavailable</title><style>body{font-family:Arial,sans-serif;padding:32px;line-height:1.5;background:#f8fafc;color:#0f172a}main{max-width:640px;margin:8vh auto;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:32px;box-shadow:0 10px 30px rgba(15,23,42,.06)}h1{margin-top:0}</style></head><body><main><h1>Installation Unavailable</h1><p>The installation wizard is temporarily unavailable. Please check your server logs and try again.</p></main></body></html>';
}
