<section class="panel payslip">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Payslip</p>
            <h2><?= e($payment['payment_month']) ?> Salary Payslip</h2>
        </div>
        <button class="secondary-action" onclick="window.print()">Print</button>
    </div>
    <div class="payslip-grid">
        <div><span>Staff</span><strong><?= e($payment['employee_no'] . ' - ' . trim($payment['name'] . ' ' . $payment['surname'])) ?></strong></div>
        <div><span>Designation</span><strong><?= e($payment['designation']) ?></strong></div>
        <div><span>Mobile</span><strong><?= e($payment['mobile_no']) ?></strong></div>
        <div><span>Status</span><strong><?= e($payment['status']) ?></strong></div>
        <div><span>Basic Salary</span><strong><?= money($payment['basic_salary']) ?></strong></div>
        <div><span>Overtime</span><strong><?= money($payment['overtime']) ?></strong></div>
        <div><span>Bonus</span><strong><?= money($payment['bonus']) ?></strong></div>
        <div><span>Total Allowance</span><strong><?= money($payment['total_allowance']) ?></strong></div>
        <div><span>Total Deduction</span><strong><?= money($payment['total_deduction']) ?></strong></div>
        <div><span>Gross Pay</span><strong><?= money($payment['gross_pay']) ?></strong></div>
        <div><span>Net Payment</span><strong><?= money($payment['net_payment']) ?></strong></div>
    </div>
</section>
