<section class="module-hero"><div><p class="eyebrow">Classes</p><h2>Timetables</h2><p>Your class timetable and published exam timetable. Creation tools remain in the admin and teacher modules.</p></div></section>
<?php require __DIR__ . '/nav.php'; ?>
<?php if ($student): ?>
<section class="student-portal-grid">
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Student timetable</p><h3><?= e($student['class_name']) ?> <?= e($student['section']) ?></h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Day</th><th>Subject</th><th>Teacher</th><th>Time</th></tr></thead><tbody>
            <?php foreach ($timetables as $row): ?>
                <?php $teacherName = trim(($row['name'] ?? '') . ' ' . ($row['middle_name'] ?? '') . ' ' . ($row['surname'] ?? '')); ?>
                <tr><td><?= e($row['day_of_week']) ?></td><td><?= e($row['subject_name']) ?></td><td><?= e($teacherName) ?></td><td><?= e($row['start_time']) ?> - <?= e($row['end_time']) ?></td></tr>
            <?php endforeach; ?>
            <?php if (empty($timetables)): ?><tr><td colspan="4">No class timetable has been published for your class yet.</td></tr><?php endif; ?>
        </tbody></table></div>
    </article>
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Exam timetable</p><h3>Published Exams</h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Date</th><th>Subject</th><th>Term</th><th>Session</th><th>Time</th></tr></thead><tbody>
            <?php foreach ($examSchedules as $row): ?><tr><td><?= e($row['exam_date']) ?></td><td><?= e($row['subject_name']) ?></td><td><?= e($row['school_term']) ?></td><td><?= e($row['school_session']) ?></td><td><?= e($row['start_time']) ?> - <?= e($row['end_time']) ?></td></tr><?php endforeach; ?>
            <?php if (empty($examSchedules)): ?><tr><td colspan="5">No exam timetable has been published for your class yet.</td></tr><?php endif; ?>
        </tbody></table></div>
    </article>
</section>
<?php endif; ?>
