<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2>Parent Messages</h2>
        <p>Start conversations only with parents linked to your assigned students.</p>
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
    <div class="panel-header"><div><p class="eyebrow">Compose</p><h3>New Parent Conversation</h3></div></div>
    <form class="payroll-form" method="post" action="/teacher/messages/send">
        <?= csrf_field() ?>
        <input type="hidden" name="conversation_type" value="teacher_parent">
        <label class="form-wide">
            <span>Parent and Student</span>
            <select id="teacher-parent-target" name="parent_student_target" required>
                <option value="">Select Parent Contact</option>
                <?php foreach ($parents as $parent): ?>
                    <?php $value = (int) $parent['parent_id'] . ':' . (int) $parent['student_id']; ?>
                    <option value="<?= e($value) ?>" data-parent-id="<?= e($parent['parent_id']) ?>" data-student-id="<?= e($parent['student_id']) ?>" <?= ((int) ($formValues['parent_id'] ?? 0) === (int) $parent['parent_id'] && (int) ($formValues['student_id'] ?? 0) === (int) $parent['student_id']) ? 'selected' : '' ?>>
                        <?= e($parent['parent_name']) ?> | <?= e($parent['student_name']) ?> | <?= e($parent['class_name']) ?> <?= e($parent['section']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <input type="hidden" id="teacher-parent-id" name="parent_id" value="<?= e($formValues['parent_id'] ?? 0) ?>">
        <input type="hidden" id="teacher-student-id" name="student_id" value="<?= e($formValues['student_id'] ?? 0) ?>">
        <label><span>Subject</span><input name="subject" value="<?= e($formValues['subject'] ?? '') ?>" required></label>
        <label class="form-wide"><span>Message</span><textarea name="message" rows="6" required><?= e($formValues['message'] ?? '') ?></textarea></label>
        <button type="submit">Start Parent Conversation</button>
    </form>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Assigned parents</p><h3>Available Parent Contacts</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Parent</th><th>Student</th><th>Class</th><th>Relationship</th><th>Contact</th></tr></thead>
            <tbody>
                <?php foreach ($parents as $parent): ?>
                    <tr>
                        <td><?= e($parent['parent_name']) ?></td>
                        <td><?= e($parent['student_name']) ?></td>
                        <td><?= e($parent['class_name']) ?> <?= e($parent['section']) ?></td>
                        <td><?= e(($parent['guardian_relationship'] ?? '') ?: '-') ?></td>
                        <td><?= e(($parent['parent_login'] ?? '') ?: ($parent['guardian_mobile'] ?? '-')) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($parents)): ?><tr><td colspan="5">No parent contact is linked to your assigned students yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Threads</p><h3>Recent Parent Conversations</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Parent</th><th>Student</th><th>Subject</th><th>Last Message</th><th>Last Activity</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($conversations as $conversation): ?>
                    <tr>
                        <td><?= e($conversation['participant_name'] ?? '-') ?></td>
                        <td><?= e(($conversation['student_name'] ?? '') ?: '-') ?></td>
                        <td><?= e($conversation['subject'] ?? '') ?></td>
                        <td><?= e(($conversation['last_message_preview'] ?? '') ?: '-') ?></td>
                        <td><?= e($conversation['last_activity_at'] ?? $conversation['created_at'] ?? '') ?></td>
                        <td><a href="/teacher/messages/view?id=<?= e($conversation['id']) ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($conversations)): ?><tr><td colspan="6">No parent conversation has been started yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    (function () {
        var select = document.getElementById('teacher-parent-target');
        var parentInput = document.getElementById('teacher-parent-id');
        var studentInput = document.getElementById('teacher-student-id');

        if (!select || !parentInput || !studentInput) {
            return;
        }

        function syncSelection() {
            var option = select.options[select.selectedIndex];
            parentInput.value = option ? (option.getAttribute('data-parent-id') || '0') : '0';
            studentInput.value = option ? (option.getAttribute('data-student-id') || '0') : '0';
        }

        select.addEventListener('change', syncSelection);
        syncSelection();
    }());
</script>
