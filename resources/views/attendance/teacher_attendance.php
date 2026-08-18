<?php
    $selectedClass = null;
    foreach ($classes as $class) {
        if ((int) ($filters['class_id'] ?? 0) === (int) ($class['class_id'] ?? 0)) {
            $selectedClass = $class;
            break;
        }
    }
    $savedCount = count(array_filter($records ?? [], static fn (array $row): bool => !empty($row)));
?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2>Mark Student Attendance</h2>
        <p>Load one of your assigned classes, then record daily attendance only for the students inside your teaching scope.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/teacher/attendance/history">Attendance History</a>
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
    <article class="stat-card"><span>assigned classes</span><strong><?= e(count($classes)) ?></strong><p>classes available for attendance</p></article>
    <article class="stat-card"><span>loaded students</span><strong><?= e(count($students)) ?></strong><p>students visible in the selected class</p></article>
    <article class="stat-card"><span>saved today</span><strong><?= e($savedCount) ?></strong><p>attendance rows already stored for the selected date</p></article>
    <article class="stat-card"><span>selected date</span><strong><?= e($filters['attendance_date']) ?></strong><p>attendance day currently loaded</p></article>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Filter</p><h3>Select Assigned Class and Date</h3></div></div>
    <form class="student-search-form" method="get" action="/teacher/attendance">
        <label>
            <span>Class</span>
            <select name="class_id" required>
                <option value="0">Select Assigned Class</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= e($class['class_id']) ?>" <?= (int) ($filters['class_id'] ?? 0) === (int) $class['class_id'] ? 'selected' : '' ?>>
                        <?= e($class['class_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Date</span>
            <input type="date" name="attendance_date" value="<?= e($filters['attendance_date']) ?>" required>
        </label>
        <button type="submit">Load Students</button>
        <a class="secondary-action" href="/teacher/attendance">Reset</a>
    </form>
</section>

<?php if ($loaded): ?>
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow"><?= e($filters['attendance_date']) ?></p>
                <h3><?= e($selectedClass['class_name'] ?? 'Assigned Class') ?> Student List</h3>
            </div>
        </div>
        <form method="post" action="/teacher/attendance/save">
            <?= csrf_field() ?>
            <input type="hidden" name="class_id" value="<?= e($filters['class_id']) ?>">
            <input type="hidden" name="attendance_date" value="<?= e($filters['attendance_date']) ?>">
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Passport</th><th>Student Name</th><th>Register No</th><th>Section</th><th>Status</th><th>Note</th></tr></thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <?php
                                $studentId = (int) $student['id'];
                                $fullName = trim((string) (($student['first_name'] ?: $student['applicant']) . ' ' . ($student['middle_name'] ?? '') . ' ' . ($student['last_name'] ?? '')));
                                $initials = strtoupper(substr($student['first_name'] ?: $student['applicant'], 0, 1) . substr($student['last_name'] ?: $student['applicant'], 0, 1));
                                $record = $records[$studentId] ?? [];
                                $draft = $draftRows[$studentId] ?? [];
                                $selectedStatus = $draft['status'] ?? $record['status'] ?? 'Present';
                                $remark = $draft['remark'] ?? $record['remark'] ?? '';
                            ?>
                            <tr>
                                <td><?php if (!empty($student['profile_picture'])): ?><img class="student-photo" src="<?= e($student['profile_picture']) ?>" alt="<?= e($fullName) ?>"><?php else: ?><span class="student-photo placeholder"><?= e($initials) ?></span><?php endif; ?></td>
                                <td><strong><?= e($fullName) ?></strong></td>
                                <td><?= e($student['registration_no']) ?></td>
                                <td><?= e($student['section'] ?: '-') ?></td>
                                <td>
                                    <select class="table-input attendance-status-select" name="attendance[<?= e($studentId) ?>][status]" required>
                                        <?php foreach ($statuses as $status): ?>
                                            <option value="<?= e($status) ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input class="table-input attendance-remark" name="attendance[<?= e($studentId) ?>][remark]" value="<?= e($remark) ?>" placeholder="Optional note"></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($students)): ?><tr><td colspan="6">No assigned students were found for this class.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($students)): ?><div class="form-actions"><button type="submit">Save Attendance</button></div><?php endif; ?>
        </form>
    </section>
<?php endif; ?>
