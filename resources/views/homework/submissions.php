<section class="module-hero">
    <div><p class="eyebrow">Homework Portal</p><h2>Marking Dashboard</h2><p>Review submissions, download student files, score work, request corrections, and keep private teacher notes.</p></div>
</section>
<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <form class="filter-form" method="get" action="/homework/submissions">
        <label><span>Homework</span><select name="homework_id"><?php foreach ($homeworks as $row): ?><option value="<?= e($row['id']) ?>" <?= (int) ($selected['id'] ?? 0) === (int) $row['id'] ? 'selected' : '' ?>><?= e($row['title']) ?> - <?= e($row['class_name']) ?> <?= e($row['section']) ?></option><?php endforeach; ?></select></label>
        <label><span>Status</span><select name="status"><option value="">All</option><?php foreach (['pending', 'submitted', 'late', 'marked', 'resubmission_required', 'missed'] as $item): ?><option value="<?= e($item) ?>" <?= $status === $item ? 'selected' : '' ?>><?= e(str_replace('_', ' ', ucfirst($item))) ?></option><?php endforeach; ?></select></label>
        <button type="submit">Load Submissions</button>
    </form>
</section>

<?php if ($selected): ?>
<section class="panel">
    <div class="panel-header"><div><p class="eyebrow"><?= e($selected['class_name']) ?> <?= e($selected['section']) ?></p><h3><?= e($selected['title']) ?></h3></div><span class="status"><?= e(ucfirst($selected['status'])) ?></span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Student</th><th>Register No</th><th>Status</th><th>Submitted</th><th>Answer</th><th>Score</th><th>Feedback</th><th>Marking</th></tr></thead>
            <tbody>
                <?php foreach ($submissions as $row): ?>
                    <?php $name = trim(($row['first_name'] ?: $row['applicant']) . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?>
                    <tr>
                        <td><div class="person-cell"><?php if (!empty($row['profile_picture'])): ?><img src="<?= e($row['profile_picture']) ?>" alt="<?= e($name) ?>"><?php endif; ?><span><?= e($name) ?></span></div></td>
                        <td><?= e($row['registration_no']) ?></td>
                        <td><span class="status"><?= e(str_replace('_', ' ', $row['status'])) ?></span></td>
                        <td><?= e($row['submitted_at']) ?></td>
                        <td>
                            <?php if (!empty($row['submission_text'])): ?><p><?= e(substr($row['submission_text'], 0, 120)) ?></p><?php endif; ?>
                            <?php if (!empty($row['attachment_path'])): ?><a href="/homework/download?path=<?= e($row['attachment_path']) ?>"><?= e($row['attachment_name'] ?: 'Download file') ?></a><?php endif; ?>
                            <?php if (!empty($row['submission_link'])): ?><a href="<?= e($row['submission_link']) ?>" target="_blank" rel="noopener">Open link</a><?php endif; ?>
                        </td>
                        <td><?= $row['score'] !== null ? e($row['score']) . ' / ' . e($selected['total_marks']) : '-' ?></td>
                        <td><?= e($row['feedback']) ?></td>
                        <td>
                            <?php if ((int) ($row['id'] ?? 0) > 0): ?>
                                <form class="marking-form" method="post" action="/homework/mark" enctype="multipart/form-data">
                                    <input type="hidden" name="submission_id" value="<?= e($row['id']) ?>">
                                    <input type="number" name="score" min="0" max="<?= e($selected['total_marks']) ?>" step="0.01" value="<?= e($row['score'] ?? 0) ?>">
                                    <select name="status"><option value="marked">Marked</option><option value="resubmission_required" <?= $row['status'] === 'resubmission_required' ? 'selected' : '' ?>>Resubmission Required</option></select>
                                    <textarea name="feedback" rows="2" placeholder="Feedback"><?= e($row['feedback']) ?></textarea>
                                    <textarea name="private_note" rows="2" placeholder="Private note"><?= e($row['private_note']) ?></textarea>
                                    <input type="file" name="correction_attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <button type="submit">Save Mark</button>
                                </form>
                            <?php else: ?>
                                <span class="muted">No submission yet.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($submissions)): ?><tr><td colspan="8">No submission record found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php else: ?>
    <section class="panel empty-state"><h3>No homework selected</h3><p>Create or publish homework first.</p></section>
<?php endif; ?>
