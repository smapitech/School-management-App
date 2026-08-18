<section class="module-hero">
    <div>
        <p class="eyebrow">Student attendance</p>
        <h2>Student Attendance</h2>
        <p>Filter by class, section, and date, then record each student's attendance status and parent-visible remark.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Filter</p><h3>Select Class and Date</h3></div></div>
    <form class="student-search-form" method="get" action="/attendance/students">
        <label><span>Class</span><select name="class_name" required><option value="">Class</option><?php foreach ($classOptions as $class): ?><option value="<?= e($class) ?>" <?= $filters['class_name'] === $class ? 'selected' : '' ?>><?= e($class) ?></option><?php endforeach; ?></select></label>
        <label><span>Section</span><select name="section" required><option value="">Section</option><?php foreach ($sections as $section): ?><option value="<?= e($section) ?>" <?= $filters['section'] === $section ? 'selected' : '' ?>><?= e($section) ?></option><?php endforeach; ?></select></label>
        <label><span>Date</span><input type="date" name="attendance_date" value="<?= e($filters['attendance_date']) ?>" required></label>
        <button type="submit">Load Student List</button>
    </form>
</section>

<?php if ($loaded): ?>
    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow"><?= e($filters['attendance_date']) ?></p><h3><?= e($filters['class_name']) ?> <?= e($filters['section']) ?> Student List</h3></div></div>
        <form method="post" action="/attendance/students/save">
            <input type="hidden" name="class_name" value="<?= e($filters['class_name']) ?>">
            <input type="hidden" name="section" value="<?= e($filters['section']) ?>">
            <input type="hidden" name="attendance_date" value="<?= e($filters['attendance_date']) ?>">
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Passport</th><th>Student Name</th><th>Register No</th><th>Status</th><th>Remark</th></tr></thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <?php
                                $studentId = (int) $student['id'];
                                $fullName = trim(($student['first_name'] ?: $student['applicant']) . ' ' . $student['middle_name'] . ' ' . $student['last_name']);
                                $initials = strtoupper(substr($student['first_name'] ?: $student['applicant'], 0, 1) . substr($student['last_name'] ?: $student['applicant'], 0, 1));
                                $record = $records[$studentId] ?? [];
                                $selected = $record['status'] ?? 'Present';
                            ?>
                            <tr>
                                <td><?php if (!empty($student['profile_picture'])): ?><img class="student-photo" src="<?= e($student['profile_picture']) ?>" alt="<?= e($fullName) ?>"><?php else: ?><span class="student-photo placeholder"><?= e($initials) ?></span><?php endif; ?></td>
                                <td><strong><?= e($fullName) ?></strong></td>
                                <td><?= e($student['registration_no']) ?></td>
                                <td class="attendance-options">
                                    <?php foreach ($statuses as $status): ?>
                                        <label><input type="radio" name="attendance[<?= e($studentId) ?>][status]" value="<?= e($status) ?>" <?= $selected === $status ? 'checked' : '' ?>> <?= e($status) ?></label>
                                    <?php endforeach; ?>
                                </td>
                                <td><input class="table-input attendance-remark" name="attendance[<?= e($studentId) ?>][remark]" value="<?= e($record['remark'] ?? '') ?>" placeholder="Remark visible to parent"></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($students)): ?><tr><td colspan="5">No admitted students found for this class and section.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (can('attendance', 'edit')): ?><div class="form-actions"><button type="submit">Save Student Attendance</button></div><?php endif; ?>
        </form>
    </section>
<?php endif; ?>
