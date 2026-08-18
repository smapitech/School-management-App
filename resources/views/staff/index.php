<section class="module-hero">
    <div>
        <p class="eyebrow">Staff directory</p>
        <h2><?= !empty($isTeacherDirectory) ? 'Teachers List' : 'Staff Management' ?></h2>
        <p><?= !empty($isTeacherDirectory) ? 'View teachers, administration staff, designations, email addresses, and phone numbers.' : 'Browse staff by role, manage profiles, and open staff account tools.' ?></p>
    </div>
    <?php if (empty($isTeacherDirectory)): ?><div class="action-dropdown">
        <button type="button">Staff Actions</button>
        <div>
            <?php if (can('staff', 'create')): ?><a href="/staff/add">Add Staff</a><?php endif; ?>
            <?php if (can('staff', 'edit')): ?><a href="/staff/designations">Designation</a><?php endif; ?>
            <?php if (can('staff', 'edit')): ?><a href="/staff/accounts">Account Management</a><?php endif; ?>
        </div>
    </div><?php endif; ?>
</section>

<?php if (empty($isTeacherDirectory)): ?><section class="role-tabs">
    <?php foreach ($roles as $role): ?>
        <a class="<?= $selectedRole === $role ? 'is-active' : '' ?>" href="/staff?role=<?= e(urlencode($role)) ?>"><?= e($role) ?></a>
    <?php endforeach; ?>
</section><?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow"><?= e($selectedRole) ?></p>
            <h3>Staff List</h3>
        </div>
    </div>
    <div class="table-wrap">
        <table class="staff-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Staff ID</th>
                    <th>Full Name</th>
                    <th>Designation</th>
                    <th>Email</th>
                    <th>Mobile No</th>
                    <?php if (can('staff', 'edit') || can('staff', 'delete')): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <?php $fullName = trim($row['name'] . ' ' . $row['middle_name'] . ' ' . $row['surname']); ?>
                    <tr>
                        <td>
                            <?php if (!empty($row['staff_photo'])): ?>
                                <img class="student-photo" src="<?= e($row['staff_photo']) ?>" alt="<?= e($fullName) ?>">
                            <?php else: ?>
                                <span class="student-photo placeholder"><?= e(strtoupper(substr($row['name'], 0, 1) . substr($row['surname'] ?: $row['name'], 0, 1))) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($row['employee_no']) ?></td>
                        <td><?= e($fullName) ?></td>
                        <td><?= e($row['designation'] ?: $row['role']) ?></td>
                        <td><?= e($row['email']) ?></td>
                        <td><?= e($row['mobile_no']) ?></td>
                        <?php if (can('staff', 'edit') || can('staff', 'delete')): ?>
                            <td class="row-actions">
                                <?php if (can('staff', 'edit')): ?><a href="/staff/edit?id=<?= e($row['id']) ?>">Edit</a><?php endif; ?>
                                <?php if (can('staff', 'delete')): ?>
                                    <form method="post" action="/staff/delete" onsubmit="return confirm('Delete this staff member?');">
                                        <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="<?= can('staff', 'edit') || can('staff', 'delete') ? '7' : '6' ?>">No staff found for this role.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
