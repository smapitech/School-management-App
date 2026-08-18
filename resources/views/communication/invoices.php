<section class="module-hero">
    <div>
        <p class="eyebrow">Invoice messaging</p>
        <h2>Send Invoice</h2>
        <p>Queue invoice messages to parents and students through Email, SMS, or WhatsApp.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (can('communication', 'create')): ?>
<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Invoice</p><h3>Send Fee Invoice</h3></div></div>
    <form class="payroll-form" method="post" action="/communication/invoices/send" enctype="multipart/form-data">
        <label><span>Channel</span><select name="channel"><option>Email</option><option>SMS</option><option>WhatsApp</option><option>Internal</option></select></label>
        <label class="form-wide"><span>Invoice</span><select name="fee_id" required><option value="">Select invoice</option><?php foreach ($fees as $fee): ?><option value="<?= e($fee['id']) ?>"><?= e($fee['invoice_no'] . ' - ' . $fee['student_name'] . ' - ' . money($fee['amount']) . ' - ' . $fee['status']) ?></option><?php endforeach; ?></select></label>
        <?php $label = 'Message'; $defaultMessage = '<p>Dear Parent, please find the school fee invoice attached/queued for your attention.</p>'; $required = false; require __DIR__ . '/editor.php'; ?>
        <label class="form-wide upload-field"><span>Attachment</span><input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt"><small>Optional: attach invoice PDF or supporting document.</small></label>
        <button type="submit">Queue Invoice Message</button>
    </form>
</section>
<?php endif; ?>

<?php require __DIR__ . '/partials_log.php'; ?>
