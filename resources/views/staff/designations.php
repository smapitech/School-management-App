<section class="module-hero">
    <div>
        <p class="eyebrow">Designation</p>
        <h2>Designation Management</h2>
        <p>Create and maintain staff designation names such as Principal, Teacher, and Accountant.</p>
    </div>
    <div class="action-dropdown">
        <button type="button">Staff Actions</button>
        <div>
            <a href="/staff">Staff List</a>
            <?php if (can('staff', 'create')): ?><a href="/staff/add">Add Staff</a><?php endif; ?>
            <a href="/staff/accounts">Account Management</a>
        </div>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow"><?= $edit ? 'Edit' : 'Create' ?></p>
            <h3><?= $edit ? 'Edit Designation' : 'Create Designation Name' ?></h3>
        </div>
    </div>
    <form class="filter-form" method="post" action="/staff/designations/save">
        <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
        <label>
            <span>Designation Name</span>
            <input name="name" value="<?= e($edit['name'] ?? '') ?>" placeholder="Principal" required>
        </label>
        <button type="submit"><?= $edit ? 'Update' : 'Create' ?></button>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Designation list</p>
            <h3>Created Designations</h3>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($designations as $designation): ?>
                    <tr>
                        <td><?= e($designation['name']) ?></td>
                        <td><?= e($designation['created_at']) ?></td>
                        <td class="row-actions">
                            <?php if (can('staff', 'edit')): ?><a href="/staff/designations?edit=<?= e($designation['id']) ?>">Edit</a><?php endif; ?>
                            <?php if (can('staff', 'delete')): ?>
                                <form method="post" action="/staff/designations/delete" onsubmit="return confirm('Delete this designation?');">
                                    <input type="hidden" name="id" value="<?= e($designation['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
