<section class="module-hero">
    <div>
        <p class="eyebrow">Staff attendance</p>
        <h2>Staff Attendance</h2>
        <p>Filter staff by role and date, then record attendance status and daily remarks.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Filter</p><h3>Select Role and Date</h3></div></div>
    <form class="student-search-form" method="get" action="/attendance/staff">
        <label><span>Role</span><select name="role" required><option value="">Role</option><?php foreach ($roles as $role): ?><option value="<?= e($role) ?>" <?= $filters['role'] === $role ? 'selected' : '' ?>><?= e($role) ?></option><?php endforeach; ?></select></label>
        <label><span>Date</span><input type="date" name="attendance_date" value="<?= e($filters['attendance_date']) ?>" required></label>
        <button type="submit">Load Staff List</button>
    </form>
</section>

<?php if ($loaded): ?>
    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow"><?= e($filters['attendance_date']) ?></p><h3><?= e($filters['role']) ?> Staff List</h3></div></div>
        <form method="post" action="/attendance/staff/save">
            <input type="hidden" name="role" value="<?= e($filters['role']) ?>">
            <input type="hidden" name="attendance_date" value="<?= e($filters['attendance_date']) ?>">
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Name</th><th>Staff ID</th><th>Status</th><th>Remark</th></tr></thead>
                    <tbody>
                        <?php foreach ($staff as $person): ?>
                            <?php
                                $staffId = (int) $person['id'];
                                $fullName = trim($person['name'] . ' ' . $person['middle_name'] . ' ' . $person['surname']);
                                $record = $records[$staffId] ?? [];
                                $selected = $record['status'] ?? 'Present';
                            ?>
                            <tr>
                                <td><strong><?= e($fullName) ?></strong><small><?= e($person['designation'] ?: $person['role']) ?></small></td>
                                <td><?= e($person['employee_no']) ?></td>
                                <td class="attendance-options">
                                    <?php foreach ($statuses as $status): ?>
                                        <label><input type="radio" name="attendance[<?= e($staffId) ?>][status]" value="<?= e($status) ?>" <?= $selected === $status ? 'checked' : '' ?>> <?= e($status) ?></label>
                                    <?php endforeach; ?>
                                </td>
                                <td><input class="table-input attendance-remark" name="attendance[<?= e($staffId) ?>][remark]" value="<?= e($record['remark'] ?? '') ?>" placeholder="Remark"></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($staff)): ?><tr><td colspan="4">No staff found for this role.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (can('attendance', 'edit')): ?><div class="form-actions"><button type="submit">Save Staff Attendance</button></div><?php endif; ?>
        </form>
    </section>
<?php endif; ?>
