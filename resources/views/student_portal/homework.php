<section class="module-hero">
    <div><p class="eyebrow">Homework</p><h2>My Homework</h2><p>Track new, pending, submitted, late, marked, missed, and upcoming class work.</p></div>
</section>
<?php require __DIR__ . '/nav.php'; ?>

<?php if ($student): ?>
<?php
    $counts = ['pending' => 0, 'submitted' => 0, 'late' => 0, 'marked' => 0, 'missed' => 0, 'resubmission_required' => 0];
    foreach ($assignments as $assignment) {
        $status = $assignment['submission_status'] ?: 'pending';
        $counts[$status] = ($counts[$status] ?? 0) + 1;
    }
?>
<section class="mini-stat-grid">
    <article><span>Pending</span><strong><?= e($counts['pending']) ?></strong></article>
    <article><span>Submitted</span><strong><?= e($counts['submitted']) ?></strong></article>
    <article><span>Marked</span><strong><?= e($counts['marked']) ?></strong></article>
    <article><span>Missed</span><strong><?= e($counts['missed']) ?></strong></article>
</section>

<section class="homework-grid">
    <?php foreach ($assignments as $assignment): ?>
        <?php $status = $assignment['submission_status'] ?: 'pending'; ?>
        <form class="homework-card student-homework-card" method="post" action="/student_portal/assignment/submit" enctype="multipart/form-data">
            <input type="hidden" name="assignment_id" value="<?= e($assignment['id']) ?>">
            <div>
                <span class="status"><?= e(str_replace('_', ' ', $status)) ?></span>
                <h3><?= e($assignment['title']) ?></h3>
                <p><?= e(($assignment['subject_name'] ?? '') ?: ($assignment['subject'] ?? '')) ?> | <?= e(trim(($assignment['teacher_name'] ?? '') . ' ' . ($assignment['teacher_surname'] ?? ''))) ?></p>
            </div>
            <div class="rich-content"><?= rich_text($assignment['description']) ?></div>
            <dl>
                <div><dt>Due</dt><dd><?= e($assignment['due_at'] ?: $assignment['due_date']) ?></dd></div>
                <div><dt>Marks</dt><dd><?= e($assignment['score'] ?? '-') ?> / <?= e($assignment['total_marks']) ?></dd></div>
                <div><dt>Type</dt><dd><?= e(str_replace('_', ' ', $assignment['submission_type'])) ?></dd></div>
            </dl>
            <?php if (!empty($assignment['attachment_path'])): ?><a href="/homework/download?path=<?= e($assignment['attachment_path']) ?>">Download teacher attachment</a><?php endif; ?>
            <?php if (!empty($assignment['resource_link'])): ?><a href="<?= e($assignment['resource_link']) ?>" target="_blank" rel="noopener">Open resource</a><?php endif; ?>
            <?php if (in_array($status, ['pending', 'submitted', 'late', 'resubmission_required'], true) && ($assignment['status'] ?? '') !== 'closed'): ?>
                <?php if (in_array($assignment['submission_type'], ['text', 'both'], true)): ?><textarea name="submission_text" rows="4" placeholder="Write your answer"><?= e($assignment['submission_text'] ?? '') ?></textarea><?php endif; ?>
                <?php if (in_array($assignment['submission_type'], ['file', 'both'], true)): ?><input type="file" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"><?php endif; ?>
                <?php if (($assignment['submission_type'] ?? '') === 'link'): ?><input type="url" name="submission_link" placeholder="Paste your submission link" value="<?= e($assignment['submission_link'] ?? '') ?>"><?php endif; ?>
                <button type="submit"><?= !empty($assignment['submission_id']) ? 'Update Submission' : 'Submit Homework' ?></button>
            <?php endif; ?>
            <?php if (!empty($assignment['feedback'])): ?><div class="feedback-box"><strong>Teacher Feedback</strong><p><?= e($assignment['feedback']) ?></p></div><?php endif; ?>
            <?php if (!empty($assignment['correction_file_path'])): ?><a href="/homework/download?path=<?= e($assignment['correction_file_path']) ?>">Download correction file</a><?php endif; ?>
            <?php if (!empty($assignment['submitted_at'])): ?><small>Submitted: <?= e($assignment['submitted_at']) ?> <?= !empty($assignment['is_late']) ? '| Late' : '' ?></small><?php endif; ?>
        </form>
    <?php endforeach; ?>
    <?php if (empty($assignments)): ?><section class="panel empty-state"><h3>No homework yet</h3><p>Your published class homework will appear here.</p></section><?php endif; ?>
</section>
<?php endif; ?>
