<?php
    $sectionOptions = [];
    foreach ($sectionOptionsByClass as $classSections) {
        foreach ($classSections as $section) {
            if ($section !== '') {
                $sectionOptions[$section] = $section;
            }
        }
    }
    $ownRows = array_values(array_filter($rows, fn (array $row): bool => (int) ($row['teacher_id'] ?? 0) === (int) ($teacherId ?? 0)));
?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2>Class Timetable</h2>
        <p>View timetable rows only for the classes and subjects assigned to your teaching workspace.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/teacher/classes">My Assigned Classes</a>
        <a class="primary-action" href="/teacher/classes/timetable/create">Create Timetable</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (!empty($success)): ?>
    <section class="alert-success"><p><?= e($success) ?></p></section>
<?php endif; ?>
<?php if (!empty($workspaceWarning)): ?>
    <section class="alert-error"><p><?= e($workspaceWarning) ?></p></section>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <section class="alert-error"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></section>
<?php endif; ?>

<section class="stat-grid">
    <article class="stat-card"><span>visible rows</span><strong><?= e(count($rows)) ?></strong><p>timetable records inside your assigned scope</p></article>
    <article class="stat-card"><span>my rows</span><strong><?= e(count($ownRows)) ?></strong><p>entries you can edit or delete</p></article>
    <article class="stat-card"><span>assigned classes</span><strong><?= e(count($classes)) ?></strong><p>class sections linked to your profile</p></article>
    <article class="stat-card"><span>assigned subjects</span><strong><?= e(count($subjectOptions)) ?></strong><p>subjects available in your timetable scope</p></article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Filter</p>
            <h3>Find timetable rows inside your assigned scope</h3>
        </div>
    </div>

    <form class="student-search-form" method="get" action="/teacher/classes/timetable">
        <label>
            <span>Class</span>
            <select name="class_id">
                <option value="0">All Assigned Classes</option>
                <?php foreach ($classOptions as $class): ?>
                    <option value="<?= e($class['class_id']) ?>" <?= (int) ($filters['class_id'] ?? 0) === (int) $class['class_id'] ? 'selected' : '' ?>>
                        <?= e($class['class_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Section</span>
            <select name="section">
                <option value="">All Sections</option>
                <?php foreach ($sectionOptions as $section): ?>
                    <option value="<?= e($section) ?>" <?= ($filters['section'] ?? '') === $section ? 'selected' : '' ?>><?= e($section) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Subject</span>
            <select name="subject_id">
                <option value="0">All Assigned Subjects</option>
                <?php foreach ($subjectOptions as $subject): ?>
                    <option value="<?= e($subject['subject_id']) ?>" <?= (int) ($filters['subject_id'] ?? 0) === (int) $subject['subject_id'] ? 'selected' : '' ?>>
                        <?= e($subject['subject_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Day</span>
            <select name="day_of_week">
                <option value="">All Days</option>
                <?php foreach ($days as $day): ?>
                    <option value="<?= e($day) ?>" <?= ($filters['day_of_week'] ?? '') === $day ? 'selected' : '' ?>><?= e($day) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Filter</button>
        <a class="secondary-action" href="/teacher/classes/timetable">Reset</a>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Timetable list</p>
            <h3>Assigned Timetable Rows</h3>
        </div>
    </div>

    <div class="table-wrap">
        <table class="student-table">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Subject</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Room</th>
                    <th>Note</th>
                    <th>Teacher</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e($row['day_of_week']) ?></td>
                        <td><?= e($row['class_name']) ?></td>
                        <td><?= e($row['section'] ?: 'All Sections') ?></td>
                        <td>
                            <strong><?= e($row['subject_name']) ?></strong>
                            <small><?= e($row['subject_code']) ?></small>
                        </td>
                        <td><?= e($row['start_time']) ?></td>
                        <td><?= e($row['end_time']) ?></td>
                        <td><?= e($row['room'] ?: '-') ?></td>
                        <td><?= e($row['note'] ?: '-') ?></td>
                        <td><?= e(trim($row['name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . ($row['surname'] ?? ''))) ?></td>
                        <td class="row-actions">
                            <?php if ((int) ($row['teacher_id'] ?? 0) === (int) ($teacherId ?? 0)): ?>
                                <a href="/teacher/classes/timetable/edit?id=<?= e($row['id']) ?>">Edit</a>
                                <form method="post" action="/teacher/classes/timetable/delete" onsubmit="return confirm('Delete this timetable row?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            <?php else: ?>
                                <span class="status">View Only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="10">No timetable rows match your assigned filters.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
