<section class="module-hero">
    <div>
        <p class="eyebrow module-eyebrow"><?= module_icon($module['icon']) ?> module</p>
        <h2><?= e($module['title']) ?></h2>
        <p><?= e($module['description']) ?></p>
    </div>
</section>

<?php if (!empty($stats)): ?>
    <section class="mini-stat-grid">
        <?php foreach ($stats as $label => $value): ?>
            <article>
                <span><?= e($label) ?></span>
                <strong><?= is_numeric($value) && $value > 999 ? e(number_format((float) $value)) : e($value) ?></strong>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php if (!empty($module['create']) && can($moduleKey, 'create')): ?>
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Quick create</p>
                <h3>Add <?= e(rtrim($module['title'], 's')) ?></h3>
            </div>
        </div>
        <form class="inline-form" method="post" action="<?= e($module['create']) ?>">
            <?php foreach ($module['fields'] as $name => $label): ?>
                <label>
                    <span><?= e($label) ?></span>
                    <input name="<?= e($name) ?>" placeholder="<?= e($label) ?>" <?= in_array($name, ['name', 'student_no', 'applicant', 'invoice_no', 'student_name'], true) ? 'required' : '' ?>>
                </label>
            <?php endforeach; ?>
            <button type="submit">Save</button>
        </form>
    </section>
<?php endif; ?>

<?php if (!empty($module['columns'])): ?>
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Records</p>
                <h3><?= e($module['title']) ?> List</h3>
            </div>
            <button class="secondary-action" type="button">Export</button>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <?php foreach ($module['columns'] as $label): ?>
                            <th><?= e($label) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach (array_keys($module['columns']) as $field): ?>
                                <td>
                                    <?php if ($field === 'amount'): ?>
                                        <?= money($row[$field] ?? 0) ?>
                                    <?php elseif (in_array($field, ['status', 'stage'], true)): ?>
                                        <span class="status"><?= e($row[$field] ?? '') ?></span>
                                    <?php else: ?>
                                        <?= e($row[$field] ?? '') ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= count($module['columns']) ?>">No records yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php else: ?>
    <section class="panel empty-state">
        <h3><?= e($module['title']) ?> workspace</h3>
        <p>This module is reserved for deeper configuration screens, filters, exports, and approval workflows.</p>
    </section>
<?php endif; ?>
