<section class="module-hero"><div><p class="eyebrow">Teachers list</p><h2>Teachers and School Staff</h2><p>View academic and administrative staff contact details published for students.</p></div></section>
<?php require __DIR__ . '/nav.php'; ?>
<?php if ($student): ?>
<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Directory</p><h3>Teachers List</h3></div></div>
    <div class="table-wrap"><table><thead><tr><th>Photo</th><th>Staff ID</th><th>Name</th><th>Designation</th><th>Email</th><th>Phone</th></tr></thead><tbody>
        <?php foreach ($teachers as $teacher): ?>
            <?php $name = trim($teacher['name'] . ' ' . ($teacher['middle_name'] ?? '') . ' ' . ($teacher['surname'] ?? '')); ?>
            <tr>
                <td><?php if (!empty($teacher['staff_photo'])): ?><img class="student-photo" src="<?= e($teacher['staff_photo']) ?>" alt="<?= e($name) ?>"><?php else: ?><span class="student-photo placeholder"><?= e(strtoupper(substr($name, 0, 2))) ?></span><?php endif; ?></td>
                <td><?= e($teacher['employee_no']) ?></td>
                <td><?= e($name) ?></td>
                <td><?= e($teacher['designation'] ?: $teacher['role']) ?></td>
                <td><?= e($teacher['email']) ?></td>
                <td><?= e($teacher['mobile_no']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($teachers)): ?><tr><td colspan="6">No staff directory has been published yet.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?php endif; ?>
