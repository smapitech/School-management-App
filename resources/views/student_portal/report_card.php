<?php
    $fullName = trim(($student['first_name'] ?: $student['applicant']) . ' ' . $student['middle_name'] . ' ' . $student['last_name']);
    $total = array_sum(array_map(fn ($row) => (float) $row['student_score'], $results));
    $obtainable = array_sum(array_map(fn ($row) => (float) $row['total_mark'], $results));
?>

<section class="panel print-report">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Student report card</p>
            <h3><?= e($fullName) ?></h3>
        </div>
        <button class="secondary-action no-print" type="button" data-print>Print</button>
    </div>
    <div class="print-meta">
        <span>Register No: <?= e($student['registration_no']) ?></span>
        <span>Class: <?= e($student['class_name']) ?> <?= e($student['section']) ?></span>
        <span>Generated: <?= e(date('Y-m-d')) ?></span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Subject</th><th>Score</th><th>Obtainable</th><th>Pass Mark</th><th>Grade</th></tr></thead>
            <tbody>
                <?php foreach ($results as $row): ?><tr><td><?= e($row['subject_name']) ?></td><td><?= e($row['student_score']) ?></td><td><?= e($row['total_mark']) ?></td><td><?= e($row['pass_mark']) ?></td><td><?= e($row['grade']) ?></td></tr><?php endforeach; ?>
                <tr><th>Total</th><th><?= e($total) ?></th><th><?= e($obtainable) ?></th><th colspan="2"><?= $obtainable > 0 ? e(round(($total / $obtainable) * 100, 1)) . '%' : '0%' ?></th></tr>
                <?php if (empty($results)): ?><tr><td colspan="5">No result has been recorded yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Attendance Date</th><th>Status</th><th>Remark</th></tr></thead>
            <tbody>
                <?php foreach (array_slice($attendance, 0, 8) as $row): ?><tr><td><?= e($row['attendance_date']) ?></td><td><?= e($row['status']) ?></td><td><?= e($row['remark']) ?></td></tr><?php endforeach; ?>
                <?php if (empty($attendance)): ?><tr><td colspan="3">No attendance record available.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
