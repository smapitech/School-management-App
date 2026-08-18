<section class="module-hero">
    <div><p class="eyebrow">Notifications</p><h2>My Notifications</h2><p>School updates, reminders, alerts, and important notices for your account.</p></div>
    <form method="post" action="/notifications/read-all"><?= csrf_field() ?><button class="secondary-action" type="submit">Mark All as Read</button></form>
</section>

<nav class="subnav">
    <a class="<?= $status === '' ? 'is-active' : '' ?>" href="/notifications">All</a>
    <a class="<?= $status === 'unread' ? 'is-active' : '' ?>" href="/notifications?status=unread">Unread</a>
    <a class="<?= $status === 'read' ? 'is-active' : '' ?>" href="/notifications?status=read">Read</a>
</nav>

<section class="notification-list">
    <?php foreach ($notifications as $notification): ?>
        <?php $isUnread = (int) ($notification['effective_is_read'] ?? $notification['is_read'] ?? 0) === 0; ?>
        <article class="notification-card <?= $isUnread ? 'notification-unread' : '' ?>">
            <div class="notification-card-icon"><?= e(notification_icon($notification['type'] ?? 'general')) ?></div>
            <div>
                <div class="notification-card-head">
                    <h3><?= e($notification['title']) ?></h3>
                    <span class="<?= e(notification_priority_class($notification['priority'] ?? 'normal')) ?>"><?= e(ucfirst($notification['priority'] ?? 'normal')) ?></span>
                </div>
                <div class="notification-message rich-content"><?= rich_text($notification['message']) ?></div>
                <small><?= e(notification_type_label($notification['type'] ?? 'general')) ?> | <?= e(time_ago($notification['created_at'] ?? '')) ?></small>
                <div class="row-actions">
                    <?php if (!empty($notification['link'])): ?><a href="<?= e($notification['link']) ?>">Open Link</a><?php endif; ?>
                    <?php if ($isUnread): ?><form method="post" action="/notifications/read"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($notification['id']) ?>"><button type="submit">Mark Read</button></form><?php endif; ?>
                    <form method="post" action="/notifications/delete" onsubmit="return confirm('Delete this notification?');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($notification['id']) ?>"><button type="submit">Delete</button></form>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (empty($notifications)): ?><section class="panel empty-state"><h3>You do not have any notifications yet.</h3><p>Important school updates will appear here.</p></section><?php endif; ?>
</section>
