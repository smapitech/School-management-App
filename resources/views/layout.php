<!doctype html>
<?php $uiTheme = ui_theme_from_settings($settings ?? []); ?>
<?php $isGuest = empty($user); ?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e($title ?? 'School Manager') ?> | <?= e($settings['school_short_name'] ?? 'Custom SMS') ?></title>
    <meta name="theme-color" content="<?= e($uiTheme['sidebar_background']) ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <?php if (!empty($settings['favicon_path'])): ?>
        <link rel="icon" href="<?= e($settings['favicon_path']) ?>">
    <?php endif; ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/pwa/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <style>
        :root {
            --smapis-app-bg: <?= e($uiTheme['app_background']) ?>;
            --smapis-sidebar-bg: <?= e($uiTheme['sidebar_background']) ?>;
            --smapis-primary-btn-bg: <?= e($uiTheme['primary_button_background']) ?>;
            --smapis-primary-btn-text: <?= e($uiTheme['primary_button_text']) ?>;
            --smapis-active-sidebar-bg: <?= e($uiTheme['active_sidebar_background']) ?>;
            --smapis-active-sidebar-text: <?= e($uiTheme['active_sidebar_text']) ?>;
            --smapis-inactive-sidebar-text: <?= e($uiTheme['inactive_sidebar_text']) ?>;
            --smapis-topbar-bg: <?= e($uiTheme['topbar_background']) ?>;
            --smapis-banner-bg: <?= e($uiTheme['banner_background']) ?>;
            --smapis-card-bg: <?= e($uiTheme['card_background']) ?>;
        }
    </style>
    <?php
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $isWebsiteManager = $requestPath === '/website-manager' || strpos($requestPath, '/website-manager/') === 0;
        $publicRoot = dirname(__DIR__, 2) . '/public';
        $appCssVersion = is_file($publicRoot . '/assets/css/app.css') ? (string) filemtime($publicRoot . '/assets/css/app.css') : '1';
        $backendCssVersion = is_file($publicRoot . '/assets/css/backend-pro.css') ? (string) filemtime($publicRoot . '/assets/css/backend-pro.css') : '1';
        $websiteManagerCssVersion = is_file($publicRoot . '/assets/css/website-manager-pro.css') ? (string) filemtime($publicRoot . '/assets/css/website-manager-pro.css') : '1';
        $appJsVersion = is_file($publicRoot . '/assets/js/app.js') ? (string) filemtime($publicRoot . '/assets/js/app.js') : '1';
        $backendJsVersion = is_file($publicRoot . '/assets/js/backend-pro.js') ? (string) filemtime($publicRoot . '/assets/js/backend-pro.js') : '1';
        $pwaJsVersion = is_file($publicRoot . '/assets/js/pwa.js') ? (string) filemtime($publicRoot . '/assets/js/pwa.js') : '1';
    ?>
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= e($appCssVersion) ?>">
    <?php if (!$isGuest): ?><link rel="stylesheet" href="/assets/css/backend-pro.css?v=<?= e($backendCssVersion) ?>"><?php endif; ?>
    <?php if (!$isGuest && $isWebsiteManager): ?><link rel="stylesheet" href="/assets/css/website-manager-pro.css?v=<?= e($websiteManagerCssVersion) ?>"><?php endif; ?>
