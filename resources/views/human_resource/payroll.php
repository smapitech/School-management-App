<section class="module-hero">
    <div>
        <p class="eyebrow">Payroll</p>
        <h2>Create Salary Template</h2>
        <p>Create payroll salary templates with allowances, deductions, gross pay, and net salary.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Template form</p><h3>Create Salary</h3></div></div>
    <form class="payroll-form" method="post" action="/human-resource/payroll/template">
        <label>
            <span>Staff</span>
            <select name="staff_id" required>
                <?php foreach ($staff as $person): ?>
                    <option value="<?= e($person['id']) ?>"><?= e(trim($person['name'] . ' ' . $person['surname']) . ' - ' . $person['role']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label><span>Basic Salary</span><input type="number" step="0.01" name="basic_salary" required></label>
        <label><span>Overtime</span><input type="number" step="0.01" name="overtime" value="0"></label>
        <label><span>Bonus</span><input type="number" step="0.01" name="bonus" value="0"></label>
        <label><span>Allowance Name</span><input name="allowance_name" placeholder="Transport"></label>
        <label><span>Allowance Amount</span><input type="number" step="0.01" name="allowance_amount" value="0"></label>
        <label><span>Total Allowance</span><input type="number" step="0.01" name="total_allowance" value="0"></label>
        <label><span>Total Deduction</span><input type="number" step="0.01" name="total_deduction" value="0"></label>
        <button type="submit">Create Template</button>
    </form>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Template list</p><h3>All Staff To Be Paid</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Staff</th><th>Role</th><th>Designation</th><th>Basic Salary</th><th>Gross Pay</th><th>Total Salary</th></tr></thead>
            <tbody>
                <?php foreach ($templates as $row): ?>
                    <tr>
                        <td><?= e(trim($row['name'] . ' ' . $row['middle_name'] . ' ' . $row['surname'])) ?></td>
                        <td><?= e($row['role']) ?></td>
                        <td><?= e($row['designation']) ?></td>
                        <td><?= money($row['basic_salary']) ?></td>
                        <td><?= money($row['gross_pay']) ?></td>
                        <td><?= money($row['net_salary']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($templates)): ?><tr><td colspan="6">No salary template has been created.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
