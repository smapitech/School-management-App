<section class="module-hero"><div><p class="eyebrow">Website Manager</p><h2>Contact Messages</h2><p>Read and manage messages submitted from the public website.</p></div></section>
<?php require __DIR__ . '/nav.php'; ?>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>
<?php foreach ($messages as $message): ?><tr><td><?= e($message['full_name']) ?></td><td><?= e($message['email']) ?></td><td><?= e($message['subject']) ?></td><td><span class="status"><?= !empty($message['is_read']) ? 'Read' : 'New' ?></span></td><td><?= e($message['created_at']) ?></td><td><a href="/website-manager/contact-messages/view?id=<?= e($message['id']) ?>">View</a></td></tr><?php endforeach; ?>
<?php if (empty($messages)): ?><tr><td colspan="6">No contact messages yet.</td></tr><?php endif; ?>
</tbody></table></div></section>
