<?php require __DIR__ . '/partials/header.php'; ?>
<main>
    <section class="website-public-section">
        <h1><?= e($sections['gallery_title'] ?? 'Students Gallery') ?></h1>
        <div class="website-gallery-groups">
            <?php foreach ($galleryGroups as $group): ?>
                <section class="website-gallery-group">
                    <div class="website-gallery-group-header">
                        <div>
                            <p class="eyebrow">Category</p>
                            <h2><?= e($group['category']) ?></h2>
                        </div>
                        <small><?= count($group['items']) ?> photo<?= count($group['items']) === 1 ? '' : 's' ?></small>
                    </div>
                    <div class="website-gallery-grid">
                        <?php foreach ($group['items'] as $item): ?>
                            <article>
                                <img src="<?= e(public_upload_url($item['image_path'])) ?>" alt="<?= e($item['title'] ?: $group['category']) ?>">
                                <?php if (!empty($item['title'])): ?><strong><?= e($item['title']) ?></strong><?php endif; ?>
                                <?php if (!empty($item['description'])): ?><p><?= e($item['description']) ?></p><?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
            <?php if (empty($galleryGroups)): ?><p>No gallery images have been uploaded yet.</p><?php endif; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
