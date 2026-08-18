<?php $old = $old ?? []; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Installation Wizard') ?> | Smapis School Portal</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f7fb;
            --card: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: #dbe4f0;
            --primary: #2563eb;
            --success: #15803d;
            --danger: #b91c1c;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(180deg, #f8fbff 0%, var(--bg) 100%);
            color: var(--text);
        }
        .installer {
            max-width: 1120px;
            margin: 0 auto;
            padding: 32px 20px 48px;
        }
        .hero {
            display: grid;
            gap: 12px;
            margin-bottom: 24px;
        }
        .eyebrow {
            margin: 0;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
        }
        h1, h2, h3, p { margin-top: 0; }
        h1 { margin-bottom: 0; font-size: 34px; line-height: 1.1; }
        .lead { margin: 0; max-width: 760px; color: var(--muted); font-size: 16px; line-height: 1.6; }
        .notice, .card, .alert-error {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }
        .notice {
            padding: 16px 18px;
            margin-bottom: 20px;
            color: var(--muted);
        }
        .notice strong { color: var(--text); }
        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 1.15fr;
            gap: 20px;
        }
        .card {
            padding: 22px;
        }
        .card h2 {
            font-size: 20px;
            margin-bottom: 14px;
        }
        .requirements {
            display: grid;
            gap: 10px;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .requirements li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fafcff;
        }
        .badge {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            flex: 0 0 auto;
        }
        .badge.ok { background: #dcfce7; color: var(--success); }
        .badge.bad { background: #fee2e2; color: var(--danger); }
        .requirements span:last-child { color: var(--text); }
        .stack { display: grid; gap: 14px; }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .field {
            display: grid;
            gap: 7px;
        }
        .field.full { grid-column: 1 / -1; }
        .field label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 700;
        }
        .field input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 11px 12px;
            font: inherit;
            color: var(--text);
            background: #fff;
        }
        .field input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }
        .help {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }
        .alert-error {
            padding: 14px 16px;
            margin-bottom: 16px;
            border-color: #fecaca;
            background: #fff5f5;
            color: var(--danger);
        }
        .alert-error ul {
            margin: 0;
            padding-left: 18px;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 18px;
        }
        button {
            border: 0;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            padding: 12px 18px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
        button:hover { filter: brightness(0.96); }
        .footnote {
            margin-top: 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }
        @media (max-width: 960px) {
            .layout, .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <main class="installer">
        <section class="hero">
            <p class="eyebrow">First run setup</p>
            <h1>Smapis School Portal installation</h1>
            <p class="lead">Fill in your aaPanel MySQL details and the installer will create the database if needed, import the full schema, write the production `.env` file, and hand you off to the login page.</p>
        </section>

        <section class="notice">
            <strong>Before you begin:</strong> make sure your MySQL user has permission to create databases, or create the target database in aaPanel first.
        </section>

        <?php if (!empty($errors)): ?>
            <section class="alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <section class="layout">
            <article class="card stack">
                <div>
                    <h2>Server checks</h2>
                    <p class="help">These checks help confirm the hosting environment is ready.</p>
                </div>
                <ul class="requirements">
                    <?php foreach ($requirements as $item): ?>
                        <li>
                            <span class="badge <?= !empty($item['ok']) ? 'ok' : 'bad' ?>"><?= !empty($item['ok']) ? 'OK' : '!' ?></span>
                            <span><?= e($item['label'] ?? '') ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <article class="card">
                <h2>Database and admin setup</h2>
                <form method="post" action="/install" class="stack">
                    <?= csrf_field() ?>
                    <div class="form-grid">
                        <div class="field full">
                            <label for="school_name">School Name</label>
                            <input id="school_name" name="school_name" value="<?= e($old['school_name'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label for="school_short_name">School Short Name</label>
                            <input id="school_short_name" name="school_short_name" value="<?= e($old['school_short_name'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label for="app_timezone">Timezone</label>
                            <input id="app_timezone" name="app_timezone" value="<?= e($old['app_timezone'] ?? 'Africa/Lagos') ?>" required>
                        </div>
                        <div class="field">
                            <label for="superadmin_name">Superadmin Name</label>
                            <input id="superadmin_name" name="superadmin_name" value="<?= e($old['superadmin_name'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label for="superadmin_email">Superadmin Email</label>
                            <input id="superadmin_email" type="email" name="superadmin_email" value="<?= e($old['superadmin_email'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label for="superadmin_password">Superadmin Password</label>
                            <input id="superadmin_password" type="password" name="superadmin_password" value="" required>
                        </div>
                        <div class="field">
                            <label for="superadmin_password_confirmation">Confirm Password</label>
                            <input id="superadmin_password_confirmation" type="password" name="superadmin_password_confirmation" value="" required>
                        </div>
                        <div class="field">
                            <label for="db_host">MySQL Host</label>
                            <input id="db_host" name="db_host" value="<?= e($old['db_host'] ?? '127.0.0.1') ?>" required>
                        </div>
                        <div class="field">
                            <label for="db_port">MySQL Port</label>
                            <input id="db_port" name="db_port" type="number" min="1" max="65535" value="<?= e($old['db_port'] ?? '3306') ?>" required>
                        </div>
                        <div class="field">
                            <label for="db_name">Database Name</label>
                            <input id="db_name" name="db_name" value="<?= e($old['db_name'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label for="db_user">Database User</label>
                            <input id="db_user" name="db_user" value="<?= e($old['db_user'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label for="db_password">Database Password</label>
                            <input id="db_password" type="password" name="db_password" value="">
                        </div>
                        <div class="field">
                            <label for="db_charset">Charset</label>
                            <input id="db_charset" name="db_charset" value="<?= e($old['db_charset'] ?? 'utf8mb4') ?>">
                        </div>
                        <div class="field full">
                            <label for="db_socket">MySQL Socket, optional</label>
                            <input id="db_socket" name="db_socket" value="<?= e($old['db_socket'] ?? '') ?>">
                        </div>
                    </div>

                    <p class="help">The installer writes production `.env` values automatically, sets the app key, and keeps sample login hints hidden in production.</p>

                    <div class="actions">
                        <button type="submit">Install School Portal</button>
                    </div>
                </form>
            </article>
        </section>

        <p class="footnote">When setup finishes, you will be redirected to the login page. If the database already exists, the installer will complete the configuration and update the school identity without asking you to run SQL manually.</p>
    </main>
</body>
</html>
