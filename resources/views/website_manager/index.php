<section class="website-pro-manager-hero">
    <div><span class="website-pro-manager-kicker">Public Website</span><h2>Build a school website families can trust.</h2><p>Manage your public identity, content, staff profiles, gallery and enquiries from one focused workspace.</p></div>
    <div class="website-pro-manager-actions"><a class="secondary-action" href="/website" target="_blank" rel="noopener">View Live Website</a><a href="/website-manager/edit">Edit Website Content</a></div>
</section>
<?php require __DIR__ . '/nav.php'; ?>
<?php if (!empty($success)): ?><section class="alert-success" role="status"><?= e($success) ?></section><?php endif; ?>
<section class="website-pro-manager-summary">
    <article><span>Active design</span><strong><?= e($templates[$websiteSettings['active_template']]['name'] ?? ucfirst($websiteSettings['active_template'] ?? 'classic')) ?></strong><small>Current public theme</small></article>
    <article><span>Gallery</span><strong><?= e($galleryCount) ?></strong><small>Uploaded school photos</small></article>
    <article><span>Teachers</span><strong><?= e($teacherCount) ?></strong><small>Staff profiles available</small></article>
    <article><span>Parent reviews</span><strong><?= e($reviewCount) ?></strong><small>Testimonials recorded</small></article>
    <article><span>Messages</span><strong><?= e($messageCount) ?></strong><small>Website enquiries</small></article>
</section>
<section class="website-pro-manager-layout">
    <div class="website-pro-manager-main">
        <header><div><span class="website-pro-manager-kicker">Appearance</span><h3>Choose the right public experience</h3></div><p>Preview a design before activating it. Your saved content and media remain unchanged.</p></header>
        <div class="website-pro-theme-grid">
            <?php foreach ($templates as $key => $template): ?>
                <?php $active = ($websiteSettings['active_template'] ?? '') === $key; ?>
                <article class="website-pro-theme-card <?= $active ? 'is-active' : '' ?> <?= $key === 'modern' ? 'is-horizon' : 'is-heritage' ?>">
                    <div class="website-pro-theme-preview"><span></span><span></span><span></span><strong><?= e($key === 'modern' ? 'Horizon' : 'Heritage') ?></strong></div>
                    <div class="website-pro-theme-card-body"><div><span class="website-pro-manager-kicker"><?= $active ? 'Active theme' : 'Available theme' ?></span><h4><?= e($template['name']) ?></h4><p><?= e($template['description']) ?></p></div><div class="website-pro-theme-actions"><a href="/website/preview?template=<?= e($key) ?>" target="_blank" rel="noopener">Preview</a><?php if (!$active): ?><form method="post" action="/website-manager/template"><?= csrf_field() ?><input type="hidden" name="active_template" value="<?= e($key) ?>"><button type="submit">Activate theme</button></form><?php else: ?><span class="website-pro-active-badge">Currently active</span><?php endif; ?></div></div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <aside class="website-pro-manager-side">
        <span class="website-pro-manager-kicker">Quick actions</span><h3>Keep your website current</h3>
        <a href="/website-manager/edit"><strong>Edit content and branding</strong><span>Hero text, school story, contact details and SEO.</span></a>
        <a href="/website-manager/gallery"><strong>Add school photos</strong><span>Show learning, events, achievements and school life.</span></a>
        <a href="/website-manager/teachers"><strong>Update teacher profiles</strong><span>Introduce the educators behind your school.</span></a>
        <a href="/website-manager/reviews"><strong>Manage parent reviews</strong><span>Publish authentic feedback from families.</span></a>
        <a href="/website-manager/contact-messages"><strong>Read website enquiries</strong><span>Follow up with prospective families quickly.</span></a>
    </aside>
</section>
