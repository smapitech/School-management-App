<section class="module-hero">
    <div><p class="eyebrow">Homework Portal</p><h2><?= $homework ? 'Edit Homework' : 'Create Homework' ?></h2><p>Publish structured class work with deadline, marks, attachments, and submission rules.</p></div>
</section>
<?php require __DIR__ . '/nav.php'; ?>

<?php if (!empty($errors)): ?><section class="alert-error"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></section><?php endif; ?>

<section class="panel">
    <form class="form-grid homework-form" method="post" action="/homework/save" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= e($homework['id'] ?? '') ?>">
        <label><span>Homework Title</span><input name="title" required maxlength="255" value="<?= e($homework['title'] ?? '') ?>"></label>
        <label><span>Topic</span><input name="topic" value="<?= e($homework['topic'] ?? '') ?>"></label>
        <label><span>Class</span><select name="class_name" required><option value="">Select Class</option><?php foreach ($classOptions as $class): ?><option value="<?= e($class) ?>" <?= ($homework['class_name'] ?? '') === $class ? 'selected' : '' ?>><?= e($class) ?></option><?php endforeach; ?></select></label>
        <label><span>Section</span><select name="section" required><option value="">Select Section</option><?php foreach ($sectionOptions as $section): ?><option value="<?= e($section) ?>" <?= ($homework['section'] ?? '') === $section ? 'selected' : '' ?>><?= e($section) ?></option><?php endforeach; ?></select></label>
        <label><span>Subject</span><select name="subject_id" required><option value="">Select Subject</option><?php foreach ($subjects as $subject): ?><?php $id = $subject['id'] ?? $subject['subject_id']; ?><option value="<?= e($id) ?>" <?= (int) ($homework['subject_id'] ?? 0) === (int) $id ? 'selected' : '' ?>><?= e($subject['subject_name']) ?></option><?php endforeach; ?></select></label>
        <label><span>Due Date and Time</span><input type="datetime-local" name="due_at" required value="<?= e(str_replace(' ', 'T', substr($homework['due_at'] ?? '', 0, 16))) ?>"></label>
        <label><span>Total Marks</span><input type="number" name="total_marks" min="1" step="0.01" required value="<?= e($homework['total_marks'] ?? 100) ?>"></label>
        <label><span>Submission Type</span><select name="submission_type" required><?php foreach (['text' => 'Text answer', 'file' => 'File upload', 'both' => 'Text and file', 'link' => 'Link submission'] as $value => $label): ?><option value="<?= e($value) ?>" <?= ($homework['submission_type'] ?? 'both') === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
        <label><span>Status</span><select name="status"><?php foreach (['draft', 'published', 'closed', 'archived'] as $status): ?><option value="<?= e($status) ?>" <?= ($homework['status'] ?? 'draft') === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option><?php endforeach; ?></select></label>
        <label><span>External Resource Link</span><input type="url" name="resource_link" value="<?= e($homework['resource_link'] ?? '') ?>"></label>
        <label><span>Attachment</span><input type="file" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"><?php if (!empty($homework['attachment_path'])): ?><small>Current: <?= e($homework['attachment_name']) ?></small><?php endif; ?></label>
        <label class="check-row"><input type="checkbox" name="allow_late_submission" value="1" <?= !empty($homework['allow_late_submission']) ? 'checked' : '' ?>> Allow late submission</label>
        <?php $label = 'Description / Instructions'; $fieldName = 'description'; $defaultMessage = $homework['description'] ?? ''; $required = true; require dirname(__DIR__) . '/communication/editor.php'; ?>
        <button type="submit"><?= $homework ? 'Save Changes' : 'Create Homework' ?></button>
    </form>
</section>
