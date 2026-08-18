<section class="module-hero"><div><p class="eyebrow">Website Manager</p><h2>Teachers</h2><p>Manage public teacher photos, contact details, subjects, and biographies.</p></div></section>
<?php require __DIR__ . '/nav.php'; ?>
<section class="panel">
    <form class="form-grid" method="post" action="/website-manager/teachers/save" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label><span>Photo</span><input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp"></label>
        <label><span>Full Name</span><input name="full_name" required></label>
        <label><span>Role / Title</span><input name="role_title"></label>
        <label><span>Subject</span><input name="subject"></label>
        <label><span>Phone</span><input name="phone"></label>
        <label><span>Email</span><input type="email" name="email"></label>
        <label><span>Social Link</span><input type="url" name="social_link"></label>
        <label><span>Sort Order</span><input type="number" name="sort_order" value="0"></label>
        <label class="check-row"><input type="checkbox" name="is_featured" value="1"> Featured</label>
        <label class="check-row"><input type="checkbox" name="is_published" value="1" checked> Published</label>
        <label class="form-wide"><span>Biography</span><textarea name="biography" rows="3"></textarea></label>
        <button type="submit">Save Teacher</button>
    </form>
</section>
<section class="panel"><div class="panel-header"><div><p class="eyebrow">Teachers</p><h3>Website Teacher List</h3></div></div><div class="website-manager-media-grid">
<?php foreach ($items as $item): ?><article><?php if (!empty($item['photo_path'])): ?><img src="<?= e(public_upload_url($item['photo_path'])) ?>" alt="<?= e($item['full_name']) ?>"><?php endif; ?><strong><?= e($item['full_name']) ?></strong><span><?= e($item['role_title']) ?> <?= $item['subject'] ? '| ' . e($item['subject']) : '' ?></span><p><?= e($item['biography']) ?></p><form method="post" action="/website-manager/teachers/delete" onsubmit="return confirm('Delete this teacher?');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($item['id']) ?>"><button type="submit">Delete</button></form></article><?php endforeach; ?>
<?php if (empty($items)): ?><p class="muted">No teacher profile has been added yet.</p><?php endif; ?>
</div></section>
