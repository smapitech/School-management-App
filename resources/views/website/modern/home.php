<?php require __DIR__ . '/partials/header.php'; ?>
<?php
$heroImage = public_upload_url($sections['hero_image'] ?? '');
$aboutImage = public_upload_url($sections['about_image'] ?? '');
$features = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($sections['why_choose_points'] ?? '')))));
$programs = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($sections['programs_content'] ?? '')))));
$heroLink = trim((string) ($sections['hero_button_link'] ?? '')) ?: '/website/contact';
$heroButton = trim((string) ($sections['hero_button_text'] ?? '')) ?: 'Discover our school';
?>
<main id="main-content">
<?php
$slides = [
    [
        'image' => public_upload_url($sections['hero_image'] ?? ''),
        'title' => trim((string) ($sections['hero_title'] ?? '')) ?: 'Building confident learners for a changing world',
        'subtitle' => trim((string) ($sections['hero_subtitle'] ?? '')) ?: 'A warm, purposeful school community where excellent teaching and strong values help every child flourish.',
        'button_text' => trim((string) ($sections['hero_button_text'] ?? '')) ?: 'Discover our school',
        'button_link' => trim((string) ($sections['hero_button_link'] ?? '')) ?: '/website/contact',
    ],
    [
        'image' => public_upload_url($sections['hero_slide_2_image'] ?? ''),
        'title' => trim((string) ($sections['hero_slide_2_title'] ?? '')),
        'subtitle' => trim((string) ($sections['hero_slide_2_subtitle'] ?? '')),
        'button_text' => trim((string) ($sections['hero_slide_2_button_text'] ?? '')),
        'button_link' => trim((string) ($sections['hero_slide_2_button_link'] ?? '')),
    ],
    [
        'image' => public_upload_url($sections['hero_slide_3_image'] ?? ''),
        'title' => trim((string) ($sections['hero_slide_3_title'] ?? '')),
        'subtitle' => trim((string) ($sections['hero_slide_3_subtitle'] ?? '')),
        'button_text' => trim((string) ($sections['hero_slide_3_button_text'] ?? '')),
        'button_link' => trim((string) ($sections['hero_slide_3_button_link'] ?? '')),
    ],
];
$slides = array_values(array_filter($slides, static fn (array $slide): bool => $slide['image'] !== '' || $slide['title'] !== '' || $slide['subtitle'] !== ''));
?>
<section class="website-pro-hero-slider" data-hero-slider aria-label="School highlights">
    <div class="website-pro-slides">
        <?php foreach ($slides as $index => $slide): ?>
            <article class="website-pro-slide <?= $index === 0 ? 'is-active' : '' ?>" data-slide aria-hidden="<?= $index === 0 ? 'false' : 'true' ?>">
                <?php if ($slide['image'] !== ''): ?>
                    <img src="<?= e($slide['image']) ?>" alt="" <?= $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                <?php else: ?>
                    <div class="website-pro-slide-placeholder" aria-hidden="true"></div>
                <?php endif; ?>
                <div class="website-pro-slide-overlay"></div>
                <div class="website-pro-slide-content">
                    <span class="website-pro-eyebrow">A place to learn, belong and thrive</span>
                    <h1><?= e($slide['title']) ?></h1>
                    <?php if ($slide['subtitle'] !== ''): ?><p><?= e($slide['subtitle']) ?></p><?php endif; ?>
                    <div class="website-pro-hero-actions">
                        <?php if ($slide['button_text'] !== ''): ?><a class="website-pro-button website-pro-button-primary" href="<?= e($slide['button_link'] ?: '/website/contact') ?>"><?= e($slide['button_text']) ?> <span aria-hidden="true">→</span></a><?php endif; ?>
                        <a class="website-pro-button website-pro-button-glass" href="/website/about">Learn about us</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if (count($slides) > 1): ?>
        <button class="website-pro-slider-arrow is-prev" type="button" data-slider-prev aria-label="Previous slide">‹</button>
        <button class="website-pro-slider-arrow is-next" type="button" data-slider-next aria-label="Next slide">›</button>
        <div class="website-pro-slider-dots" role="tablist" aria-label="Choose slide">
            <?php foreach ($slides as $index => $_slide): ?><button type="button" class="<?= $index === 0 ? 'is-active' : '' ?>" data-slider-dot="<?= $index ?>" aria-label="Show slide <?= $index + 1 ?>" aria-selected="<?= $index === 0 ? 'true' : 'false' ?>"></button><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="website-pro-hero-trust"><span>Safe learning environment</span><span>Qualified educators</span><span>Whole-child development</span></div>
