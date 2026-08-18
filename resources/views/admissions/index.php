<section class="module-hero">
    <div>
        <p class="eyebrow">Admissions module</p>
        <h2>Admissions</h2>
        <p>Create new admission records, upload student/guardian pictures, and filter admitted students by class.</p>
    </div>
    <div class="action-dropdown">
        <button type="button">Admission Actions</button>
        <div>
            <?php if (can('admissions', 'create')): ?>
                <a href="/admissions/create">Create Admission</a>
            <?php endif; ?>
            <a href="/admissions/admitted">Admitted Students</a>
        </div>
    </div>
</section>

<?php if (!empty($manageSuccess)): ?><section class="alert-success"><?= e($manageSuccess) ?></section><?php endif; ?>
<?php if (!empty($manageErrors)): ?><section class="alert-error"><?php foreach ($manageErrors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></section><?php endif; ?>

<section class="mini-stat-grid">
    <?php foreach ($stats as $label => $value): ?>
        <article>
            <span><?= e($label) ?></span>
            <strong><?= e($value) ?></strong>
        </article>
    <?php endforeach; ?>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Recent admissions</p>
            <h3>Latest admitted students</h3>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Reg No</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Term</th>
                    <th>Guardian</th>
                    <th>Admission Date</th>
                    <?php if (can('admissions', 'edit') || can('admissions', 'delete')): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e($row['registration_no'] ?: 'Pending') ?></td>
                        <td><?= e(trim(($row['first_name'] ?: $row['applicant']) . ' ' . $row['middle_name'] . ' ' . $row['last_name'])) ?></td>
                        <td><?= e($row['class_name']) ?></td>
                        <td><?= e($row['section']) ?></td>
                        <td><?= e($row['school_term'] ?? '') ?></td>
                        <td><?= e($row['guardian_full_name'] ?: $row['guardian']) ?></td>
                        <td><?= e($row['admission_date'] ?: $row['created_at']) ?></td>
                        <?php if (can('admissions', 'edit') || can('admissions', 'delete')): ?>
                            <td class="row-actions">
                                <?php if (can('admissions', 'edit')): ?>
                                    <a href="/admissions/create?edit=<?= e($row['id']) ?>&return_to=%2Fadmissions">Edit</a>
                                <?php endif; ?>
                                <?php if (can('admissions', 'delete')): ?>
                                    <form method="post" action="/admissions/delete" onsubmit="return confirm('Delete this admission record?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                        <input type="hidden" name="back" value="/admissions">
                                        <button type="submit">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="<?= (can('admissions', 'edit') || can('admissions', 'delete')) ? 8 : 7 ?>">No admissions yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
