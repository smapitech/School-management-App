<?php require __DIR__ . '/partials/header.php'; ?>
<?php $aboutImage = public_upload_url($sections['about_image'] ?? ''); ?>
<main id="main-content">
    <section class="website-pro-page-hero"><span class="website-pro-eyebrow">Our school story</span><h1><?= e($sections['about_title'] ?? 'About Our School') ?></h1><p>Learn about the values, purpose and people that shape our learning community.</p></section>
    <section class="website-pro-about-page">
        <article><span class="website-pro-eyebrow">Who we are</span><h2>A community built around every child</h2><p><?= nl2br(e($sections['about_content'] ?? 'Please update this section from the Website Manager.')) ?></p></article>
        <div><?php if ($aboutImage): ?><img src="<?= e($aboutImage) ?>" alt="About <?= e($websiteSettings['site_title'] ?: 'our school') ?>"><?php else: ?><div class="website-pro-media-placeholder">Purposeful learning in a caring community</div><?php endif; ?></div>
    </section>
    <section class="website-pro-purpose website-pro-purpose-page"><article><span>01</span><div><h2><?= e($sections['mission_title'] ?? 'Our Mission') ?></h2><p><?= e($sections['mission_content'] ?? '') ?></p></div></article><article><span>02</span><div><h2><?= e($sections['vision_title'] ?? 'Our Vision') ?></h2><p><?= e($sections['vision_content'] ?? '') ?></p></div></article></section>
    <section class="website-pro-final-cta"><div><span class="website-pro-eyebrow">Visit our community</span><h2>Come and experience our school for yourself.</h2><p>Speak with our team to learn more about admissions and school life.</p></div><a class="website-pro-button website-pro-button-light" href="/website/contact">Arrange an enquiry →</a></section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
