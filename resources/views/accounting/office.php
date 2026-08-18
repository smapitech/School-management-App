<?php include __DIR__ . '/nav.php'; ?>
<?php $printQuery = http_build_query(array_filter($filters, fn ($value) => $value !== '')); ?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Deposits and expenses</p>
        <h2>Office Accounting</h2>
        <p>Record office income, expenditure vouchers, references, payment methods, and operating notes.</p>
    </div>
    <a class="secondary-action" href="/accounting/office/print<?= $printQuery ? '?' . e($printQuery) : '' ?>">Printable View</a>
</section>

<section class="mini-stat-grid">
    <article>
        <span>Office income</span>
        <strong><?= money($summary['office_income']) ?></strong>
    </article>
    <article>
        <span>Expenditure</span>
        <strong><?= money($summary['expenditure']) ?></strong>
    </article>
    <article>
        <span>Net after expense</span>
        <strong><?= money($summary['office_income'] - $summary['expenditure']) ?></strong>
    </article>
</section>

<?php if (can('accounting', 'create')): ?>
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Voucher</p>
                <h3>Create Office Transaction</h3>
            </div>
        </div>
        <form class="payroll-form accounting-form" method="post" action="/accounting/office/create">
            <label>
                <span>Type</span>
                <select name="type">
                    <option>Income</option>
                    <option>Expenditure</option>
                </select>
            </label>
            <label>
                <span>Account</span>
                <input name="account_name" value="Main Account">
            </label>
            <label>
                <span>Category</span>
                <input name="category" placeholder="Utilities, donation, maintenance">
            </label>
            <label>
                <span>Description</span>
                <input name="title" required>
            </label>
            <label>
                <span>Amount</span>
                <input type="number" min="0" step="0.01" name="amount" required>
            </label>
            <label>
                <span>Payment Method</span>
                <select name="payment_method">
                    <option>Cash</option>
                    <option>Bank Transfer</option>
                    <option>POS</option>
                    <option>Cheque</option>
                </select>
            </label>
            <label>
                <span>Reference No</span>
                <input name="reference_no" placeholder="Voucher or receipt no">
            </label>
            <label>
                <span>Date</span>
                <input type="date" name="transaction_date" value="<?= e(date('Y-m-d')) ?>">
            </label>
            <label class="form-wide">
                <span>Note</span>
                <input name="note" placeholder="Optional note">
            </label>
            <button type="submit">Save Voucher</button>
        </form>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Filter</p>
            <h3>Office Transactions</h3>
        </div>
    </div>
    <form class="student-search-form" method="get" action="/accounting/office">
        <label>
            <span>Type</span>
            <select name="type">
                <option value="">All transactions</option>
                <?php foreach (['Income', 'Expenditure'] as $type): ?>
                    <option value="<?= e($type) ?>" <?= $filters['type'] === $type ? 'selected' : '' ?>><?= e($type) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Category</span>
            <input name="category" value="<?= e($filters['category']) ?>" placeholder="Search category">
        </label>
        <button type="submit">Search</button>
        <a class="secondary-action" href="/accounting/office">Reset</a>
    </form>
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $row): ?>
                    <tr>
                        <td><?= e($row['transaction_date']) ?></td>
                        <td><span class="status"><?= e($row['type']) ?></span></td>
                        <td><?= e($row['account_name']) ?></td>
                        <td><?= e($row['category']) ?></td>
                        <td><?= e($row['title']) ?></td>
                        <td><?= e($row['payment_method']) ?></td>
                        <td><?= e($row['reference_no']) ?></td>
                        <td><?= money($row['amount']) ?></td>
                        <td>
                            <?php if (can('accounting', 'delete')): ?>
                                <form method="post" action="/accounting/office/delete">
                                    <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                    <button class="danger-action" type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="9">No office accounting record matches this filter.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
