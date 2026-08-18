<section class="module-hero"><div><p class="eyebrow">Report</p><h2>Report Card and Certificates</h2><p>View printable report card outcomes and download certificates when available.</p></div><?php if ($student): ?><a class="secondary-action" href="/student_portal/report-card">View Report Card</a><?php endif; ?></section>
<?php require __DIR__ . '/nav.php'; ?>
<?php if ($student): ?>
<section class="student-portal-grid">
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Report card</p><h3>Exam Outcome</h3></div><a href="/student_portal/report-card">Print</a></div>
        <div class="table-wrap"><table><thead><tr><th>Subject</th><th>Score</th><th>Obtainable</th><th>Grade</th></tr></thead><tbody>
            <?php foreach ($results as $row): ?><tr><td><?= e($row['subject_name']) ?></td><td><?= e($row['student_score']) ?></td><td><?= e($row['total_mark']) ?></td><td><?= e($row['grade']) ?></td></tr><?php endforeach; ?>
            <?php if (empty($results)): ?><tr><td colspan="4">No report card result has been published yet.</td></tr><?php endif; ?>
        </tbody></table></div>
    </article>
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Certificates</p><h3>Downloads</h3></div></div>
        <div class="message-list">
            <?php foreach ($certificates as $certificate): ?><article><span><?= e($certificate['issued_at']) ?></span><strong><?= e($certificate['title']) ?></strong><p>Status: <?= e($certificate['status']) ?></p><a class="secondary-action" href="<?= e($certificate['download']) ?>">Download</a></article><?php endforeach; ?>
            <?php if (empty($certificates)): ?><p class="muted">No certificate is available for download yet.</p><?php endif; ?>
        </div>
    </article>
</section>
<?php endif; ?>
