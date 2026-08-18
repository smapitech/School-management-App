<section class="panel print-report">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Printable timetable</p>
            <h3>Exam Schedule</h3>
        </div>
        <button class="secondary-action no-print" type="button" data-print>Print</button>
    </div>
    <div class="print-meta">
        <span>Class: <?= e($filters['class_name'] ?: 'All Classes') ?></span>
        <span>Section: <?= e($filters['section'] ?: 'All Sections') ?></span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Time</th><th>Class</th><th>Section</th><th>Term</th><th>Session</th><th>Subject</th></tr></thead>
            <tbody>
                <?php foreach ($schedules as $row): ?>
                    <tr><td><?= e($row['exam_date']) ?></td><td><?= e($row['start_time']) ?> - <?= e($row['end_time']) ?></td><td><?= e($row['class_name']) ?></td><td><?= e($row['section']) ?></td><td><?= e($row['school_term']) ?></td><td><?= e($row['school_session']) ?></td><td><?= e($row['subject_name']) ?></td></tr>
                <?php endforeach; ?>
                <?php if (empty($schedules)): ?><tr><td colspan="7">No exam schedule matches this report.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
