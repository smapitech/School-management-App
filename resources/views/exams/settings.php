<?php include __DIR__ . '/nav.php'; ?>

<section class="module-hero">
    <div>
        <p class="eyebrow">System records</p>
        <h2>Configured Exam Records</h2>
        <p>These hidden records are created automatically when marks are entered. Superadmin can review them here for debugging and reconciliation.</p>
    </div>
</section>

<?php if (!empty($notice)): ?>
    <section class="alert-success" role="status"><?= e($notice) ?></section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">List</p>
            <h3>Configured Exam Records</h3>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Record</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Term</th>
                    <th>Session</th>
                    <th>Subject</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($settings as $row): ?>
                    <tr>
                        <td><?= e($row['exam_name']) ?></td>
                        <td><?= e($row['class_name']) ?></td>
                        <td><?= e($row['section']) ?></td>
                        <td><?= e($row['school_term']) ?></td>
                        <td><?= e($row['school_session']) ?></td>
                        <td><?= e($row['subject_name']) ?></td>
                        <td><?= e($row['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($settings)): ?>
                    <tr><td colspan="7">No configured exam records are available yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
