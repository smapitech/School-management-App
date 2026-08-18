<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2>Teacher Messages</h2>
        <p>Message fellow teachers directly without exposing school-wide parent contacts.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (!empty($success)): ?>
    <section class="alert-success"><p><?= e($success) ?></p></section>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <section class="alert-error"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Compose</p><h3>New Teacher Conversation</h3></div></div>
    <form class="payroll-form" method="post" action="/teacher/messages/send">
        <?= csrf_field() ?>
        <input type="hidden" name="conversation_type" value="teacher_teacher">
        <label>
            <span>Teacher</span>
            <select name="other_teacher_id" required>
                <option value="0">Select Fellow Teacher</option>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= e($teacher['teacher_id']) ?>" <?= (int) ($formValues['other_teacher_id'] ?? 0) === (int) $teacher['teacher_id'] ? 'selected' : '' ?>>
                        <?= e($teacher['name']) ?><?= !empty($teacher['designation']) ? ' | ' . e($teacher['designation']) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label><span>Subject</span><input name="subject" value="<?= e($formValues['subject'] ?? '') ?>" required></label>
        <label class="form-wide"><span>Message</span><textarea name="message" rows="6" required><?= e($formValues['message'] ?? '') ?></textarea></label>
        <button type="submit">Start Teacher Conversation</button>
    </form>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Fellow teachers</p><h3>Available Teacher Contacts</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Teacher</th><th>Employee No</th><th>Role</th><th>Department</th><th>Contact</th></tr></thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><?= e($teacher['name']) ?></td>
                        <td><?= e($teacher['employee_no']) ?></td>
                        <td><?= e(($teacher['designation'] ?? '') ?: ($teacher['role'] ?? '-')) ?></td>
                        <td><?= e(($teacher['department'] ?? '') ?: '-') ?></td>
                        <td><?= e(($teacher['login_email'] ?? '') ?: (($teacher['username'] ?? '') ?: ($teacher['email'] ?? '-'))) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($teachers)): ?><tr><td colspan="5">No fellow teacher is available yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Threads</p><h3>Recent Teacher Conversations</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Teacher</th><th>Subject</th><th>Last Message</th><th>Last Activity</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($conversations as $conversation): ?>
                    <tr>
                        <td><?= e($conversation['participant_name'] ?? '-') ?></td>
                        <td><?= e($conversation['subject'] ?? '') ?></td>
                        <td><?= e(($conversation['last_message_preview'] ?? '') ?: '-') ?></td>
                        <td><?= e($conversation['last_activity_at'] ?? $conversation['created_at'] ?? '') ?></td>
                        <td><a href="/teacher/messages/view?id=<?= e($conversation['id']) ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($conversations)): ?><tr><td colspan="5">No teacher-to-teacher conversation has been started yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
