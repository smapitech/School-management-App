<section class="module-hero"><div><p class="eyebrow">Website Manager</p><h2>Contact Message</h2><p>Review public website enquiry.</p></div><a class="secondary-action" href="/website-manager/contact-messages">Back</a></section>
<section class="panel message-detail">
    <h3><?= e($message['subject'] ?: 'Website enquiry') ?></h3>
    <p><strong>Name:</strong> <?= e($message['full_name']) ?></p>
    <p><strong>Email:</strong> <?= e($message['email']) ?></p>
    <p><strong>Phone:</strong> <?= e($message['phone']) ?></p>
    <p><strong>Date:</strong> <?= e($message['created_at']) ?></p>
    <p><?= nl2br(e($message['message'])) ?></p>
    <div class="row-actions">
        <form method="post" action="/website-manager/contact-messages/read"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($message['id']) ?>"><button type="submit">Mark as Read</button></form>
        <form method="post" action="/website-manager/contact-messages/delete" onsubmit="return confirm('Delete this message?');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($message['id']) ?>"><button type="submit">Delete</button></form>
    </div>
</section>
