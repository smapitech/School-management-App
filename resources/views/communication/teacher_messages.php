<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2>Teacher Messages</h2>
        <p>Track conversations with only your assigned parents and fellow teachers.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/teacher/messages/parents">Parent Messages</a>
        <a class="primary-action" href="/teacher/messages/teachers">Teacher Messages</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (!empty($success)): ?>
    <section class="alert-success"><p><?= e($success) ?></p></section>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <section class="alert-error"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></section>
<?php endif; ?>

<section class="stat-grid">
    <article class="stat-card"><span>conversations</span><strong><?= e(count($conversations)) ?></strong><p>active teacher message threads</p></article>
    <article class="stat-card"><span>assigned parents</span><strong><?= e(count($parents)) ?></strong><p>parent contacts linked to your students</p></article>
    <article class="stat-card"><span>fellow teachers</span><strong><?= e(count($teachers)) ?></strong><p>teachers you can contact directly</p></article>
    <article class="stat-card"><span>teacher threads</span><strong><?= e(count($teacherConversations)) ?></strong><p>conversations with fellow teachers</p></article>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Recent</p><h3>Conversation Overview</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Type</th><th>Participant</th><th>Student</th><th>Subject</th><th>Last Message</th><th>Last Activity</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($conversations as $conversation): ?>
                    <tr>
                        <td><?= e(ucwords(str_replace('_', ' ', $conversation['conversation_type'] ?? 'conversation'))) ?></td>
                        <td><?= e($conversation['participant_name'] ?? '-') ?></td>
                        <td><?= e(($conversation['student_name'] ?? '') ?: '-') ?></td>
                        <td><?= e($conversation['subject'] ?? '') ?></td>
                        <td><?= e(($conversation['last_message_preview'] ?? '') ?: '-') ?></td>
                        <td><?= e($conversation['last_activity_at'] ?? $conversation['created_at'] ?? '') ?></td>
                        <td><a href="/teacher/messages/view?id=<?= e($conversation['id']) ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($conversations)): ?><tr><td colspan="7">No teacher conversations have been started yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