</head>
<body class="<?= $isGuest ? 'guest-shell' : 'app-shell-root role-' . e($user['role'] ?? 'guest') ?>">
    <?php if ($isGuest): ?>
        <main class="guest-shell-main">
            <?= $content ?>
        </main>
    <?php else: ?>
        <aside class="sidebar">
            <a href="/" class="brand <?= !empty($settings['logo_path']) ? 'has-full-logo' : 'has-text-brand' ?>" aria-label="<?= e($settings['school_name'] ?? 'School') ?> dashboard">
                <?php if (!empty($settings['logo_path'])): ?>
                    <img class="brand-logo brand-logo-rectangular" src="<?= e($settings['logo_path']) ?>" alt="<?= e($settings['school_name'] ?? 'School') ?>">
                <?php else: ?>
                    <span class="brand-mark"><?= e(strtoupper(substr($settings['school_short_name'] ?? 'CS', 0, 2))) ?></span>
                    <span class="brand-copy">
                        <strong><?= e($settings['school_name'] ?? 'Custom School') ?></strong>
                        <small>Management Suite</small>
                    </span>
                <?php endif; ?>
            </a>

            <nav class="nav">
                <a class="<?= is_active('/') ?><?= is_active('/dashboard') ?>" href="/">
                    <span class="nav-icon"><?= module_icon('layout-dashboard') ?></span> Dashboard
                </a>
                <?php foreach ($modules as $key => $item): ?>
                    <?php $role = $user['role'] ?? ''; ?>
                    <?php
                        $fallbackTitle = ucwords(str_replace(['-', '_'], ' ', (string) $key));
                        $modulePath = trim((string) ($item['path'] ?? ''));
                        $path = $item['role_paths'][$role] ?? ($modulePath !== '' ? $modulePath : '/' . $key);
                        $moduleIcon = trim((string) ($item['icon'] ?? 'school')) ?: 'school';
                        $moduleTitle = trim((string) ($item['role_titles'][$role] ?? ''));
                        if ($moduleTitle === '') {
                            $moduleTitle = ($role === 'teacher' && $key === 'staff')
                                ? 'Teachers'
                                : trim((string) ($item['title'] ?? ''));
                        }
                        if ($moduleTitle === '') {
                            $moduleTitle = $fallbackTitle;
                        }
                    ?>
                    <?php
                        $children = $item['role_children'][$role] ?? $item['children'] ?? [];
                        $children = array_values(array_filter($children, static function (array $child) use ($user, $key): bool {
                            $permissionKey = strtolower(trim((string) ($child['permission_key'] ?? $key)));
                            $permissionAction = strtolower(trim((string) ($child['permission_action'] ?? 'view')));

                            return $permissionKey !== '' && \App\Auth::can($permissionKey, $permissionAction, $user);
                        }));
                    ?>
                    <?php $overviewLabel = $item['overview_labels'][$role] ?? 'Overview'; ?>
                    <?php if (!empty($children)): ?>
                        <?php $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'; ?>
                        <?php
                            $childIsActive = false;
                            foreach ($children as $child) {
                                if ($currentPath === $child['path'] || strpos($currentPath, $child['path'] . '/') === 0) {
                                    $childIsActive = true;
                                    break;
                                }
                            }
                            $isOpen = $currentPath === $path || strpos($currentPath, $path . '/') === 0 || $childIsActive;
                        ?>
                        <details class="nav-group" <?= $isOpen ? 'open' : '' ?>>
                            <summary class="<?= $isOpen ? 'is-active' : '' ?>">
                                <span class="nav-icon"><?= module_icon($moduleIcon) ?></span> <?= e($moduleTitle) ?>
                            </summary>
                            <div>
                                <a class="<?= is_active($path) ?>" href="<?= e($path) ?>"><?= e($overviewLabel) ?></a>
                                <?php foreach ($children as $child): ?>
                                    <a class="<?= is_active($child['path']) ?>" href="<?= e($child['path']) ?>"><?= e($child['title']) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </details>
                    <?php else: ?>
                        <a class="<?= is_active($path) ?>" href="<?= e($path) ?>">
                            <span class="nav-icon"><?= module_icon($moduleIcon) ?></span> <?= e($moduleTitle) ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        </aside>

        <main class="app-shell">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Academic year <?= e($settings['academic_year'] ?? '2026') ?></p>
                    <h1><?= e($title ?? 'Dashboard') ?></h1>
                </div>
                <div class="topbar-actions">
                    <button class="icon-button" type="button" title="Search"><?= module_icon('search') ?></button>
                    <details class="notification-bell">
                        <summary class="icon-button" title="Notifications">
                            <?= module_icon('bell') ?>
                            <?php if (($notificationUnreadCount ?? 0) > 0): ?><span class="notification-badge"><?= e($notificationUnreadCount) ?></span><?php endif; ?>
                        </summary>
                        <div class="notification-dropdown">
                            <div class="notification-dropdown-head">
                                <strong>Notifications</strong>
                                <a href="/notifications">View all</a>
                            </div>
                            <?php foreach (($recentNotifications ?? []) as $notice): ?>
                                <?php $isUnread = (int) ($notice['effective_is_read'] ?? $notice['is_read'] ?? 0) === 0; ?>
                                <a class="notification-item <?= $isUnread ? 'notification-unread' : '' ?>" href="<?= e($notice['link'] ?: '/notifications') ?>">
                                    <span class="notification-item-icon"><?= e(notification_icon($notice['type'] ?? 'general')) ?></span>
                                    <span><strong><?= e($notice['title']) ?></strong><small><?= e(substr(strip_tags($notice['message']), 0, 90)) ?></small><em><?= e(time_ago($notice['created_at'] ?? '')) ?></em></span>
                                    <b class="<?= e(notification_priority_class($notice['priority'] ?? 'normal')) ?>"><?= e($notice['priority'] ?? 'normal') ?></b>
                                </a>
                            <?php endforeach; ?>
                            <?php if (empty($recentNotifications)): ?><p class="notification-empty">No notifications yet.</p><?php endif; ?>
                            <?php if (!empty($recentNotifications)): ?><form method="post" action="/notifications/read-all"><?= csrf_field() ?><button type="submit">Mark all as read</button></form><?php endif; ?>
                        </div>
                    </details>
                    <?php
                        $role = $user['role'] ?? '';
                        $profilePath = match ($role) {
                            'student' => '/student_portal/profile',
                            default => '/profile',
                        };
                        $mailboxPath = match ($role) {
                            'student' => '/student_portal/messages',
                            'parent' => '/parent_portal',
                            'teacher' => '/teacher/messages',
                            default => '/communication/internal',
                        };
                    ?>
                    <details class="profile-menu">
                        <summary class="profile-chip">
                            <?php if (!empty($profilePhoto)): ?>
                                <img src="<?= e($profilePhoto) ?>" alt="<?= e($user['name']) ?>">
                            <?php else: ?>
                                <span><?= e(strtoupper(substr($user['name'], 0, 2))) ?></span>
                            <?php endif; ?>
                            <strong><?= e($user['name']) ?></strong>
                            <small><?= e(role_name($user['role'])) ?></small>
                        </summary>
                        <div>
                            <a href="<?= e($profilePath) ?>">Profile</a>
                            <a href="/profile/password">Change Password</a>
                            <a href="<?= e($mailboxPath) ?>">Mailbox</a>
                            <a href="/logout">Logout</a>
                        </div>
                    </details>
                </div>
            </header>

            <?= $content ?>
        </main>
    <?php endif; ?>

    <script src="/assets/js/app.js?v=<?= e($appJsVersion) ?>"></script>
    <?php if (!$isGuest): ?><script src="/assets/js/backend-pro.js?v=<?= e($backendJsVersion) ?>"></script><?php endif; ?>
    <script src="/assets/js/pwa.js?v=<?= e($pwaJsVersion) ?>"></script>
</body>
</html>
