<?php

declare(strict_types=1);

namespace App;

use PDO;
use Throwable;

final class Installer
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
        $path = preg_replace('~/index\.php$~i', '', $path) ?? $path;
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        if (app_installed() && $path === '/install') {
            $this->redirect('/login');
        }

        if ($path !== '/install') {
            $this->redirect('/install');
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
            $pdo = $this->connectDatabase($input);
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

    private function connectServer(array $input): PDO
    {
        $options = $this->pdoOptions();
        $dsn = $this->buildMysqlDsn($input, false);

        return new PDO($dsn, $input['db_user'], $input['db_password'], $options);
    }

    private function connectDatabase(array $input): PDO
    {
        $options = $this->pdoOptions();
        $dsn = $this->buildMysqlDsn($input, true);

        try {
            return new PDO($dsn, $input['db_user'], $input['db_password'], $options);
        } catch (Throwable) {
            $server = $this->connectServer($input);
            $this->createDatabaseIfNeeded($server, $input['db_name']);

            return new PDO($dsn, $input['db_user'], $input['db_password'], $options);
        }
    }

    private function createDatabaseIfNeeded(PDO $pdo, string $databaseName): void
    {
        $safeName = $this->quoteIdentifier($databaseName);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS {$safeName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    private function buildMysqlDsn(array $input, bool $withDatabase): string
    {
        $charset = $this->sanitizeCharset($input['db_charset']);
        $socket = $this->sanitizeSocket((string) $input['db_socket']);
        $host = $this->sanitizeHost($input['db_host']);
        $port = (int) $input['db_port'];

        if ($socket !== '') {
            $dsn = "mysql:unix_socket={$socket}";
        } else {
            $dsn = "mysql:host={$host};port={$port}";
        }

        if ($withDatabase) {
            $dsn .= ';dbname=' . $input['db_name'];
        }

        return $dsn . ';charset=' . $charset;
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
            throw new \RuntimeException('Unable to read the schema file.');
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
        $statement = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
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
                $pdo->prepare('UPDATE website_settings SET site_title = ?, updated_at = NOW()')
                    ->execute([$input['school_name']]);
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
            throw new \RuntimeException('Unable to write the .env file. Check file permissions.');
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
                throw new \RuntimeException('Unable to create or access: ' . $directory);
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
        require $this->viewPath . '/install/wizard.php';
    }

    private function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}
