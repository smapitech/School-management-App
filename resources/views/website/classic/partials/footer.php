<footer class="website-public-footer">
    <div>
        <strong><?= e($websiteSettings['site_title'] ?: 'Smapis School Portal') ?></strong>
        <p><?= e($websiteSettings['footer_text'] ?: ($sections['footer_text'] ?? 'Building knowledge, character, and confidence.')) ?></p>
    </div>
    <div>
        <a href="<?= e($sections['facebook_url'] ?? '#') ?>">Facebook</a>
        <a href="<?= e($sections['instagram_url'] ?? '#') ?>">Instagram</a>
        <a href="<?= e($sections['youtube_url'] ?? '#') ?>">YouTube</a>
    </div>
</footer>
<script src="/assets/js/pwa.js" defer></script>
</body>
</html>
