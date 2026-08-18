<section class="module-hero">
    <div>
        <p class="eyebrow">Class Assign</p>
        <h2>Assign Class Teacher</h2>
        <p>Assign a registered teacher to a class and section.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow"><?= $edit ? 'Edit' : 'Create' ?></p><h3>Class Assignment Form</h3></div></div>
    <form class="payroll-form" method="post" action="/classes/assign/save">
        <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
        <label><span>Class</span><select name="class_name" required><?php foreach ($classOptions as $class): ?><option value="<?= e($class) ?>" <?= (($edit['class_name'] ?? '') === $class) ? 'selected' : '' ?>><?= e($class) ?></option><?php endforeach; ?></select></label>
        <label><span>Section</span><select name="section" required><?php foreach ($sections as $section): ?><option value="<?= e($section) ?>" <?= (($edit['section'] ?? '') === $section) ? 'selected' : '' ?>><?= e($section) ?></option><?php endforeach; ?></select></label>
        <label><span>Teacher Name</span><select name="teacher_id" required><?php foreach ($teachers as $teacher): ?><option value="<?= e($teacher['id']) ?>" <?= ((int)($edit['teacher_id'] ?? 0) === (int)$teacher['id']) ? 'selected' : '' ?>><?= e(trim($teacher['name'] . ' ' . $teacher['middle_name'] . ' ' . $teacher['surname'])) ?></option><?php endforeach; ?></select></label>
        <label><span>Number of Students in Class</span><input type="number" name="number_of_students" value="<?= e($edit['number_of_students'] ?? 0) ?>"></label>
        <button type="submit"><?= $edit ? 'Update' : 'Assign' ?></button>
    </form>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Class teachers list</p><h3>Assigned Class Teachers</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Class</th><th>Section</th><th>Assigned Teacher</th><th>Students</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($assignments as $row): ?>
                    <tr>
                        <td><?= e($row['class_name']) ?></td>
                        <td><?= e($row['section']) ?></td>
                        <td><?= e(trim($row['name'] . ' ' . $row['middle_name'] . ' ' . $row['surname'])) ?></td>
                        <td><?= e($row['number_of_students']) ?></td>
                        <td class="row-actions">
                            <a href="/classes/assign?edit=<?= e($row['id']) ?>">Edit</a>
                            <?php if (can('classes', 'delete')): ?><form method="post" action="/classes/assign/delete" onsubmit="return confirm('Delete this class assignment?');"><input type="hidden" name="id" value="<?= e($row['id']) ?>"><button type="submit">Delete</button></form><?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($assignments)): ?><tr><td colspan="5">No class teacher assignment yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
