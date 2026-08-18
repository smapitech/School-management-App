<section class="module-hero"><div><p class="eyebrow">Mailbox</p><h2>Message</h2><p>Student communication inbox for notices, reminders, invoices, and school messages.</p></div></section>
<?php require __DIR__ . '/nav.php'; ?>
<?php if ($student): ?>
<section class="mailbox-layout">
    <aside class="mailbox-folders">
        <a class="is-active" href="/student_portal/messages">Inbox</a>
        <a href="/student_portal/messages">Notices</a>
        <a href="/student_portal/messages">Invoices</a>
        <a href="/student_portal/messages">Reminders</a>
    </aside>
    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">Inbox</p><h3>Messages</h3></div></div>
        <div class="message-list">
            <?php foreach ($messages as $message): ?><article><span><?= e($message['channel']) ?> | <?= e($message['created_at']) ?></span><strong><?= e($message['subject']) ?></strong><p><?= e(substr(strip_tags($message['message']), 0, 220)) ?></p><?php if (!empty($message['attachment_path'])): ?><a href="<?= e($message['attachment_path']) ?>">Attachment: <?= e($message['attachment_name']) ?></a><?php endif; ?></article><?php endforeach; ?>
            <?php if (empty($messages)): ?><p class="muted">No message is available yet.</p><?php endif; ?>
        </div>
    </section>
</section>
<?php endif; ?>
