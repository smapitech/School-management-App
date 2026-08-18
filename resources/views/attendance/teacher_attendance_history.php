<?php
    $classMap = [];
    foreach ($classes as $class) {
        $classMap[$class['class_name']] = (int) $class['class_id'];
    }
?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2>Attendance History</h2>
        <p>Review attendance records only for the classes and students assigned to your teaching workspace.</p>
    </div>
    <div class="hero-actions">
        <a class="primary-action" href="/teacher/attendance">Mark Attendance</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (!empty($workspaceWarning)): ?>
    <section class="alert-error"><p><?= e($workspaceWarning) ?></p></section>
<?php endif; ?>

<section class="stat-grid">
    <article class="stat-card"><span>records</span><strong><?= e(count($rows)) ?></strong><p>attendance rows inside your assigned scope</p></article>
    <article class="stat-card"><span>present</span><strong><?= e($summary['Present'] ?? 0) ?></strong><p>present records in the filtered list</p></article>
    <article class="stat-card"><span>absent</span><strong><?= e($summary['Absent'] ?? 0) ?></strong><p>absent records in the filtered list</p></article>
    <article class="stat-card"><span>late or excused</span><strong><?= e(($summary['Late'] ?? 0) + ($summary['Excused'] ?? 0)) ?></strong><p>late and excused records combined</p></article>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Filter</p><h3>Find Assigned Attendance Records</h3></div></div>
    <form class="student-search-form" method="get" action="/teacher/attendance/history">
        <label>
            <span>Class</span>
            <select name="class_id">
                <option value="0">All Assigned Classes</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= e($class['class_id']) ?>" <?= (int) ($filters['class_id'] ?? 0) === (int) $class['class_id'] ? 'selected' : '' ?>>
                        <?= e($class['class_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Date</span>
            <input type="date" name="attendance_date" value="<?= e($filters['attendance_date']) ?>">
        </label>
        <label>
            <span>Status</span>
            <select name="status">
                <option value="">All Statuses</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Filter History</button>
        <a class="secondary-action" href="/teacher/attendance/history">Reset</a>
    </form>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">History</p><h3>Assigned Attendance Records</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Student</th><th>Register No</th><th>Class</th><th>Section</th><th>Status</th><th>Note</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <?php $studentName = trim((string) (($row['first_name'] ?: $row['applicant']) . ' ' . ($row['middle_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))); ?>
                    <tr>
                        <td><?= e($row['attendance_date']) ?></td>
                        <td><strong><?= e($studentName) ?></strong></td>
                        <td><?= e($row['registration_no']) ?></td>
                        <td><?= e($row['class_name']) ?></td>
                        <td><?= e($row['section'] ?: '-') ?></td>
                        <td><span class="status"><?= e($row['status']) ?></span></td>
                        <td><?= e($row['remark'] ?: '-') ?></td>
                        <td><a href="/teacher/attendance?class_id=<?= e($classMap[$row['class_name']] ?? 0) ?>&attendance_date=<?= e($row['attendance_date']) ?>">Open Day</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?><tr><td colspan="8">No attendance history matches the selected filters.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
