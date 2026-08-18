<section class="module-hero"><div><p class="eyebrow">Accounting</p><h2>Fees and Invoices</h2><p>Invoice history, payment history, online payment, and offline payment information.</p></div></section>
<?php require __DIR__ . '/nav.php'; ?>
<?php if ($student): ?>
<section class="stat-grid">
    <?php foreach ($feeSummary as $label => $amount): ?>
        <article class="stat-card"><span><?= e($label) ?></span><strong><?= money($amount) ?></strong><p>annual fee summary</p></article>
    <?php endforeach; ?>
    <article class="stat-card"><span>Invoices</span><strong><?= e(count($fees)) ?></strong><p>fee records</p></article>
</section>
<section class="student-portal-grid">
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Invoice</p><h3>Fee Invoice History</h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Class</th><th>Term</th><th>Session</th><th>Amount</th><th>Status</th></tr></thead><tbody>
            <?php foreach ($fees as $fee): ?><tr><td><?= e($fee['invoice_no']) ?></td><td><?= e($fee['class_name']) ?></td><td><?= e($fee['school_term']) ?></td><td><?= e($fee['school_session']) ?></td><td><?= money($fee['amount']) ?></td><td><span class="status"><?= e($fee['status']) ?></span></td></tr><?php endforeach; ?>
            <?php if (empty($fees)): ?><tr><td colspan="6">No invoice has been created for your class.</td></tr><?php endif; ?>
        </tbody></table></div>
    </article>
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Payment options</p><h3>Online and Offline Payment</h3></div></div>
        <div class="message-list">
            <article><span>Online payment</span><strong>Payment gateway pending</strong><p>Online payment will be connected when the school gateway is configured.</p></article>
            <article><span>Offline payment</span><strong>Bank or office payment</strong><p>Use the school account details from the accounting office and keep your receipt for confirmation.</p></article>
        </div>
    </article>
</section>
<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Payment history</p><h3>Confirmed Payments</h3></div></div>
    <div class="table-wrap"><table><thead><tr><th>Reference</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody>
        <?php foreach ($fees as $fee): ?><?php if (($fee['status'] ?? '') !== 'Unpaid'): ?><tr><td><?= e($fee['invoice_no']) ?></td><td><?= e($fee['created_at']) ?></td><td><?= money($fee['amount']) ?></td><td><span class="status"><?= e($fee['status']) ?></span></td></tr><?php endif; ?><?php endforeach; ?>
    </tbody></table></div>
</section>
<?php endif; ?>
