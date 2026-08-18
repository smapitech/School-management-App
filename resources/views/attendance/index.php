<section class="module-hero">
    <div>
        <p class="eyebrow">Daily records</p>
        <h2>Attendance</h2>
        <p>Record student and staff attendance by date, status, and remarks for parent and management visibility.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="stat-grid">
    <article class="stat-card"><span>student records</span><strong><?= e($overview['student_total']) ?></strong><p>all saved student attendance entries</p></article>
    <article class="stat-card"><span>student present</span><strong><?= e($overview['student']['Present']) ?></strong><p>present student entries</p></article>
    <article class="stat-card"><span>staff records</span><strong><?= e($overview['staff_total']) ?></strong><p>all saved staff attendance entries</p></article>
    <article class="stat-card"><span>staff present</span><strong><?= e($overview['staff']['Present']) ?></strong><p>present staff entries</p></article>
</section>

<section class="exam-distribution-grid">
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Students</p><h3>Student Attendance Summary</h3></div></div>
        <div class="fee-breakdown">
            <?php foreach ($overview['student'] as $status => $total): ?><div><span><?= e($status) ?></span><strong><?= e($total) ?></strong></div><?php endforeach; ?>
        </div>
    </article>
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Staff</p><h3>Staff Attendance Summary</h3></div></div>
        <div class="fee-breakdown">
            <?php foreach ($overview['staff'] as $status => $total): ?><div><span><?= e($status) ?></span><strong><?= e($total) ?></strong></div><?php endforeach; ?>
        </div>
    </article>
</section>
