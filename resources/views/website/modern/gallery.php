<?php require __DIR__ . '/partials/header.php'; ?>
<main id="main-content">
    <section class="website-pro-page-hero"><span class="website-pro-eyebrow">School life</span><h1><?= e($sections['gallery_title'] ?? 'Our Gallery') ?></h1><p>A glimpse into learning, creativity, friendship and memorable school moments.</p></section>
    <section class="website-pro-gallery-page">
        <?php foreach ($galleryGroups as $group): ?><section class="website-pro-gallery-group"><header><div><span class="website-pro-eyebrow">Collection</span><h2><?= e($group['category']) ?></h2></div><small><?= count($group['items']) ?> photo<?= count($group['items']) === 1 ? '' : 's' ?></small></header><div class="website-pro-gallery-grid"><?php foreach ($group['items'] as $item): ?><figure><img src="<?= e(public_upload_url($item['image_path'])) ?>" alt="<?= e($item['title'] ?: $group['category']) ?>" loading="lazy"><?php if (!empty($item['title']) || !empty($item['description'])): ?><figcaption><?php if (!empty($item['title'])): ?><strong><?= e($item['title']) ?></strong><?php endif; ?><?php if (!empty($item['description'])): ?><span><?= e($item['description']) ?></span><?php endif; ?></figcaption><?php endif; ?></figure><?php endforeach; ?></div></section><?php endforeach; ?>
        <?php if (empty($galleryGroups)): ?><div class="website-pro-empty"><h2>Our gallery is being prepared.</h2><p>Photos from school activities will appear here soon.</p></div><?php endif; ?>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
