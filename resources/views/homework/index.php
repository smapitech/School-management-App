<section class="module-hero">
    <div>
        <p class="eyebrow">Homework Portal</p>
        <h2>Homework Management</h2>
        <p>Create, publish, track, mark, and report on homework across classes, subjects, teachers, students, and parents.</p>
    </div>
    <?php if (can('homework', 'create')): ?><a class="primary-action" href="/homework/create">Create Homework</a><?php endif; ?>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="stat-grid">
    <article class="stat-card"><span>homework</span><strong><?= e($summary['total_homework']) ?></strong><p>created records</p></article>
    <article class="stat-card"><span>published</span><strong><?= e($summary['published']) ?></strong><p>visible to students</p></article>
    <article class="stat-card"><span>pending</span><strong><?= e($summary['pending_submissions']) ?></strong><p>awaiting submission</p></article>
    <article class="stat-card"><span>completion</span><strong><?= e($summary['completion_rate']) ?>%</strong><p>overall submission rate</p></article>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Filter</p><h3>Homework List</h3></div></div>
    <form class="filter-form" method="get" action="/homework">
        <label><span>Class</span><select name="class_name"><option value="">All Classes</option><?php foreach ($classOptions as $class): ?><option value="<?= e($class) ?>" <?= ($filters['class_name'] ?? '') === $class ? 'selected' : '' ?>><?= e($class) ?></option><?php endforeach; ?></select></label>
        <label><span>Section</span><select name="section"><option value="">All Sections</option><?php foreach ($sectionOptions as $section): ?><option value="<?= e($section) ?>" <?= ($filters['section'] ?? '') === $section ? 'selected' : '' ?>><?= e($section) ?></option><?php endforeach; ?></select></label>
        <label><span>Subject</span><select name="subject_id"><option value="">All Subjects</option><?php foreach ($subjects as $subject): ?><option value="<?= e($subject['id'] ?? $subject['subject_id']) ?>" <?= (int) ($filters['subject_id'] ?? 0) === (int) ($subject['id'] ?? $subject['subject_id']) ? 'selected' : '' ?>><?= e($subject['subject_name']) ?></option><?php endforeach; ?></select></label>
        <label><span>Status</span><select name="status"><option value="">All Status</option><?php foreach (['draft', 'published', 'closed', 'archived'] as $status): ?><option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option><?php endforeach; ?></select></label>
        <button type="submit">Apply Filter</button>
    </form>
    <div class="homework-grid">
        <?php foreach ($homeworks as $homework): ?>
            <article class="homework-card">
                <div>
                    <span class="status"><?= e(ucfirst($homework['status'])) ?></span>
                    <h3><?= e($homework['title']) ?></h3>
                    <p><?= e($homework['class_name']) ?> <?= e($homework['section']) ?> | <?= e($homework['subject_name'] ?: $homework['subject']) ?></p>
                </div>
                <div class="rich-content"><?= rich_text($homework['description']) ?></div>
                <dl>
                    <div><dt>Due</dt><dd><?= e($homework['due_at'] ?: $homework['due_date']) ?></dd></div>
                    <div><dt>Marks</dt><dd><?= e($homework['total_marks']) ?></dd></div>
                    <div><dt>Submissions</dt><dd><?= e($homework['submissions_count'] ?? 0) ?></dd></div>
                    <div><dt>Teacher</dt><dd><?= e(trim(($homework['teacher_name'] ?? '') . ' ' . ($homework['teacher_surname'] ?? '')) ?: 'Admin') ?></dd></div>
                </dl>
                <div class="row-actions">
                    <a href="/homework/submissions?homework_id=<?= e($homework['id']) ?>">View</a>
                    <?php if (can('homework', 'edit')): ?><a href="/homework/edit?id=<?= e($homework['id']) ?>">Edit</a><?php endif; ?>
                    <?php if (can('homework', 'edit')): ?><form method="post" action="/homework/close"><input type="hidden" name="id" value="<?= e($homework['id']) ?>"><input type="hidden" name="status" value="closed"><button type="submit">Close</button></form><?php endif; ?>
                    <?php if (can('homework', 'delete') && $homework['status'] === 'draft'): ?><form method="post" action="/homework/delete" onsubmit="return confirm('Delete this draft homework?');"><input type="hidden" name="id" value="<?= e($homework['id']) ?>"><button type="submit">Delete</button></form><?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (empty($homeworks)): ?><p class="muted">No homework matches this filter.</p><?php endif; ?>
    </div>
</section>
