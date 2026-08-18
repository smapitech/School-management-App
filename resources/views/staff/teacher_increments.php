<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher payroll</p>
        <h2>Salary Increments</h2>
        <p>Review salary increases recorded in your own salary template history.</p>
    </div>
    <div class="hero-actions">
        <a class="primary-action" href="/teacher/payslips">My Payslips</a>
        <a class="secondary-action" href="/teacher/payroll">Payroll Overview</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="stat-grid">
    <article class="stat-card"><span>increments</span><strong><?= e($totals['count'] ?? 0) ?></strong><p>salary increases recorded in history</p></article>
    <article class="stat-card"><span>total increase</span><strong><?= money($totals['amount'] ?? 0) ?></strong><p>combined increase across history</p></article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Increment history</p>
            <h3>Your Salary Changes</h3>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Effective Date</th>
                    <th>Previous Salary</th>
                    <th>Current Salary</th>
                    <th>Increment</th>
                    <th>Allowance</th>
                    <th>Net Salary</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($increments as $row): ?>
                    <tr>
                        <td><?= e($row['effective_date']) ?></td>
                        <td><?= money($row['previous_basic_salary']) ?></td>
                        <td><?= money($row['current_basic_salary']) ?></td>
                        <td><?= money($row['increment_amount']) ?></td>
                        <td><?= e($row['allowance_name'] ?: '-') ?></td>
                        <td><?= money($row['net_salary']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($increments)): ?>
                    <tr><td colspan="6">No salary increment records were found for your staff profile.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

