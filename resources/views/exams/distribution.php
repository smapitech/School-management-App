<?php include __DIR__ . '/nav.php'; ?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Assessment structure</p>
        <h2>Mark Distribution &amp; Grade Setup</h2>
        <p>Create assessment components such as test, exam, assignment, project, practical, and set the maximum score for each one.</p>
    </div>
</section>

<?php if (!empty($notice)): ?>
    <section class="alert-success" role="status"><?= e($notice) ?></section>
<?php endif; ?>

<?php if (can('exams', 'edit') && (($user['role'] ?? '') !== 'teacher')): ?>
    <section class="exam-distribution-grid">
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Distribution</p><h3><?= $editDistribution ? 'Edit Mark Type' : 'Add Mark Type' ?></h3></div></div>
            <form class="payroll-form" method="post" action="/exams/distribution/save">
                <input type="hidden" name="id" value="<?= e($editDistribution['id'] ?? '') ?>">
                <label><span>Name</span><input name="name" value="<?= e($editDistribution['name'] ?? '') ?>" placeholder="Test, Exam, Assignment" required></label>
                <label><span>Mark Obtainable</span><input type="number" step="0.01" name="max_mark" value="<?= e($editDistribution['max_mark'] ?? '') ?>" required></label>
                <label><span>Sort</span><input type="number" name="sort_order" value="<?= e($editDistribution['sort_order'] ?? 1) ?>"></label>
                <button type="submit"><?= $editDistribution ? 'Update' : 'Save' ?></button>
                <?php if ($editDistribution): ?><a class="secondary-action" href="/exams/distribution">Cancel</a><?php endif; ?>
            </form>
        </article>
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Grades</p><h3><?= $editGrade ? 'Edit Grade Range' : 'Add Grade Range' ?></h3></div></div>
            <form class="payroll-form exam-grade-form" method="post" action="/exams/distribution/grade-save">
                <input type="hidden" name="id" value="<?= e($editGrade['id'] ?? '') ?>">
                <label><span>Grade</span><input name="grade_name" value="<?= e($editGrade['grade_name'] ?? '') ?>" placeholder="A" required></label>
                <label><span>Min</span><input type="number" step="0.01" name="min_mark" value="<?= e($editGrade['min_mark'] ?? '') ?>" required></label>
                <label><span>Max</span><input type="number" step="0.01" name="max_mark" value="<?= e($editGrade['max_mark'] ?? '') ?>" required></label>
                <label><span>Remark</span><input name="remark" value="<?= e($editGrade['remark'] ?? '') ?>" placeholder="Excellent"></label>
                <button type="submit"><?= $editGrade ? 'Update' : 'Save' ?></button>
                <?php if ($editGrade): ?><a class="secondary-action" href="/exams/distribution">Cancel</a><?php endif; ?>
            </form>
        </article>
    </section>
<?php endif; ?>

<section class="exam-distribution-grid">
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">List</p><h3>Mark Types</h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Name</th><th>Mark Obtainable</th><th>Sort</th><th>Action</th></tr></thead><tbody>
            <?php foreach ($distributions as $row): ?>
                <tr>
                    <td><?= e($row['name']) ?></td>
                    <td><?= e($row['max_mark']) ?></td>
                    <td><?= e($row['sort_order']) ?></td>
                    <td class="row-actions">
                        <?php if (($user['role'] ?? '') !== 'teacher' && can('exams', 'edit')): ?><a href="/exams/distribution?edit_distribution=<?= e($row['id']) ?>">Edit</a><?php endif; ?>
                        <?php if (($user['role'] ?? '') !== 'teacher' && can('exams', 'delete')): ?><form method="post" action="/exams/distribution/delete"><input type="hidden" name="id" value="<?= e($row['id']) ?>"><button type="submit">Delete</button></form><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody></table></div>
    </article>
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">List</p><h3>Grade Ranges</h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Grade</th><th>Min</th><th>Max</th><th>Remark</th><th>Action</th></tr></thead><tbody>
            <?php foreach ($grades as $row): ?>
                <tr>
                    <td><?= e($row['grade_name']) ?></td>
                    <td><?= e($row['min_mark']) ?></td>
                    <td><?= e($row['max_mark']) ?></td>
                    <td><?= e($row['remark']) ?></td>
                    <td class="row-actions">
                        <?php if (($user['role'] ?? '') !== 'teacher' && can('exams', 'edit')): ?><a href="/exams/distribution?edit_grade=<?= e($row['id']) ?>">Edit</a><?php endif; ?>
                        <?php if (($user['role'] ?? '') !== 'teacher' && can('exams', 'delete')): ?><form method="post" action="/exams/distribution/grade-delete"><input type="hidden" name="id" value="<?= e($row['id']) ?>"><button type="submit">Delete</button></form><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody></table></div>
    </article>
</section>
