<section class="panel print-report">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Printable report</p>
            <h3>Student Accounting</h3>
        </div>
        <button class="secondary-action no-print" type="button" data-print>Print</button>
    </div>

    <div class="print-meta">
        <span>Class: <?= e($filters['class_name'] ?: 'All Classes') ?></span>
        <span>Term: <?= e($filters['school_term'] ?: 'All Terms') ?></span>
        <span>Session: <?= e($filters['school_session'] ?: 'All Sessions') ?></span>
        <span>Status: <?= e($filters['status'] ?: 'All Status') ?></span>
    </div>

    <section class="mini-stat-grid">
        <?php foreach ($summary as $label => $value): ?>
            <article>
                <span><?= e($label) ?></span>
                <strong><?= money($value) ?></strong>
            </article>
        <?php endforeach; ?>
    </section>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Class</th>
                    <th>School Term</th>
                    <th>School Session</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fees as $row): ?>
                    <tr>
                        <td><?= e($row['invoice_no']) ?></td>
                        <td><?= e($row['class_name'] ?: $row['student_name']) ?></td>
                        <td><?= e($row['school_term']) ?></td>
                        <td><?= e($row['school_session']) ?></td>
                        <td><?= money($row['amount']) ?></td>
                        <td><?= e($row['status']) ?></td>
                        <td><?= e($row['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($fees)): ?>
                    <tr>
                        <td colspan="7">No student accounting record matches this report.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
