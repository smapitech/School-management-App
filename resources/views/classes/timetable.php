<section class="module-hero">
    <div>
        <p class="eyebrow">Timetable</p>
        <h2>Class Timetable</h2>
        <p>Create and filter timetables for each class and section.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Filter</p><h3>View Class Timetable</h3></div></div>
    <form class="student-search-form" method="get" action="/classes/timetable">
        <label><span>Class</span><select name="class_name"><option value="">All</option><?php foreach ($classOptions as $class): ?><option value="<?= e($class) ?>" <?= $filters['class_name'] === $class ? 'selected' : '' ?>><?= e($class) ?></option><?php endforeach; ?></select></label>
        <label><span>Section</span><select name="section"><option value="">All</option><?php foreach ($sections as $section): ?><option value="<?= e($section) ?>" <?= $filters['section'] === $section ? 'selected' : '' ?>><?= e($section) ?></option><?php endforeach; ?></select></label>
        <button type="submit">Filter</button>
        <a class="secondary-action" href="/classes/timetable">Reset</a>
    </form>
</section>

<?php if (can('classes', 'edit')): ?>
    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">Create</p><h3>Create Class Timetable</h3></div></div>
        <?php if (empty($subjects)): ?>
            <div class="alert-error">Create Subject first.</div>
        <?php else: ?>
            <form class="payroll-form" method="post" action="/classes/timetable/save">
                <label><span>Class</span><select name="class_name" required><?php foreach ($classOptions as $class): ?><option value="<?= e($class) ?>"><?= e($class) ?></option><?php endforeach; ?></select></label>
                <label><span>Section</span><select name="section" required><?php foreach ($sections as $section): ?><option value="<?= e($section) ?>"><?= e($section) ?></option><?php endforeach; ?></select></label>
                <label><span>Subject</span><select name="subject_id" required><?php foreach ($subjects as $subject): ?><option value="<?= e($subject['id']) ?>"><?= e($subject['subject_name']) ?></option><?php endforeach; ?></select></label>
                <label><span>Teacher</span><select name="teacher_id" required><?php foreach ($teachers as $teacher): ?><option value="<?= e($teacher['id']) ?>"><?= e(trim($teacher['name'] . ' ' . $teacher['surname'])) ?></option><?php endforeach; ?></select></label>
                <label><span>Day</span><select name="day_of_week" required><?php foreach ($days as $day): ?><option><?= e($day) ?></option><?php endforeach; ?></select></label>
                <label><span>Start Time</span><input type="time" name="start_time" required></label>
                <label><span>End Time</span><input type="time" name="end_time" required></label>
                <button type="submit">Create Timetable</button>
            </form>
        <?php endif; ?>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Timetable list</p><h3>Class Timetable Rows</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Class</th><th>Section</th><th>Day</th><th>Subject</th><th>Teacher</th><th>Time</th></tr></thead>
            <tbody>
                <?php foreach ($rows as $row): ?><tr><td><?= e($row['class_name']) ?></td><td><?= e($row['section']) ?></td><td><?= e($row['day_of_week']) ?></td><td><?= e($row['subject_name']) ?></td><td><?= e(trim($row['name'] . ' ' . $row['middle_name'] . ' ' . $row['surname'])) ?></td><td><?= e($row['start_time'] . ' - ' . $row['end_time']) ?></td></tr><?php endforeach; ?>
                <?php if (empty($rows)): ?><tr><td colspan="6">No timetable rows found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
