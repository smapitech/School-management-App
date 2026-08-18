<section class="module-hero">
    <div>
        <p class="eyebrow">Salary Assign</p>
        <h2>Assign Or Reassign Salary</h2>
        <p>Use this when a staff member changes role/designation or receives a promotion.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Assignment form</p><h3>Assign Salary Template</h3></div></div>
    <form class="payroll-form" method="post" action="/human-resource/salary-assign/save">
        <label>
            <span>Staff</span>
            <select name="staff_id" required>
                <?php foreach ($staff as $person): ?>
                    <option value="<?= e($person['id']) ?>"><?= e($person['employee_no'] . ' - ' . trim($person['name'] . ' ' . $person['surname']) . ' (' . $person['role'] . ')') ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Salary Template</span>
            <select name="template_id" required>
                <?php foreach ($templates as $template): ?>
                    <option value="<?= e($template['id']) ?>"><?= e(trim($template['name'] . ' ' . $template['surname']) . ' - ' . money($template['net_salary'])) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label><span>Assigned Date</span><input type="date" name="assigned_date" value="<?= e(date('Y-m-d')) ?>"></label>
        <label><span>Note</span><input name="note" placeholder="Promotion, reassignment, etc."></label>
        <button type="submit">Assign Salary</button>
    </form>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Assignments</p><h3>Salary Assignment List</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Staff</th><th>Role</th><th>Designation</th><th>Net Salary</th><th>Date</th><th>Note</th></tr></thead>
            <tbody>
                <?php foreach ($assignments as $row): ?>
                    <tr><td><?= e($row['employee_no'] . ' - ' . trim($row['name'] . ' ' . $row['surname'])) ?></td><td><?= e($row['role']) ?></td><td><?= e($row['designation']) ?></td><td><?= money($row['net_salary']) ?></td><td><?= e($row['assigned_date']) ?></td><td><?= e($row['note']) ?></td></tr>
                <?php endforeach; ?>
                <?php if (empty($assignments)): ?><tr><td colspan="6">No salary assignment yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
