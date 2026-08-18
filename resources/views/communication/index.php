<section class="module-hero">
    <div>
        <p class="eyebrow">School communication</p>
        <h2>Communication</h2>
        <p>Manage internal messages, invoice notifications, parent reminders, and delivery setup for Email, SMS, and WhatsApp.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="stat-grid">
    <article class="stat-card"><span>messages</span><strong><?= e(count($messages)) ?></strong><p>recent queued communication</p></article>
    <article class="stat-card"><span>email</span><strong><?= !empty($settings['smtp_host']) ? 'Ready' : 'Setup' ?></strong><p>SMTP configuration</p></article>
    <article class="stat-card"><span>sms api</span><strong><?= !empty($settings['sms_api_url']) ? 'Ready' : 'Setup' ?></strong><p>provider endpoint</p></article>
    <article class="stat-card"><span>whatsapp</span><strong><?= !empty($settings['whatsapp_api_url']) ? 'Ready' : 'Setup' ?></strong><p>provider endpoint</p></article>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Log</p><h3>Recent Communication</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Type</th><th>Channel</th><th>Audience</th><th>Subject</th><th>Message</th><th>Attachment</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= e($message['message_type']) ?></td>
                        <td><?= e($message['channel']) ?></td>
                        <td><?= e($message['audience']) ?></td>
                        <td><?= e($message['subject']) ?></td>
                        <td><?= e(substr(strip_tags($message['message']), 0, 90)) ?></td>
                        <td><?php if (!empty($message['attachment_path'])): ?><a href="<?= e($message['attachment_path']) ?>" target="_blank"><?= e($message['attachment_name'] ?: 'Attachment') ?></a><?php else: ?>-<?php endif; ?></td>
                        <td><span class="status"><?= e($message['status']) ?></span></td>
                        <td><?= e($message['created_at']) ?></td>
                        <td class="row-actions">
                            <?php if (can('communication', 'create')): ?>
                                <form method="post" action="/communication/messages/delete">
                                    <input type="hidden" name="id" value="<?= e($message['id']) ?>">
                                    <input type="hidden" name="return_to" value="/communication">
                                    <button type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($messages)): ?><tr><td colspan="9">No communication has been queued yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
