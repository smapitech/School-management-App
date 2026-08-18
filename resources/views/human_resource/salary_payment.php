<section class="module-hero">
    <div>
        <p class="eyebrow">Salary Payment</p>
        <h2>Salary Payment</h2>
        <p>Create salary payments, filter payment history, and view or print payslips.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Create payment</p><h3>Record Salary Payment</h3></div></div>
    <form class="payroll-form" method="post" action="/human-resource/salary-payment/save">
        <label>
            <span>Salary Template</span>
            <select name="template_id" required>
                <?php foreach ($templates as $template): ?>
                    <option value="<?= e($template['id']) ?>"><?= e(trim($template['name'] . ' ' . $template['surname']) . ' - ' . money($template['net_salary'])) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label><span>Payment Month</span><input type="month" name="payment_month" value="<?= e(date('Y-m')) ?>"></label>
        <label>
            <span>Status</span>
            <select name="status"><option>Pending</option><option>Paid</option><option>Failed</option></select>
        </label>
        <button type="submit">Save Payment</button>
    </form>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Filter</p><h3>Payment History</h3></div></div>
    <form class="student-search-form" method="get" action="/human-resource/salary-payment">
        <label><span>Payment Month</span><input type="month" name="payment_month" value="<?= e($filters['payment_month']) ?>"></label>
        <label><span>Status</span><select name="status"><option value="">All</option><option <?= $filters['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option><option <?= $filters['status'] === 'Paid' ? 'selected' : '' ?>>Paid</option><option <?= $filters['status'] === 'Failed' ? 'selected' : '' ?>>Failed</option></select></label>
        <button type="submit">Filter</button>
        <a class="secondary-action" href="/human-resource/salary-payment">Reset</a>
    </form>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Staff</th><th>Designation</th><th>Mobile Number</th><th>Net Payment</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($payments as $row): ?>
                    <tr>
                        <td><?= e(trim($row['name'] . ' ' . $row['middle_name'] . ' ' . $row['surname'])) ?></td>
                        <td><?= e($row['designation']) ?></td>
                        <td><?= e($row['mobile_no']) ?></td>
                        <td><?= money($row['net_payment']) ?></td>
                        <td><span class="status"><?= e($row['status']) ?></span></td>
                        <td class="row-actions"><a href="/human-resource/payslip?id=<?= e($row['id']) ?>">View</a><a href="/human-resource/payslip?id=<?= e($row['id']) ?>" onclick="setTimeout(() => window.print(), 500)">Print</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($payments)): ?><tr><td colspan="6">No salary payments found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
