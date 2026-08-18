<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher payroll</p>
        <h2>My Payslips</h2>
        <p>Review only your own payslips. This page never exposes another staff member's salary records.</p>
    </div>
    <div class="hero-actions">
        <a class="primary-action" href="/teacher/payroll">Payroll Overview</a>
        <a class="secondary-action" href="/teacher/my-attendance">My Staff Attendance</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="stat-grid">
    <article class="stat-card"><span>records</span><strong><?= e(count($payslips ?? [])) ?></strong><p>payslips available in your history</p></article>
    <article class="stat-card"><span>latest pay period</span><strong><?= e($latestPayslip['pay_period'] ?? '-') ?></strong><p>most recent salary period</p></article>
    <article class="stat-card"><span>latest status</span><strong><?= e($latestPayslip['payment_status'] ?? '-') ?></strong><p>payment status for your latest payslip</p></article>
    <article class="stat-card"><span>latest net pay</span><strong><?= money($latestPayslip['net_pay'] ?? 0) ?></strong><p>most recent net salary amount</p></article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Salary history</p>
            <h3>All My Payslips</h3>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pay Period</th>
                    <th>Basic Salary</th>
                    <th>Total Allowances</th>
                    <th>Total Deductions</th>
                    <th>Net Pay</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payslips as $row): ?>
                    <tr>
                        <td><?= e($row['pay_period']) ?></td>
                        <td><?= money($row['basic_salary']) ?></td>
                        <td><?= money($row['total_allowance']) ?></td>
                        <td><?= money($row['total_deduction']) ?></td>
                        <td><?= money($row['net_pay']) ?></td>
                        <td><span class="status"><?= e($row['payment_status']) ?></span></td>
                        <td><?= e($row['payment_date'] ?: '-') ?></td>
                        <td class="row-actions">
                            <a href="/teacher/payslips/view?id=<?= e($row['payslip_id']) ?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($payslips)): ?>
                    <tr><td colspan="8">No payslips are available for your staff profile yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

