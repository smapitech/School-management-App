<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher payroll</p>
        <h2>Allowances</h2>
        <p>Review the allowance values attached to your own salary records only.</p>
    </div>
    <div class="hero-actions">
        <a class="primary-action" href="/teacher/payslips">My Payslips</a>
        <a class="secondary-action" href="/teacher/payroll">Payroll Overview</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="stat-grid">
    <article class="stat-card"><span>records</span><strong><?= e($totals['count'] ?? 0) ?></strong><p>payslips with allowance values</p></article>
    <article class="stat-card"><span>total allowance</span><strong><?= money($totals['amount'] ?? 0) ?></strong><p>combined allowance amount across your payslips</p></article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Allowance history</p>
            <h3>Your Allowance Records</h3>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pay Period</th>
                    <th>Allowance</th>
                    <th>Allowance Amount</th>
                    <th>Total Allowance</th>
                    <th>Basic Salary</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allowances as $row): ?>
                    <tr>
                        <td><?= e($row['pay_period']) ?></td>
                        <td><?= e($row['allowance_name']) ?></td>
                        <td><?= money($row['allowance_amount']) ?></td>
                        <td><?= money($row['total_allowance']) ?></td>
                        <td><?= money($row['basic_salary']) ?></td>
                        <td><span class="status"><?= e($row['payment_status']) ?></span></td>
                        <td><?= e($row['payment_date'] ?: '-') ?></td>
                        <td class="row-actions">
                            <a href="/teacher/payslips/view?id=<?= e($row['payslip_id']) ?>">View Payslip</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($allowances)): ?>
                    <tr><td colspan="8">No allowance records were found for your staff profile.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

