<?php
    $selectedClass = null;
    foreach ($classes as $class) {
        if ((int) ($class['class_id'] ?? 0) === (int) ($selectedClassId ?? 0)) {
            $selectedClass = $class;
            break;
        }
    }
    $closeQuery = http_build_query(array_filter([
        'class_id' => (int) ($filters['class_id'] ?? 0),
        'name' => $filters['name'] ?? '',
        'registration_no' => $filters['registration_no'] ?? '',
    ], static fn (mixed $value): bool => $value !== '' && $value !== 0));
?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2>My Students</h2>
        <p>Search only within the students attached to your assigned classes and teaching responsibility.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/teacher/classes">My Assigned Classes</a>
        <a class="primary-action" href="/teacher/classes/subjects">My Assigned Subjects</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Search</p>
            <h3>Find students inside your assigned classes</h3>
        </div>
    </div>

    <form class="student-search-form" method="get" action="/teacher/classes/students">
        <label>
            <span>Assigned Class</span>
            <select name="class_id">
                <option value="0">All Assigned Classes</option>
                <?php foreach ($classes as $class): ?>
                    <?php $label = trim($class['class_name'] . ' ' . ($class['section'] ?? '')); ?>
                    <option value="<?= e($class['class_id']) ?>" <?= (int) ($filters['class_id'] ?? 0) === (int) $class['class_id'] ? 'selected' : '' ?>>
                        <?= e($label !== '' ? $label : $class['class_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Name</span>
            <input name="name" value="<?= e($filters['name'] ?? '') ?>" placeholder="Search student name">
        </label>
        <label>
            <span>Register No</span>
            <input name="registration_no" value="<?= e($filters['registration_no'] ?? '') ?>" placeholder="ADM-2026">
        </label>
        <button type="submit">Search</button>
        <a class="secondary-action" href="/teacher/classes/students">Reset</a>
    </form>
</section>

<?php if ($selectedStudent): ?>
    <?php
        $selectedName = trim(($selectedStudent['first_name'] ?: $selectedStudent['applicant']) . ' ' . ($selectedStudent['middle_name'] ?? '') . ' ' . ($selectedStudent['last_name'] ?? ''));
        $selectedAge = 'Not set';
        if (!empty($selectedStudent['date_of_birth'])) {
            $selectedAge = (string) (new DateTime($selectedStudent['date_of_birth']))->diff(new DateTime('today'))->y;
        }
    ?>
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Student profile</p>
                <h3><?= e($selectedName) ?></h3>
            </div>
            <a href="/teacher/classes/students<?= $closeQuery !== '' ? '?' . e($closeQuery) : '' ?>">Close</a>
        </div>
        <div class="table-wrap">
            <table>
                <tbody>
                    <tr><th>Register No</th><td><?= e($selectedStudent['registration_no']) ?></td><th>Age</th><td><?= e($selectedAge) ?></td></tr>
                    <tr><th>Class</th><td><?= e(trim($selectedStudent['class_name'] . ' ' . ($selectedStudent['section'] ?? ''))) ?></td><th>Gender</th><td><?= e($selectedStudent['gender']) ?></td></tr>
                    <tr><th>Guardian</th><td><?= e($selectedStudent['guardian_full_name'] ?: $selectedStudent['guardian']) ?></td><th>Email</th><td><?= e($selectedStudent['email']) ?></td></tr>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Student list</p>
            <h3><?= e(count($students)) ?> assigned student<?= count($students) === 1 ? '' : 's' ?></h3>
        </div>
        <?php if ($selectedClass): ?>
            <strong><?= e(trim($selectedClass['class_name'] . ' ' . ($selectedClass['section'] ?? ''))) ?></strong>
        <?php endif; ?>
    </div>

    <div class="table-wrap">
        <table class="student-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Guardian</th>
                    <th>Class Section</th>
                    <th>Register No</th>
                    <th>Gender</th>
                    <th>Open</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <?php
                        $fullName = trim(($student['first_name'] ?: $student['applicant']) . ' ' . ($student['middle_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
                        $viewQuery = http_build_query(array_filter([
                            'class_id' => (int) ($filters['class_id'] ?? 0),
                            'name' => $filters['name'] ?? '',
                            'registration_no' => $filters['registration_no'] ?? '',
                            'student_id' => (int) $student['id'],
                        ], static fn (mixed $value): bool => $value !== '' && $value !== 0));
                    ?>
                    <tr>
                        <td>
                            <strong><?= e($fullName) ?></strong>
                            <small><?= e($student['email']) ?></small>
                        </td>
                        <td><?= e($student['guardian_full_name'] ?: $student['guardian']) ?></td>
                        <td><?= e(trim($student['class_name'] . ' ' . ($student['section'] ?? ''))) ?></td>
                        <td><?= e($student['registration_no']) ?></td>
                        <td><?= e($student['gender']) ?></td>
                        <td><a href="/teacher/classes/students?<?= e($viewQuery) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="6">No assigned students match this search.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
