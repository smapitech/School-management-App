<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher payroll</p>
        <h2>Payroll</h2>
        <p>Review your salary records, allowances, deductions, salary changes, and current attendance snapshot.</p>
    </div>
    <div class="hero-actions">
        <a class="primary-action" href="/teacher/payslips">My Payslips</a>
        <a class="secondary-action" href="/teacher/my-attendance">My Staff Attendance</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php
    $latestPayslip = $latestPayslip ?? null;
    $attendanceSummary = $attendanceSummary ?? [];
    $totals = $totals ?? [];
?>

<section class="stat-grid">
    <article class="stat-card"><span>payslips</span><strong><?= e($totals['payslips'] ?? 0) ?></strong><p>salary statements available to you</p></article>
    <article class="stat-card"><span>net pay</span><strong><?= money($totals['net_pay'] ?? 0) ?></strong><p>total net payment across your payslips</p></article>
    <article class="stat-card"><span>allowances</span><strong><?= money($totals['allowances'] ?? 0) ?></strong><p>sum of allowance values in your payslips</p></article>
    <article class="stat-card"><span>deductions</span><strong><?= money($totals['deductions'] ?? 0) ?></strong><p>sum of deduction values in your payslips</p></article>
    <article class="stat-card"><span>increments</span><strong><?= money($totals['increments'] ?? 0) ?></strong><p>salary changes recorded in your history</p></article>
    <article class="stat-card"><span>attendance</span><strong><?= e($attendanceSummary['total'] ?? 0) ?></strong><p>current month staff attendance rows</p></article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Profile</p>
            <h3><?= e(trim(($staff['name'] ?? '') . ' ' . ($staff['middle_name'] ?? '') . ' ' . ($staff['surname'] ?? ''))) ?></h3>
        </div>
    </div>
    <div class="payslip-grid">
        <div><span>Employee No</span><strong><?= e($staff['employee_no'] ?? '-') ?></strong></div>
        <div><span>Designation</span><strong><?= e($staff['designation'] ?? '-') ?></strong></div>
        <div><span>Department</span><strong><?= e($staff['department'] ?? '-') ?></strong></div>
        <div><span>Mobile</span><strong><?= e($staff['mobile_no'] ?? '-') ?></strong></div>
        <div><span>Bank</span><strong><?= e($staff['bank_name'] ?? '-') ?></strong></div>
        <div><span>Account</span><strong><?= e(trim(($staff['account_name'] ?? '') . ' ' . ($staff['account_number'] ?? ''))) ?: '-' ?></strong></div>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Latest payment</p>
            <h3>Most Recent Payslip</h3>
        </div>
    </div>
    <div class="payslip-grid">
        <div><span>Pay Period</span><strong><?= e($latestPayslip['pay_period'] ?? '-') ?></strong></div>
        <div><span>Basic Salary</span><strong><?= money($latestPayslip['basic_salary'] ?? 0) ?></strong></div>
        <div><span>Total Allowances</span><strong><?= money($latestPayslip['total_allowance'] ?? 0) ?></strong></div>
        <div><span>Total Deductions</span><strong><?= money($latestPayslip['total_deduction'] ?? 0) ?></strong></div>
        <div><span>Net Pay</span><strong><?= money($latestPayslip['net_pay'] ?? 0) ?></strong></div>
        <div><span>Status</span><strong><?= e($latestPayslip['payment_status'] ?? 'No record') ?></strong></div>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Recent records</p>
            <h3>Latest Payslips</h3>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pay Period</th>
                    <th>Basic Salary</th>
                    <th>Allowances</th>
                    <th>Deductions</th>
                    <th>Net Pay</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($payslips, 0, 5) as $row): ?>
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
                    <tr><td colspan="8">No payslips have been recorded for your staff profile yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

