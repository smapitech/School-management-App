<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/website', PHP_URL_PATH) ?: '/website';
$schoolName = trim((string) ($websiteSettings['site_title'] ?? '')) ?: 'Smapis Academy';
$brandInitials = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $schoolName), 0, 2)) ?: 'SA';
$logoUrl = !empty($websiteSettings['school_logo']) ? public_upload_url($websiteSettings['school_logo']) : '';
$navItems = [
    '/website' => 'Home',
    '/website/about' => 'About',
    '/website/teachers' => 'Teachers',
    '/website/gallery' => 'Gallery',
    '/website/contact' => 'Contact',
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#184d3b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title><?= e($title ?? $schoolName) ?></title>
    <meta name="description" content="<?= e($websiteSettings['meta_description'] ?? '') ?>">
    <?php if (!empty($websiteSettings['meta_keywords'])): ?><meta name="keywords" content="<?= e($websiteSettings['meta_keywords']) ?>"><?php endif; ?>
    <?php if (!empty($websiteSettings['og_title'])): ?><meta property="og:title" content="<?= e($websiteSettings['og_title']) ?>"><?php endif; ?>
    <?php if (!empty($websiteSettings['og_description'])): ?><meta property="og:description" content="<?= e($websiteSettings['og_description']) ?>"><?php endif; ?>
    <?php if (!empty($websiteSettings['og_image'])): ?><meta property="og:image" content="<?= e(public_upload_url($websiteSettings['og_image'])) ?>"><?php endif; ?>
    <?php if (!empty($websiteSettings['favicon'])): ?><link rel="icon" href="<?= e(public_upload_url($websiteSettings['favicon'])) ?>"><?php endif; ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/pwa/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/school-website-pro.css">
</head>
<body class="website-public website-modern website-pro">
<a class="website-skip-link" href="#main-content">Skip to content</a>
<header class="website-pro-header" data-site-header>
    <div class="website-pro-header-inner">
        <a class="website-pro-brand <?= $logoUrl !== '' ? 'has-full-logo' : 'has-text-brand' ?>" href="/website" aria-label="<?= e($schoolName) ?> home">
            <?php if ($logoUrl !== ''): ?>
                <img class="website-pro-full-logo" src="<?= e($logoUrl) ?>" alt="<?= e($schoolName) ?>">
            <?php else: ?>
                <span class="website-pro-logo-fallback" aria-hidden="true"><?= e($brandInitials) ?></span>
                <span class="website-pro-brand-copy"><strong><?= e($schoolName) ?></strong><small>Learning &bull; Character &bull; Excellence</small></span>
            <?php endif; ?>
        </a>
        <button class="website-pro-menu-button" type="button" aria-expanded="false" aria-controls="website-primary-navigation" data-menu-button>
            <span></span><span></span><span></span><span class="sr-only">Open navigation</span>
        </button>
        <nav id="website-primary-navigation" class="website-pro-nav" aria-label="Primary navigation" data-menu>
            <?php foreach ($navItems as $href => $label): ?>
                <?php $isActive = $currentPath === $href || ($href !== '/website' && str_starts_with($currentPath, $href)); ?>
                <a class="<?= $isActive ? 'is-active' : '' ?>" href="<?= e($href) ?>" <?= $isActive ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
            <?php endforeach; ?>
            <a class="website-pro-portal-link" href="/login">School Portal <span aria-hidden="true">&rarr;</span></a>
        </nav>
    </div>
</header>
