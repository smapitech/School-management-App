<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher payroll</p>
        <h2>Payslip</h2>
        <p>Read-only salary statement for your own record.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/teacher/payslips">Back to Payslips</a>
        <button class="primary-action" type="button" onclick="window.print()">Print</button>
    </div>
</section>

<section class="panel payslip">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Payslip</p>
            <h2><?= e($payslip['pay_period'] ?? '') ?> Salary Payslip</h2>
        </div>
        <button class="secondary-action" type="button" onclick="window.print()">Print</button>
    </div>

    <div class="payslip-grid">
        <div><span>Employee No</span><strong><?= e($staff['employee_no'] ?? '-') ?></strong></div>
        <div><span>Staff</span><strong><?= e(trim(($staff['name'] ?? '') . ' ' . ($staff['middle_name'] ?? '') . ' ' . ($staff['surname'] ?? ''))) ?></strong></div>
        <div><span>Designation</span><strong><?= e($staff['designation'] ?? '-') ?></strong></div>
        <div><span>Department</span><strong><?= e($staff['department'] ?? '-') ?></strong></div>
        <div><span>Mobile</span><strong><?= e($staff['mobile_no'] ?? '-') ?></strong></div>
        <div><span>Status</span><strong><?= e($payslip['payment_status'] ?? '-') ?></strong></div>
        <div><span>Payment Date</span><strong><?= e($payslip['payment_date'] ?: '-') ?></strong></div>
        <div><span>Pay Period</span><strong><?= e($payslip['pay_period'] ?? '-') ?></strong></div>
    </div>

    <div class="payslip-grid" style="margin-top: 1.25rem;">
        <div><span>Basic Salary</span><strong><?= money($payslip['basic_salary'] ?? 0) ?></strong></div>
        <div><span>Overtime</span><strong><?= money($payslip['overtime'] ?? 0) ?></strong></div>
        <div><span>Bonus</span><strong><?= money($payslip['bonus'] ?? 0) ?></strong></div>
        <div><span>Allowance Name</span><strong><?= e($payslip['allowance_name'] ?: '-') ?></strong></div>
        <div><span>Allowance Amount</span><strong><?= money($payslip['allowance_amount'] ?? 0) ?></strong></div>
        <div><span>Total Allowances</span><strong><?= money($payslip['total_allowance'] ?? 0) ?></strong></div>
        <div><span>Total Deductions</span><strong><?= money($payslip['total_deduction'] ?? 0) ?></strong></div>
        <div><span>Gross Pay</span><strong><?= money($payslip['gross_pay'] ?? 0) ?></strong></div>
        <div><span>Net Pay</span><strong><?= money($payslip['net_pay'] ?? 0) ?></strong></div>
    </div>

    <div class="payslip-grid" style="margin-top: 1.25rem;">
        <div><span>Bank</span><strong><?= e($staff['bank_name'] ?? '-') ?></strong></div>
        <div><span>Account Name</span><strong><?= e($staff['account_name'] ?? '-') ?></strong></div>
        <div><span>Account Number</span><strong><?= e($staff['account_number'] ?? '-') ?></strong></div>
        <div><span>Record ID</span><strong><?= e($payslip['payslip_id'] ?? '-') ?></strong></div>
    </div>
</section>

