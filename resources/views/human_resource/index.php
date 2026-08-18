<section class="module-hero">
    <div>
        <p class="eyebrow">Human Resource</p>
        <h2>Human Resource</h2>
        <p>Manage salary templates, assignment, payments, payslip viewing, and payroll status.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php
    $roles = $overview['roles'] ?? [];
    $totals = $overview['totals'] ?? [];
    $employeeTotal = array_sum(array_map(fn ($row) => (int) $row['total'], $roles));
    $maxRole = max(1, ...array_map(fn ($row) => (int) $row['total'], $roles ?: [['total' => 1]]));
    $salaryPaid = (float) ($totals['salary_paid'] ?? 0);
    $allowancePaid = (float) ($totals['allowance_paid'] ?? 0);
    $deductionTotal = (float) ($totals['deduction_total'] ?? 0);
    $grossPaid = (float) ($totals['gross_paid'] ?? 0);
    $expectedSalary = (float) ($totals['expected_salary'] ?? 0);
    $expectedAllowance = (float) ($totals['expected_allowance'] ?? 0);
    $expectedDeduction = (float) ($totals['expected_deduction'] ?? 0);
    $payrollBars = [
        ['label' => 'Salary paid out', 'value' => $salaryPaid, 'class' => 'salary-bar'],
        ['label' => 'Allowance paid out', 'value' => $allowancePaid, 'class' => 'allowance-bar'],
        ['label' => 'Total deduction', 'value' => $deductionTotal, 'class' => 'deduction-bar'],
        ['label' => 'Gross paid out', 'value' => $grossPaid, 'class' => 'gross-bar'],
    ];
    $maxPayroll = max(1, ...array_map(fn ($row) => (float) $row['value'], $payrollBars));
?>

<section class="stat-grid">
    <article class="stat-card"><span>employees</span><strong><?= e($employeeTotal) ?></strong><p>active employee scale</p></article>
    <article class="stat-card"><span>salary assignments</span><strong><?= e(count($assignments)) ?></strong><p>current staff assignments</p></article>
    <article class="stat-card"><span>salary paid out</span><strong><?= money($salaryPaid) ?></strong><p>paid payroll records</p></article>
    <article class="stat-card"><span>pending payments</span><strong><?= e(count(array_filter($payments, fn ($p) => $p['status'] !== 'Paid'))) ?></strong><p>awaiting completion</p></article>
</section>

<section class="hr-overview-grid">
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Employee scale</p><h3>Staff by Role</h3></div></div>
        <div class="hr-role-chart">
            <?php foreach ($roles as $row): ?>
                <?php $width = ((int) $row['total'] / $maxRole) * 100; ?>
                <div>
                    <span><?= e($row['role_name']) ?></span>
                    <strong><?= e($row['total']) ?></strong>
                    <i style="width: <?= e($width) ?>%;"></i>
                </div>
            <?php endforeach; ?>
            <?php if (empty($roles)): ?><p class="muted">No employee records found.</p><?php endif; ?>
        </div>
    </article>
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Payroll chart</p><h3>Paid Salary, Allowances and Deductions</h3></div></div>
        <div class="hr-payroll-chart">
            <?php foreach ($payrollBars as $row): ?>
                <?php $width = ((float) $row['value'] / $maxPayroll) * 100; ?>
                <div class="<?= e($row['class']) ?>">
                    <span><?= e($row['label']) ?></span>
                    <strong><?= money($row['value']) ?></strong>
                    <i style="width: <?= e($width) ?>%;"></i>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="hr-payroll-summary">
            <div><span>Expected salary</span><strong><?= money($expectedSalary) ?></strong></div>
            <div><span>Expected allowance</span><strong><?= money($expectedAllowance) ?></strong></div>
            <div><span>Expected deduction</span><strong><?= money($expectedDeduction) ?></strong></div>
        </div>
    </article>
</section>
