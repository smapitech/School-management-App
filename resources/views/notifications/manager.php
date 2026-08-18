<section class="module-hero">
    <div><p class="eyebrow">Notifications</p><h2>Notification Manager</h2><p>Send role, user, and global school notifications.</p></div>
    <form method="post" action="/notification-manager/cleanup" onsubmit="return confirm('Delete expired notifications?');"><?= csrf_field() ?><button class="secondary-action" type="submit">Cleanup Expired</button></form>
</section>

<section class="panel notification-manager">
    <div class="panel-header"><div><p class="eyebrow">Send</p><h3>Create Notification</h3></div></div>
    <form class="form-grid" method="post" action="/notification-manager/send">
        <?= csrf_field() ?>
        <label><span>Target Type</span><select name="target_type"><option value="role">Role</option><option value="user">Specific User ID</option><option value="global">Global</option></select></label>
        <label><span>User ID</span><input type="number" name="user_id" min="1" placeholder="For specific user"></label>
        <label><span>Role</span><select name="role"><?php foreach ($roles as $role): ?><option value="<?= e($role) ?>"><?= e(role_name($role)) ?></option><?php endforeach; ?></select></label>
        <label><span>Type</span><select name="type"><?php foreach ($types as $type): ?><option value="<?= e($type) ?>"><?= e(notification_type_label($type)) ?></option><?php endforeach; ?></select></label>
        <label><span>Priority</span><select name="priority"><?php foreach ($priorities as $priority): ?><option value="<?= e($priority) ?>"><?= e(ucfirst($priority)) ?></option><?php endforeach; ?></select></label>
        <label><span>Link</span><input name="link" placeholder="/homework"></label>
        <label><span>Expires At</span><input type="datetime-local" name="expires_at"></label>
        <label class="form-wide"><span>Title</span><input name="title" required maxlength="255"></label>
        <?php $label = 'Message'; $fieldName = 'message'; $defaultMessage = ''; $required = true; require dirname(__DIR__) . '/communication/editor.php'; ?>
        <button type="submit">Send Notification</button>
    </form>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">History</p><h3>Notification History</h3></div></div>
    <form class="filter-form" method="get" action="/notification-manager">
        <label><span>Role</span><select name="role"><option value="">All</option><?php foreach ($roles as $role): ?><option value="<?= e($role) ?>" <?= ($filters['role'] ?? '') === $role ? 'selected' : '' ?>><?= e(role_name($role)) ?></option><?php endforeach; ?></select></label>
        <label><span>Type</span><select name="type"><option value="">All</option><?php foreach ($types as $type): ?><option value="<?= e($type) ?>" <?= ($filters['type'] ?? '') === $type ? 'selected' : '' ?>><?= e(notification_type_label($type)) ?></option><?php endforeach; ?></select></label>
        <label><span>Priority</span><select name="priority"><option value="">All</option><?php foreach ($priorities as $priority): ?><option value="<?= e($priority) ?>" <?= ($filters['priority'] ?? '') === $priority ? 'selected' : '' ?>><?= e(ucfirst($priority)) ?></option><?php endforeach; ?></select></label>
        <label><span>Status</span><select name="status"><option value="">All</option><option value="unread" <?= ($filters['status'] ?? '') === 'unread' ? 'selected' : '' ?>>Unread</option><option value="read" <?= ($filters['status'] ?? '') === 'read' ? 'selected' : '' ?>>Read</option></select></label>
        <label><span>Date</span><input type="date" name="date" value="<?= e($filters['date'] ?? '') ?>"></label>
        <button type="submit">Filter</button>
    </form>
    <div class="table-wrap"><table><thead><tr><th>Title</th><th>Target</th><th>Type</th><th>Priority</th><th>Created</th><th>Action</th></tr></thead><tbody>
        <?php foreach ($notifications as $notification): ?><tr><td><?= e($notification['title']) ?><br><small><?= e(substr(strip_tags($notification['message']), 0, 90)) ?></small></td><td><?= $notification['user_id'] ? 'User #' . e($notification['user_id']) : e($notification['role'] ?: 'Global') ?></td><td><?= e(notification_type_label($notification['type'])) ?></td><td><span class="<?= e(notification_priority_class($notification['priority'])) ?>"><?= e(ucfirst($notification['priority'])) ?></span></td><td><?= e($notification['created_at']) ?></td><td><form method="post" action="/notification-manager/delete" onsubmit="return confirm('Delete this notification globally?');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($notification['id']) ?>"><button type="submit">Delete</button></form></td></tr><?php endforeach; ?>
        <?php if (empty($notifications)): ?><tr><td colspan="6">No notifications found.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
