<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#184d3b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title><?= e($websiteSettings['site_title'] ?: 'Smapis School Portal') ?></title>
    <meta name="description" content="<?= e($websiteSettings['meta_description'] ?? '') ?>">
    <?php if (!empty($websiteSettings['favicon'])): ?><link rel="icon" href="<?= e(public_upload_url($websiteSettings['favicon'])) ?>"><?php endif; ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/pwa/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="website-public website-classic">
<div class="website-classic-topbar"><span><?= e($sections['contact_phone'] ?? '') ?></span><span><?= e($sections['contact_email'] ?? '') ?></span><span><?= e($sections['opening_hours'] ?? '') ?></span></div>
<header class="website-public-header website-classic-header">
    <a class="website-public-brand" href="/website">
        <?php if (!empty($websiteSettings['school_logo'])): ?>
            <img src="<?= e(public_upload_url($websiteSettings['school_logo'])) ?>" alt="<?= e($websiteSettings['site_title'] ?: 'School') ?> logo">
        <?php else: ?>
            <span class="website-public-logo-fallback">SP</span>
        <?php endif; ?>
        <span><?= e($websiteSettings['site_title'] ?: 'Smapis School Portal') ?></span>
    </a>
    <nav>
        <a href="/website">Home</a>
        <a href="/website/about">About</a>
        <a href="/website/gallery">Students Gallery</a>
        <a href="/website/teachers">Teachers</a>
        <a href="/website/contact">Contact Us</a>
        <a class="website-public-login" href="/login">Login</a>
    </nav>
</header>
