<section class="panel print-report">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Printable report</p>
            <h3>Office Accounting</h3>
        </div>
        <button class="secondary-action no-print" type="button" data-print>Print</button>
    </div>

    <div class="print-meta">
        <span>Type: <?= e($filters['type'] ?: 'All Transactions') ?></span>
        <span>Category: <?= e($filters['category'] ?: 'All Categories') ?></span>
        <span>Office Income: <?= money($summary['office_income']) ?></span>
        <span>Expenditure: <?= money($summary['expenditure']) ?></span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Account</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $row): ?>
                    <tr>
                        <td><?= e($row['transaction_date']) ?></td>
                        <td><?= e($row['type']) ?></td>
                        <td><?= e($row['account_name']) ?></td>
                        <td><?= e($row['category']) ?></td>
                        <td><?= e($row['title']) ?></td>
                        <td><?= e($row['payment_method']) ?></td>
                        <td><?= e($row['reference_no']) ?></td>
                        <td><?= money($row['amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="8">No office accounting record matches this report.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
