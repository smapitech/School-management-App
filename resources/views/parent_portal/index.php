<section class="module-hero">
    <div>
        <p class="eyebrow">Parent workspace</p>
        <h2>Parent Portal</h2>
        <p>Monitor children, attendance remarks, fee records, results, assignments, and school communication.</p>
    </div>
</section>

<section class="stat-grid">
    <article class="stat-card"><span>children</span><strong><?= e(count($children)) ?></strong><p>linked student profiles</p></article>
    <article class="stat-card"><span>messages</span><strong><?= e(count($messages)) ?></strong><p>school communication</p></article>
    <article class="stat-card"><span>attendance</span><strong><?= e(array_sum(array_map(fn ($child) => count($child['attendance']), $children))) ?></strong><p>recent child records</p></article>
    <article class="stat-card"><span>assignments</span><strong><?= e(array_sum(array_map(fn ($child) => count($child['assignments']), $children))) ?></strong><p>class work to monitor</p></article>
</section>

<?php if (empty($children)): ?>
    <section class="panel empty-state"><h3>No child profile linked</h3><p>Ask the admin to connect your login to your child's admission record.</p></section>
<?php endif; ?>

<?php foreach ($children as $bundle): ?>
    <?php
        $student = $bundle['student'];
        $fullName = trim(($student['first_name'] ?: $student['applicant']) . ' ' . $student['middle_name'] . ' ' . $student['last_name']);
        $initials = strtoupper(substr($student['first_name'] ?: $student['applicant'], 0, 1) . substr($student['last_name'] ?: $student['applicant'], 0, 1));
    ?>
    <section class="student-portal-hero">
        <div>
            <?php if (!empty($student['profile_picture'])): ?><img class="portal-avatar" src="<?= e($student['profile_picture']) ?>" alt="<?= e($fullName) ?>"><?php else: ?><span class="portal-avatar placeholder"><?= e($initials) ?></span><?php endif; ?>
            <div>
                <p class="eyebrow">Child profile</p>
                <h2><?= e($fullName) ?></h2>
                <p><?= e($student['registration_no']) ?> | <?= e($student['class_name']) ?> <?= e($student['section']) ?></p>
            </div>
        </div>
        <a class="secondary-action" href="/parent_portal/report-card?student_id=<?= e($student['id']) ?>">Print Report Card</a>
    </section>

    <section class="student-portal-grid">
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Results</p><h3><?= e($fullName) ?> Scores</h3></div></div>
            <div class="table-wrap"><table><thead><tr><th>Subject</th><th>Score</th><th>Obtainable</th><th>Grade</th></tr></thead><tbody>
                <?php foreach ($bundle['results'] as $row): ?><tr><td><?= e($row['subject_name']) ?></td><td><?= e($row['student_score']) ?></td><td><?= e($row['total_mark']) ?></td><td><?= e($row['grade']) ?></td></tr><?php endforeach; ?>
                <?php if (empty($bundle['results'])): ?><tr><td colspan="4">No result has been published yet.</td></tr><?php endif; ?>
            </tbody></table></div>
        </article>
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Attendance</p><h3>Attendance Remarks</h3></div></div>
            <div class="table-wrap"><table><thead><tr><th>Date</th><th>Status</th><th>Remark</th></tr></thead><tbody>
                <?php foreach ($bundle['attendance'] as $row): ?><tr><td><?= e($row['attendance_date']) ?></td><td><span class="status"><?= e($row['status']) ?></span></td><td><?= e($row['remark']) ?></td></tr><?php endforeach; ?>
                <?php if (empty($bundle['attendance'])): ?><tr><td colspan="3">No attendance record is available yet.</td></tr><?php endif; ?>
            </tbody></table></div>
        </article>
    </section>

    <section class="student-portal-grid">
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Assignments</p><h3>Class Work Monitoring</h3></div></div>
            <div class="message-list">
                <?php foreach ($bundle['assignments'] as $assignment): ?><article><span><?= e($assignment['subject']) ?> | Due <?= e($assignment['due_at'] ?: $assignment['due_date']) ?></span><strong><?= e($assignment['title']) ?></strong><div class="rich-content"><?= rich_text($assignment['description']) ?></div><small>Status: <?= e($assignment['submission_status'] ?: 'Not submitted') ?><?php if (($assignment['score'] ?? null) !== null): ?> | Score: <?= e($assignment['score']) ?>/<?= e($assignment['total_marks']) ?><?php endif; ?></small><?php if (!empty($assignment['feedback'])): ?><p><strong>Feedback:</strong> <?= e($assignment['feedback']) ?></p><?php endif; ?></article><?php endforeach; ?>
                <?php if (empty($bundle['assignments'])): ?><p class="muted">No assignment has been posted for this child.</p><?php endif; ?>
            </div>
        </article>
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Fees</p><h3>Fee Records</h3></div></div>
            <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Term</th><th>Amount</th><th>Status</th></tr></thead><tbody>
                <?php foreach ($bundle['fees'] as $fee): ?><tr><td><?= e($fee['invoice_no']) ?></td><td><?= e($fee['school_term']) ?></td><td><?= money($fee['amount']) ?></td><td><span class="status"><?= e($fee['status']) ?></span></td></tr><?php endforeach; ?>
                <?php if (empty($bundle['fees'])): ?><tr><td colspan="4">No fee record has been created.</td></tr><?php endif; ?>
            </tbody></table></div>
        </article>
    </section>
<?php endforeach; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Communication</p><h3>School Messages</h3></div></div>
    <div class="message-list">
        <?php foreach ($messages as $message): ?><article><span><?= e($message['channel']) ?> | <?= e($message['created_at']) ?></span><strong><?= e($message['subject']) ?></strong><p><?= e(substr(strip_tags($message['message']), 0, 180)) ?></p></article><?php endforeach; ?>
        <?php if (empty($messages)): ?><p class="muted">No parent message is available yet.</p><?php endif; ?>
    </div>
</section>