</section>
    <section class="website-pro-about">
        <div class="website-pro-section-copy">
            <span class="website-pro-eyebrow">Welcome to our community</span>
            <h2><?= e($sections['about_title'] ?? 'Education that prepares children for life') ?></h2>
            <p><?= nl2br(e($sections['about_content'] ?? 'Update your school story from the Website Manager to help families understand what makes your school special.')) ?></p>
            <a class="website-pro-text-link" href="/website/about">Read our school story <span aria-hidden="true">→</span></a>
        </div>
        <div class="website-pro-about-media">
            <?php if ($aboutImage): ?><img src="<?= e($aboutImage) ?>" alt="About <?= e($websiteSettings['site_title'] ?: 'our school') ?>" loading="lazy"><?php else: ?><div class="website-pro-media-placeholder">A nurturing environment for growth</div><?php endif; ?>
            <div class="website-pro-value-note"><span>Our promise</span><strong>Strong foundations. Kind hearts. Curious minds.</strong></div>
        </div>
    </section>

    <section class="website-pro-purpose">
        <article><span>01</span><div><h2><?= e($sections['mission_title'] ?? 'Our Mission') ?></h2><p><?= e($sections['mission_content'] ?? 'To provide meaningful learning experiences that develop knowledge, character and confidence.') ?></p></div></article>
        <article><span>02</span><div><h2><?= e($sections['vision_title'] ?? 'Our Vision') ?></h2><p><?= e($sections['vision_content'] ?? 'To raise responsible, creative and compassionate learners prepared to make a positive difference.') ?></p></div></article>
    </section>

    <?php if ($features): ?>
    <section class="website-pro-features">
        <header class="website-pro-section-heading"><span class="website-pro-eyebrow">Why families choose us</span><h2><?= e($sections['why_choose_title'] ?? 'A better environment for every learner') ?></h2></header>
        <div class="website-pro-feature-grid">
            <?php foreach ($features as $index => $point): ?><article><span><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span><h3><?= e($point) ?></h3><p>Thoughtfully delivered as part of our commitment to excellent, child-centred education.</p></article><?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($programs): ?>
    <section class="website-pro-programs">
        <header><span class="website-pro-eyebrow">Learning pathways</span><h2><?= e($sections['programs_title'] ?? 'Programmes designed for every stage') ?></h2></header>
        <div><?php foreach ($programs as $program): ?><article><span aria-hidden="true">✦</span><strong><?= e($program) ?></strong></article><?php endforeach; ?></div>
    </section>
    <?php endif; ?>

    <?php if (!empty($teachers)): ?>
    <section class="website-pro-showcase">
        <header class="website-pro-section-heading"><span class="website-pro-eyebrow">Meet the people who inspire learning</span><h2><?= e($sections['teachers_title'] ?? 'Dedicated teachers, meaningful relationships') ?></h2><a class="website-pro-text-link" href="/website/teachers">Meet our team →</a></header>
        <div class="website-pro-teacher-preview">
            <?php foreach (array_slice($teachers, 0, 4) as $teacher): ?><article><?php if (!empty($teacher['photo_path'])): ?><img src="<?= e(public_upload_url($teacher['photo_path'])) ?>" alt="<?= e($teacher['full_name']) ?>" loading="lazy"><?php else: ?><span class="website-pro-person-placeholder"><?= e(strtoupper(substr($teacher['full_name'], 0, 2))) ?></span><?php endif; ?><h3><?= e($teacher['full_name']) ?></h3><p><?= e($teacher['role_title']) ?><?= $teacher['subject'] ? ' · ' . e($teacher['subject']) : '' ?></p></article><?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($gallery)): ?>
    <section class="website-pro-gallery-preview">
        <header class="website-pro-section-heading"><span class="website-pro-eyebrow">Life at our school</span><h2><?= e($sections['gallery_title'] ?? 'Learning, friendship and memorable moments') ?></h2><a class="website-pro-text-link" href="/website/gallery">View full gallery →</a></header>
        <div class="website-pro-gallery-mosaic"><?php foreach (array_slice($gallery, 0, 5) as $index => $item): ?><figure class="<?= $index === 0 ? 'is-featured' : '' ?>"><img src="<?= e(public_upload_url($item['image_path'])) ?>" alt="<?= e($item['title'] ?: 'School activity') ?>" loading="lazy"><?php if (!empty($item['title'])): ?><figcaption><?= e($item['title']) ?></figcaption><?php endif; ?></figure><?php endforeach; ?></div>
    </section>
    <?php endif; ?>

    <?php if (!empty($reviews)): ?>
    <?php
    $landingReviews = array_slice($reviews, 0, 5);
    $featuredReview = $landingReviews[0];
    $supportingReviews = array_slice($landingReviews, 1);
    $featuredImage = public_upload_url($featuredReview['parent_image'] ?? '');
    $featuredRating = max(1, min(5, (int) ($featuredReview['rating'] ?? 5)));
    ?>
    <section class="website-pro-parent-stories" aria-labelledby="parent-stories-title">
        <div class="website-pro-parent-stories-intro">
            <div>
                <span class="website-pro-eyebrow">What parents say</span>
                <h2 id="parent-stories-title">Trusted by families who know us best</h2>
            </div>
            <p>Real experiences from parents who have chosen our school community for their children.</p>
        </div>

        <div class="website-pro-parent-stories-layout">
            <article class="website-pro-featured-review">
                <span class="website-pro-review-quote" aria-hidden="true">“</span>
                <div class="website-pro-review-stars" aria-label="<?= $featuredRating ?> out of 5 stars">
                    <?= str_repeat('★', $featuredRating) ?>
                </div>
                <blockquote><?= e($featuredReview['review_text'] ?? '') ?></blockquote>
                <footer>
                    <?php if ($featuredImage !== ''): ?>
                        <img src="<?= e($featuredImage) ?>" alt="<?= e($featuredReview['parent_name'] ?? 'Parent reviewer') ?>" loading="lazy">
                    <?php else: ?>
                        <span class="website-pro-review-avatar" aria-hidden="true"><?= e(strtoupper(substr((string) ($featuredReview['parent_name'] ?? 'Parent'), 0, 2))) ?></span>
                    <?php endif; ?>
                    <div>
                        <strong><?= e($featuredReview['parent_name'] ?? 'Parent') ?></strong>
                        <?php if (!empty($featuredReview['parent_title'])): ?><span><?= e($featuredReview['parent_title']) ?></span><?php endif; ?>
                    </div>
                </footer>
            </article>

            <?php if (!empty($supportingReviews)): ?>
            <div class="website-pro-review-list">
                <?php foreach ($supportingReviews as $review): ?>
                    <?php
                    $reviewImage = public_upload_url($review['parent_image'] ?? '');
                    $reviewRating = max(1, min(5, (int) ($review['rating'] ?? 5)));
                    ?>
                    <article class="website-pro-review-card">
                        <div class="website-pro-review-card-top">
                            <div class="website-pro-review-author">
                                <?php if ($reviewImage !== ''): ?>
                                    <img src="<?= e($reviewImage) ?>" alt="<?= e($review['parent_name'] ?? 'Parent reviewer') ?>" loading="lazy">
                                <?php else: ?>
                                    <span class="website-pro-review-avatar" aria-hidden="true"><?= e(strtoupper(substr((string) ($review['parent_name'] ?? 'Parent'), 0, 2))) ?></span>
                                <?php endif; ?>
                                <div>
                                    <strong><?= e($review['parent_name'] ?? 'Parent') ?></strong>
                                    <?php if (!empty($review['parent_title'])): ?><span><?= e($review['parent_title']) ?></span><?php endif; ?>
                                </div>
                            </div>
                            <div class="website-pro-review-stars is-compact" aria-label="<?= $reviewRating ?> out of 5 stars"><?= str_repeat('★', $reviewRating) ?></div>
                        </div>
                        <blockquote><?= e($review['review_text'] ?? '') ?></blockquote>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="website-pro-parent-stories-note">
            <span aria-hidden="true">✓</span>
            <p>Only approved parent reviews are published on this website.</p>
        </div>
    </section>
    <?php endif; ?>

    <section class="website-pro-final-cta"><div><span class="website-pro-eyebrow">Start a conversation</span><h2>Discover whether our school is the right place for your child.</h2><p>Our team will gladly answer your questions and guide you through the next steps.</p></div><a class="website-pro-button website-pro-button-light" href="/website/contact">Contact the school →</a></section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
