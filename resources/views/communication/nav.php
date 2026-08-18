<section class="role-tabs">
    <?php $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'; ?>
    <?php if (($user['role'] ?? '') === 'teacher'): ?>
        <a class="<?= in_array($currentPath, ['/teacher/messages', '/teacher/messages/view'], true) ? 'is-active' : '' ?>" href="/teacher/messages">Conversations</a>
        <a class="<?= $currentPath === '/teacher/messages/parents' ? 'is-active' : '' ?>" href="/teacher/messages/parents">Parent Messages</a>
        <a class="<?= $currentPath === '/teacher/messages/teachers' ? 'is-active' : '' ?>" href="/teacher/messages/teachers">Teacher Messages</a>
    <?php else: ?>
        <a class="<?= is_active('/communication') ?>" href="/communication">Overview</a>
        <a class="<?= is_active('/communication/internal') ?>" href="/communication/internal">Internal Messaging</a>
        <a class="<?= is_active('/communication/invoices') ?>" href="/communication/invoices">Invoice Messages</a>
        <a class="<?= is_active('/communication/reminders') ?>" href="/communication/reminders">Reminder Messages</a>
        <a class="<?= is_active('/communication/setup') ?>" href="/communication/setup">API Setup</a>
    <?php endif; ?>
</section>
