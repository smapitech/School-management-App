<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2>My Assigned Classes</h2>
        <p>Review the class sections, subject coverage, and student groups attached to your teaching responsibility.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/teacher/classes/subjects">My Assigned Subjects</a>
        <a class="primary-action" href="/teacher/classes/students">My Students</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="stat-grid">
    <article class="stat-card"><span>assigned classes</span><strong><?= e(count($classes)) ?></strong><p>class sections linked to your login</p></article>
    <article class="stat-card"><span>assigned subjects</span><strong><?= e(count($subjects)) ?></strong><p>subjects available under those classes</p></article>
    <article class="stat-card"><span>students</span><strong><?= e(count($students)) ?></strong><p>students you are permitted to view</p></article>
    <article class="stat-card"><span>timetable rows</span><strong><?= e(count($timetables)) ?></strong><p>scheduled lessons for your classes</p></article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Assignments</p>
            <h3>Assigned Class List</h3>
        </div>
    </div>

    <div class="table-wrap">
        <table class="student-table">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Role</th>
                    <th>Subjects</th>
                    <th>Students</th>
                    <th>Open</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><strong><?= e($class['class_name']) ?></strong></td>
                        <td><?= e($class['section'] ?: 'All Sections') ?></td>
                        <td><?= e(!empty($class['is_class_teacher']) ? 'Class Teacher' : 'Subject Teacher') ?></td>
                        <td><?= e($class['subject_count']) ?></td>
                        <td><?= e($class['student_count']) ?></td>
                        <td class="row-actions">
                            <a href="/teacher/classes/subjects?class_id=<?= e($class['class_id']) ?>">Subjects</a>
                            <a href="/teacher/classes/students?class_id=<?= e($class['class_id']) ?>">Students</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($classes)): ?>
                    <tr>
                        <td colspan="6">No active class assignment is linked to this teacher login yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Schedule</p>
            <h3>Recent Timetable Rows</h3>
        </div>
        <a href="/teacher/classes/timetable">Open timetable</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Day</th>
                    <th>Subject</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($timetables, 0, 8) as $row): ?>
                    <tr>
                        <td><?= e($row['class_name']) ?></td>
                        <td><?= e($row['section']) ?></td>
                        <td><?= e($row['day_of_week']) ?></td>
                        <td><?= e($row['subject_name']) ?></td>
                        <td><?= e($row['start_time'] . ' - ' . $row['end_time']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($timetables)): ?>
                    <tr>
                        <td colspan="5">No timetable rows are linked to your assigned classes yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
