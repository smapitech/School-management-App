<?php include __DIR__ . '/nav.php'; ?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Finance control center</p>
        <h2>Accounting Overview</h2>
        <p>Track school fee collections, office income, operating expenditure, and the current net balance from one page.</p>
    </div>
</section>

<section class="mini-stat-grid">
    <article>
        <span>Student income</span>
        <strong><?= money($summary['student_income']) ?></strong>
    </article>
    <article>
        <span>Office income</span>
        <strong><?= money($summary['office_income']) ?></strong>
    </article>
    <article>
        <span>Total income</span>
        <strong><?= money($summary['total_income']) ?></strong>
    </article>
    <article>
        <span>Expenditure</span>
        <strong><?= money($summary['expenditure']) ?></strong>
    </article>
    <article>
        <span>Net balance</span>
        <strong><?= money($summary['net_balance']) ?></strong>
    </article>
</section>

<?php $max = max(1, $summary['student_income'], $summary['office_income'], $summary['expenditure']); ?>
<section class="accounting-grid">
    <article class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Income vs expense</p>
                <h3>Finance Chart</h3>
            </div>
        </div>
        <div class="finance-chart">
            <div>
                <span>Student Accounting</span>
                <strong><?= money($summary['student_income']) ?></strong>
                <i style="width: <?= e((string) max(4, round(($summary['student_income'] / $max) * 100))) ?>%"></i>
            </div>
            <div>
                <span>Office Accounting</span>
                <strong><?= money($summary['office_income']) ?></strong>
                <i style="width: <?= e((string) max(4, round(($summary['office_income'] / $max) * 100))) ?>%"></i>
            </div>
            <div class="expense-bar">
                <span>Expenditure</span>
                <strong><?= money($summary['expenditure']) ?></strong>
                <i style="width: <?= e((string) max(4, round(($summary['expenditure'] / $max) * 100))) ?>%"></i>
            </div>
        </div>
    </article>

    <article class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Fee status</p>
                <h3>Student Accounting</h3>
            </div>
            <a class="secondary-action" href="/accounting/student">Open</a>
        </div>
        <div class="fee-breakdown">
            <?php foreach ($studentSummary as $label => $value): ?>
                <div>
                    <span><?= e($label) ?></span>
                    <strong><?= money($value) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Expenses</p>
            <h3>Expenditure Table</h3>
        </div>
        <a class="secondary-action" href="/accounting/office">Office Accounting</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Account</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Paid Via</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expenditures as $row): ?>
                    <tr>
                        <td><?= e($row['transaction_date']) ?></td>
                        <td><?= e($row['account_name']) ?></td>
                        <td><?= e($row['category']) ?></td>
                        <td><?= e($row['title']) ?></td>
                        <td><?= e($row['payment_method']) ?></td>
                        <td><?= money($row['amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($expenditures)): ?>
                    <tr>
                        <td colspan="6">No expenditure has been recorded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
