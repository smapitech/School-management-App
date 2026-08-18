<?php include __DIR__ . '/nav.php'; ?>
<?php $printQuery = http_build_query(array_filter($filters, fn ($value) => (string) $value !== '' && (string) $value !== '0')); ?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Timetable</p>
        <h2>Exam Schedule</h2>
        <p>Create, edit, view, print, and delete exam timetable entries by class, section, term, and subject record.</p>
    </div>
    <a class="secondary-action" href="/exams/schedule/print<?= $printQuery ? '?' . e($printQuery) : '' ?>">Printable View</a>
</section>

<?php if (can('exams', 'edit')): ?>
    <?php if (!empty($settings)): ?>
        <section class="panel">
            <div class="panel-header"><div><p class="eyebrow"><?= $edit ? 'Edit schedule' : 'Add schedule' ?></p><h3><?= $edit ? 'Update Exam Schedule' : 'New Exam Schedule' ?></h3></div></div>
            <form class="payroll-form accounting-form" method="post" action="/exams/schedule/save">
                <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
                <label>
                    <span>Class/Subject Record</span>
                    <select name="exam_setting_id" required>
                        <option value="">Select record</option>
                        <?php foreach ($settings as $setting): ?>
                            <option value="<?= e($setting['id']) ?>" <?= (int) ($edit['exam_setting_id'] ?? 0) === (int) $setting['id'] ? 'selected' : '' ?>><?= e($setting['class_name']) ?> <?= e($setting['section']) ?> - <?= e($setting['subject_name']) ?> - <?= e($setting['school_term']) ?> <?= e($setting['school_session']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label><span>Date</span><input type="date" name="exam_date" value="<?= e($edit['exam_date'] ?? '') ?>" required></label>
                <label><span>Start</span><input type="time" name="start_time" value="<?= e($edit['start_time'] ?? '') ?>" required></label>
                <label><span>End</span><input type="time" name="end_time" value="<?= e($edit['end_time'] ?? '') ?>" required></label>
                <button type="submit"><?= $edit ? 'Update Schedule' : 'Save Schedule' ?></button>
            </form>
        </section>
    <?php else: ?>
        <section class="panel empty-state">
            <h3>No class-subject records are available yet</h3>
            <p>Open Exam Mark to create the hidden record automatically, then come back here to schedule the exam.</p>
            <a class="secondary-action" href="/exams/marks">Open Exam Mark</a>
        </section>
    <?php endif; ?>
<?php endif; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Filter</p><h3>View Timetable</h3></div></div>
    <form class="student-search-form" method="get" action="/exams/schedule">
        <label><span>Class</span><select name="class_name"><option value="">All classes</option><?php foreach ($classOptions as $class): ?><option value="<?= e($class) ?>" <?= $filters['class_name'] === $class ? 'selected' : '' ?>><?= e($class) ?></option><?php endforeach; ?></select></label>
        <label><span>Section</span><select name="section"><option value="">All sections</option><?php foreach ($sections as $section): ?><option value="<?= e($section) ?>" <?= $filters['section'] === $section ? 'selected' : '' ?>><?= e($section) ?></option><?php endforeach; ?></select></label>
        <label>
            <span>Student</span>
            <select name="student_id">
                <option value="0">Select student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= e($student['id']) ?>" <?= (int) $filters['student_id'] === (int) $student['id'] ? 'selected' : '' ?>><?= e($student['registration_no']) ?> - <?= e($student['applicant']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Show</button>
        <a class="secondary-action" href="/exams/schedule">Reset</a>
    </form>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Schedule list</p><h3>Exam Timetable</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Time</th><th>Class</th><th>Section</th><th>Term</th><th>Session</th><th>Subject</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($schedules as $row): ?>
                    <tr>
                        <td><?= e($row['exam_date']) ?></td>
                        <td><?= e($row['start_time']) ?> - <?= e($row['end_time']) ?></td>
                        <td><?= e($row['class_name']) ?></td>
                        <td><?= e($row['section']) ?></td>
                        <td><?= e($row['school_term']) ?></td>
                        <td><?= e($row['school_session']) ?></td>
                        <td><?= e($row['subject_name']) ?></td>
                        <td class="row-actions">
                            <?php if (can('exams', 'edit')): ?><a href="/exams/schedule?edit=<?= e($row['id']) ?>">Edit</a><?php endif; ?>
                            <?php if (can('exams', 'delete')): ?><form method="post" action="/exams/schedule/delete"><input type="hidden" name="id" value="<?= e($row['id']) ?>"><button type="submit">Delete</button></form><?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($schedules)): ?><tr><td colspan="8">No exam schedule has been created yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
