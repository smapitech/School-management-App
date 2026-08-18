<section class="module-hero">
    <div>
        <p class="eyebrow">Subject Form</p>
        <h2>Manage Subjects</h2>
        <p>Create, update, and remove subjects before assigning them to classes.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php $isEditing = !empty($form['id']); ?>

<?php if (!empty($success)): ?>
    <section class="alert-success"><p><?= e($success) ?></p></section>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <section class="alert-error"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow"><?= $isEditing ? 'Edit' : 'Create' ?></p>
            <h3><?= $isEditing ? 'Edit Subject' : 'Create Subject' ?></h3>
        </div>
    </div>

    <form class="payroll-form" method="post" action="/classes/subjects/save">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= e($form['id'] ?? 0) ?>">
        <label>
            <span>Subject Name</span>
            <input name="subject_name" value="<?= e($form['subject_name'] ?? '') ?>" placeholder="Mathematics" required>
        </label>
        <label>
            <span>Subject Type</span>
            <input name="subject_type" value="<?= e($form['subject_type'] ?? '') ?>" placeholder="Core / Elective" required>
        </label>
        <label>
            <span>Subject Code</span>
            <input name="subject_code" value="<?= e($form['subject_code'] ?? '') ?>" placeholder="MTH101" required>
        </label>
        <button type="submit"><?= $isEditing ? 'Update Subject' : 'Create Subject' ?></button>
        <?php if ($isEditing): ?>
            <a class="secondary-action" href="/classes/subjects">Cancel</a>
        <?php endif; ?>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Subjects</p>
            <h3>Subject List</h3>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Subject Type</th>
                    <th>Subject Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><?= e($subject['subject_name']) ?></td>
                        <td><?= e($subject['subject_type']) ?></td>
                        <td><?= e($subject['subject_code']) ?></td>
                        <td class="row-actions">
                            <?php if (can('classes', 'edit')): ?>
                                <a href="/classes/subjects?edit=<?= e($subject['id']) ?>">Edit</a>
                                <form method="post" action="/classes/subjects/delete" onsubmit="return confirm('Delete this subject?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($subject['id']) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($subjects)): ?>
                    <tr><td colspan="4">No subject created yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
