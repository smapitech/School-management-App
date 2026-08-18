<section class="module-hero">
    <div><p class="eyebrow">Homework Portal</p><h2>Reports</h2><p>Track completion, late work, marking progress, teacher activity, class performance, and subject performance.</p></div>
    <button class="secondary-action" type="button" onclick="window.print()">Print Report</button>
</section>
<?php require __DIR__ . '/nav.php'; ?>

<section class="stat-grid">
    <article class="stat-card"><span>Total Homework</span><strong><?= e($summary['total_homework']) ?></strong><p>created</p></article>
    <article class="stat-card"><span>Marked</span><strong><?= e($summary['marked']) ?></strong><p>completed reviews</p></article>
    <article class="stat-card"><span>Late</span><strong><?= e($summary['late']) ?></strong><p>late submissions</p></article>
    <article class="stat-card"><span>Missed</span><strong><?= e($summary['missed']) ?></strong><p>not submitted</p></article>
</section>

<section class="homework-report-grid">
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Teacher</p><h3>Teacher Activity</h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Teacher</th><th>Homework</th><th>Submissions</th><th>Marked</th></tr></thead><tbody>
            <?php foreach ($teacherSummary as $row): ?><tr><td><?= e($row['teacher']) ?></td><td><?= e($row['homework']) ?></td><td><?= e($row['submissions']) ?></td><td><?= e($row['marked']) ?></td></tr><?php endforeach; ?>
            <?php if (empty($teacherSummary)): ?><tr><td colspan="4">No teacher activity yet.</td></tr><?php endif; ?>
        </tbody></table></div>
    </article>
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Class</p><h3>Class Performance</h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Class</th><th>Homework</th><th>Submissions</th><th>Late</th></tr></thead><tbody>
            <?php foreach ($classSummary as $row): ?><tr><td><?= e($row['class']) ?></td><td><?= e($row['homework']) ?></td><td><?= e($row['submissions']) ?></td><td><?= e($row['late']) ?></td></tr><?php endforeach; ?>
            <?php if (empty($classSummary)): ?><tr><td colspan="4">No class report yet.</td></tr><?php endif; ?>
        </tbody></table></div>
    </article>
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Subject</p><h3>Subject Report</h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Subject</th><th>Homework</th><th>Submissions</th></tr></thead><tbody>
            <?php foreach ($subjectSummary as $row): ?><tr><td><?= e($row['subject']) ?></td><td><?= e($row['homework']) ?></td><td><?= e($row['submissions']) ?></td></tr><?php endforeach; ?>
            <?php if (empty($subjectSummary)): ?><tr><td colspan="3">No subject report yet.</td></tr><?php endif; ?>
        </tbody></table></div>
    </article>
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Student</p><h3>Student Report</h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Student</th><th>Assigned</th><th>Submitted</th><th>Late</th><th>Missed</th><th>Average</th></tr></thead><tbody>
            <?php foreach ($studentSummary as $row): ?><tr><td><?= e($row['student']) ?></td><td><?= e($row['assigned']) ?></td><td><?= e(($row['submitted'] ?? 0) + ($row['marked'] ?? 0)) ?></td><td><?= e($row['late'] ?? 0) ?></td><td><?= e($row['missed'] ?? 0) ?></td><td><?= e($row['average_score']) ?></td></tr><?php endforeach; ?>
            <?php if (empty($studentSummary)): ?><tr><td colspan="6">No student report yet.</td></tr><?php endif; ?>
        </tbody></table></div>
    </article>
</section>
