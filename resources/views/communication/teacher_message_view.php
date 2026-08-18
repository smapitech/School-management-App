<?php
    $isParentConversation = ($conversation['conversation_type'] ?? '') === 'teacher_parent';
    $participantLabel = $isParentConversation ? 'Parent contact' : 'Teacher contact';
    $studentContext = trim((string) ($conversation['student_name'] ?? ''));
    $classContext = trim((string) (($conversation['class_name'] ?? '') . ' ' . ($conversation['section'] ?? '')));
?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2><?= e($conversation['subject'] ?: 'Conversation') ?></h2>
        <p>Reply only inside your own teacher-parent and teacher-teacher conversation space.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/teacher/messages">All Conversations</a>
        <?php if ($isParentConversation): ?>
            <a class="primary-action" href="/teacher/messages/parents">Parent Messages</a>
        <?php else: ?>
            <a class="primary-action" href="/teacher/messages/teachers">Teacher Messages</a>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (!empty($success)): ?>
    <section class="alert-success"><p><?= e($success) ?></p></section>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <section class="alert-error"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></section>
<?php endif; ?>

<section class="mini-stat-grid">
    <article><span>conversation type</span><strong><?= e(ucwords(str_replace('_', ' ', $conversation['conversation_type'] ?? 'conversation'))) ?></strong></article>
    <article><span><?= e($participantLabel) ?></span><strong><?= e($conversation['participant_name'] ?? '-') ?></strong></article>
    <article><span>student</span><strong><?= e($studentContext !== '' ? $studentContext : '-') ?></strong></article>
    <article><span>last activity</span><strong><?= e($conversation['last_activity_at'] ?? $conversation['created_at'] ?? '-') ?></strong></article>
</section>

<section class="dashboard-grid conversation-layout">
    <article class="panel panel-wide">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Message thread</p>
                <h3><?= e($conversation['subject'] ?: 'Conversation') ?></h3>
            </div>
        </div>

        <div class="conversation-thread">
            <?php foreach ($messages as $message): ?>
                <article class="<?= !empty($message['is_mine']) ? 'is-mine' : '' ?>">
                    <div class="conversation-thread-meta">
                        <strong><?= e($message['sender_name'] ?? 'User') ?></strong>
                        <span><?= e(ucfirst((string) ($message['sender_role'] ?? 'user'))) ?> | <?= e($message['created_at'] ?? '') ?></span>
                    </div>
                    <?php if (trim((string) ($message['message'] ?? '')) !== ''): ?>
                        <p><?= nl2br(e($message['message'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($message['attachment_path'])): ?>
                        <a class="conversation-attachment" href="<?= e($message['attachment_path']) ?>" target="_blank" rel="noopener">
                            <?= e(($message['attachment_name'] ?? '') ?: 'Open attachment') ?>
                        </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
                <div class="conversation-empty">No messages have been recorded in this conversation yet.</div>
            <?php endif; ?>
        </div>
    </article>

    <div class="conversation-panel-stack">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Details</p>
                    <h3>Conversation Context</h3>
                </div>
            </div>
            <div class="split-list conversation-detail-list">
                <span><b>Participant</b><strong><?= e($conversation['participant_name'] ?? '-') ?></strong></span>
                <span><b>Status</b><strong><?= e(ucfirst((string) ($conversation['status'] ?? 'open'))) ?></strong></span>
                <?php if ($studentContext !== ''): ?><span><b>Student</b><strong><?= e($studentContext) ?></strong></span><?php endif; ?>
                <?php if ($classContext !== ''): ?><span><b>Class</b><strong><?= e($classContext) ?></strong></span><?php endif; ?>
                <span><b>Started</b><strong><?= e($conversation['created_at'] ?? '-') ?></strong></span>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Reply</p>
                    <h3>Send Message</h3>
                </div>
            </div>
            <form class="payroll-form" method="post" action="/teacher/messages/reply" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="conversation_id" value="<?= e($conversation['id']) ?>">
                <label class="form-wide">
                    <span>Message</span>
                    <textarea name="message" rows="7" placeholder="Write your reply"></textarea>
                </label>
                <label class="form-wide upload-field">
                    <span>Attachment</span>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                    <small>Optional: attach image, PDF, document, spreadsheet, or text file.</small>
                </label>
                <button type="submit">Send Reply</button>
            </form>
        </section>
    </div>
</section>
