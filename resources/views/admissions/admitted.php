<section class="module-hero">
    <div>
        <p class="eyebrow">Admitted students</p>
        <h2>Class Admission List</h2>
        <p>Select a class to reveal the admitted student names and class details.</p>
    </div>
    <div class="action-dropdown">
        <button type="button">Admission Actions</button>
        <div>
            <a href="/admissions">Admission Overview</a>
            <?php if (can('admissions', 'create')): ?>
                <a href="/admissions/create">Create Admission</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($manageSuccess)): ?><section class="alert-success"><?= e($manageSuccess) ?></section><?php endif; ?>
<?php if (!empty($manageErrors)): ?><section class="alert-error"><?php foreach ($manageErrors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></section><?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Filter</p>
            <h3>Find admitted students by class</h3>
        </div>
    </div>
    <form class="filter-form" method="get" action="/admissions/admitted">
        <label>
            <span>Class</span>
            <select name="class_name" required>
                <option value="">Select Class</option>
                <?php foreach ($classOptions as $class): ?>
                    <option value="<?= e($class) ?>" <?= $selectedClass === $class ? 'selected' : '' ?>><?= e($class) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Show Students</button>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Results</p>
            <h3><?= $selectedClass ? e($selectedClass) . ' admitted students' : 'Select a class' ?></h3>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Register No</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Term</th>
                    <th>Gender</th>
                    <th>Guardian</th>
                    <th>Mobile</th>
                    <?php if (can('admissions', 'edit') || can('admissions', 'delete')): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e($row['registration_no']) ?></td>
                        <td><?= e(trim(($row['first_name'] ?: $row['applicant']) . ' ' . $row['middle_name'] . ' ' . $row['last_name'])) ?></td>
                        <td><?= e($row['class_name']) ?></td>
                        <td><?= e($row['section']) ?></td>
                        <td><?= e($row['school_term'] ?? '') ?></td>
                        <td><?= e($row['gender']) ?></td>
                        <td><?= e($row['guardian_full_name'] ?: $row['guardian']) ?></td>
                        <td><?= e($row['guardian_mobile']) ?></td>
                        <?php if (can('admissions', 'edit') || can('admissions', 'delete')): ?>
                            <td class="row-actions">
                                <?php if (can('admissions', 'edit')): ?>
                                    <a href="/admissions/create?edit=<?= e($row['id']) ?>&return_to=<?= e(urlencode('/admissions/admitted' . ($selectedClass ? '?class_name=' . urlencode($selectedClass) : ''))) ?>">Edit</a>
                                <?php endif; ?>
                                <?php if (can('admissions', 'delete')): ?>
                                    <form method="post" action="/admissions/delete" onsubmit="return confirm('Delete this admission record?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                        <input type="hidden" name="back" value="<?= e('/admissions/admitted' . ($selectedClass ? '?class_name=' . urlencode($selectedClass) : '')) ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                <?php if ($selectedClass && empty($rows)): ?>
                    <tr>
                        <td colspan="<?= (can('admissions', 'edit') || can('admissions', 'delete')) ? 9 : 8 ?>">No admitted students found for this class.</td>
                    </tr>
                <?php endif; ?>
                <?php if (!$selectedClass): ?>
                    <tr>
                        <td colspan="<?= (can('admissions', 'edit') || can('admissions', 'delete')) ? 9 : 8 ?>">Use the class filter above to show admitted students.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
