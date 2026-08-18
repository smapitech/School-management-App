<?php
$schoolName = trim((string) ($websiteSettings['site_title'] ?? '')) ?: 'Smapis Academy';
$phone = trim((string) ($sections['contact_phone'] ?? ''));
$email = trim((string) ($sections['contact_email'] ?? ''));
$address = trim((string) ($sections['contact_address'] ?? ''));
$socialLinks = [
    'Facebook' => trim((string) ($sections['facebook_url'] ?? '')),
    'Instagram' => trim((string) ($sections['instagram_url'] ?? '')),
    'YouTube' => trim((string) ($sections['youtube_url'] ?? '')),
    'TikTok' => trim((string) ($sections['tiktok_url'] ?? '')),
    'LinkedIn' => trim((string) ($sections['linkedin_url'] ?? '')),
];
?>
<footer class="website-pro-footer">
    <div class="website-pro-footer-grid">
        <section>
            <span class="website-pro-footer-kicker">Our school community</span>
            <h2><?= e($schoolName) ?></h2>
            <p><?= e($websiteSettings['footer_text'] ?: ($sections['footer_text'] ?? 'A caring learning community where every child is encouraged to grow with confidence and character.')) ?></p>
        </section>
        <section>
            <h3>Explore</h3>
            <a href="/website/about">About our school</a>
            <a href="/website/teachers">Meet our teachers</a>
            <a href="/website/gallery">School gallery</a>
            <a href="/website/contact">Contact us</a>
            <a href="/login">School portal</a>
        </section>
        <section>
            <h3>Contact</h3>
            <?php if ($address !== ''): ?><p><?= e($address) ?></p><?php endif; ?>
            <?php if ($phone !== ''): ?><a href="tel:<?= e(preg_replace('/\s+/', '', $phone)) ?>"><?= e($phone) ?></a><?php endif; ?>
            <?php if ($email !== ''): ?><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a><?php endif; ?>
            <?php if (!empty($sections['opening_hours'])): ?><p><?= e($sections['opening_hours']) ?></p><?php endif; ?>
        </section>
        <section>
            <h3>Connect</h3>
            <div class="website-pro-socials">
                <?php foreach ($socialLinks as $label => $url): ?>
                    <?php if ($url !== ''): ?><a href="<?= e($url) ?>" target="_blank" rel="noopener noreferrer"><?= e($label) ?></a><?php endif; ?>
                <?php endforeach; ?>
                <?php if (!array_filter($socialLinks)): ?><p>Follow our school updates and activities.</p><?php endif; ?>
            </div>
        </section>
    </div>
    <div class="website-pro-footer-bottom">
        <span>&copy; <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved.</span>
        <span>Powered by Smapis Technologies</span>
    </div>
</footer>
<script src="/assets/js/school-website-pro.js" defer></script>
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>
