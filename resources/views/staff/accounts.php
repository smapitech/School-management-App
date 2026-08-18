<section class="module-hero">
    <div>
        <p class="eyebrow">Account management</p>
        <h2>Staff Login Accounts</h2>
        <p>Filter by role and staff ID, then update or delete staff login details.</p>
    </div>
    <div class="action-dropdown">
        <button type="button">Staff Actions</button>
        <div>
            <a href="/staff">Staff List</a>
            <?php if (can('staff', 'create')): ?><a href="/staff/add">Add Staff</a><?php endif; ?>
            <a href="/staff/designations">Designation</a>
        </div>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Filter</p>
            <h3>Find staff accounts</h3>
        </div>
    </div>
    <form class="student-search-form" method="get" action="/staff/accounts">
        <label>
            <span>Role</span>
            <select name="role">
                <option value="">All Roles</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= e($role) ?>" <?= $filters['role'] === $role ? 'selected' : '' ?>><?= e($role) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Staff ID</span>
            <input name="employee_no" value="<?= e($filters['employee_no']) ?>" placeholder="STF-2026">
        </label>
        <button type="submit">Filter</button>
        <a class="secondary-action" href="/staff/accounts">Reset</a>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Accounts</p>
            <h3>Login and password management</h3>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Username</th>
                    <th>New Password</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <form method="post" action="/staff/accounts/update">
                            <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                            <td><?= e($row['employee_no']) ?></td>
                            <td><?= e(trim($row['name'] . ' ' . $row['middle_name'] . ' ' . $row['surname'])) ?></td>
                            <td><?= e($row['role']) ?></td>
                            <td><input class="table-input" name="username" value="<?= e($row['username']) ?>"></td>
                            <td>
                                <input class="table-input" type="password" name="password" placeholder="New password">
                                <input class="table-input" type="password" name="password_confirmation" placeholder="Retype password">
                            </td>
                            <td class="row-actions">
                                <button type="submit">Edit</button>
                        </form>
                                <?php if (can('staff', 'delete')): ?>
                                    <form method="post" action="/staff/accounts/delete" onsubmit="return confirm('Delete this login account?');">
                                        <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="6">No staff accounts match this filter.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
