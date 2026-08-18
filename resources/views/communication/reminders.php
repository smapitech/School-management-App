<section class="module-hero">
    <div>
        <p class="eyebrow">Parent reminders</p>
        <h2>Reminder Messages</h2>
        <p>Send reminder messages to parents for fees, dues, or school follow-up communication.</p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (can('communication', 'create')): ?>
<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Reminder</p><h3>New Parent Reminder</h3></div></div>
    <form class="payroll-form" method="post" action="/communication/reminders/send" enctype="multipart/form-data">
        <label><span>Channel</span><select name="channel"><option>Email</option><option>SMS</option><option>WhatsApp</option><option>Internal</option></select></label>
        <label><span>Audience</span><select name="audience"><option>Parents</option><option>Parents and Students</option></select></label>
        <label><span>Subject</span><input name="subject" value="School Fee Reminder"></label>
        <?php $label = 'Message'; $defaultMessage = '<p>Dear Parent, this is a kind reminder about the outstanding school fee payment.</p>'; $required = true; require __DIR__ . '/editor.php'; ?>
        <label class="form-wide upload-field"><span>Attachment</span><input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt"><small>Optional: attach invoice, circular, or payment guide.</small></label>
        <button type="submit">Queue Reminder</button>
    </form>
</section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Accounting drafts</p><h3>Fee Reminder Drafts</h3></div></div>
    <div class="table-wrap"><table><thead><tr><th>Class</th><th>Term</th><th>Session</th><th>Due Date</th><th>Status</th></tr></thead><tbody>
        <?php foreach ($reminders as $reminder): ?><tr><td><?= e($reminder['class_name']) ?></td><td><?= e($reminder['school_term']) ?></td><td><?= e($reminder['school_session']) ?></td><td><?= e($reminder['due_date']) ?></td><td><span class="status"><?= e($reminder['communication_status']) ?></span></td></tr><?php endforeach; ?>
        <?php if (empty($reminders)): ?><tr><td colspan="5">No fee reminders have been drafted.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>

<?php require __DIR__ . '/partials_log.php'; ?>
