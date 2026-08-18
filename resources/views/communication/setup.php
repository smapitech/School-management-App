<section class="module-hero">
    <div>
        <p class="eyebrow">API setup</p>
        <h2>Communication API Setup</h2>
        <p>These values are saved in Global Settings and will be used when Email, SMS, and WhatsApp providers are connected.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Current setup</p><h3>Provider Status</h3></div></div>
    <div class="stat-grid">
        <article class="stat-card"><span>Email</span><strong><?= !empty($settings['smtp_host']) ? 'Ready' : 'Not Set' ?></strong><p><?= e($settings['smtp_host'] ?? 'SMTP host required') ?></p></article>
        <article class="stat-card"><span>SMS</span><strong><?= !empty($settings['sms_api_url']) ? 'Ready' : 'Not Set' ?></strong><p><?= e($settings['sms_sender_id'] ?? 'sender id required') ?></p></article>
        <article class="stat-card"><span>WhatsApp</span><strong><?= !empty($settings['whatsapp_api_url']) ? 'Ready' : 'Not Set' ?></strong><p><?= e($settings['whatsapp_phone_number_id'] ?? 'phone number id required') ?></p></article>
        <article class="stat-card"><span>Manage</span><strong>Settings</strong><p><a href="/settings">Open Global Settings</a></p></article>
    </div>
</section>
