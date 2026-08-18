<section class="module-hero">
    <div>
        <p class="eyebrow">Internal messaging</p>
        <h2>Internal Messaging</h2>
        <p>Send notices to staff, parents, students, or a selected login level through the internal communication queue.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (can('communication', 'create')): ?>
<?php $isTeacher = ($user['role'] ?? '') === 'teacher'; ?>
<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Compose</p><h3>New Internal Message</h3></div></div>
    <form class="payroll-form" method="post" action="/communication/internal/send" enctype="multipart/form-data">
        <label><span>Channel</span><select name="channel"><option>Internal</option><option>Email</option><option>SMS</option><option>WhatsApp</option></select></label>
        <label><span>Audience</span><select name="audience">
            <?php if ($isTeacher): ?>
                <option>Staff</option><option>Class Students</option><option>Class Parents</option>
            <?php else: ?>
                <option>Staff</option><option>Parents</option><option>Students</option><option>All Users</option>
            <?php endif; ?>
        </select></label>
        <?php if ($isTeacher): ?>
            <label><span>Assigned Class</span><select name="class_scope" required><?php foreach ($teacherAssignments as $assignment): ?><option value="<?= e($assignment['class_name'] . '|' . $assignment['section']) ?>"><?= e($assignment['class_name']) ?> <?= e($assignment['section']) ?></option><?php endforeach; ?></select></label>
        <?php else: ?>
            <label><span>Login Level</span><select name="recipient_role"><option value="">All in audience</option><?php foreach ($roles as $key => $label): ?><option value="<?= e($key) ?>"><?= e($label) ?></option><?php endforeach; ?></select></label>
        <?php endif; ?>
        <label><span>Subject</span><input name="subject" required></label>
        <?php $label = 'Message'; $defaultMessage = ''; $required = true; require __DIR__ . '/editor.php'; ?>
        <label class="form-wide upload-field"><span>Attachment</span><input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt"><small>Optional: attach image, PDF, document, spreadsheet, or text file.</small></label>
        <button type="submit">Queue Message</button>
    </form>
</section>
<?php endif; ?>

<?php require __DIR__ . '/partials_log.php'; ?>
