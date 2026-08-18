<section class="module-hero"><div><p class="eyebrow">Website Manager</p><h2>Parent Reviews</h2><p>Add parent testimonials for the public homepage.</p></div></section>
<?php require __DIR__ . '/nav.php'; ?>
<section class="panel">
    <form class="form-grid" method="post" action="/website-manager/reviews/save" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label><span>Parent Image</span><input type="file" name="parent_image" accept=".jpg,.jpeg,.png,.webp"></label>
        <label><span>Parent Name</span><input name="parent_name" required></label>
        <label><span>Parent Title</span><input name="parent_title" placeholder="Parent of Grade 4 Student"></label>
        <label><span>Rating</span><select name="rating"><?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= e($i) ?>"><?= e($i) ?> stars</option><?php endfor; ?></select></label>
        <label><span>Sort Order</span><input type="number" name="sort_order" value="0"></label>
        <label class="check-row"><input type="checkbox" name="is_featured" value="1" checked> Featured on homepage</label>
        <label class="check-row"><input type="checkbox" name="is_published" value="1" checked> Published</label>
        <label class="form-wide"><span>Review Text</span><textarea name="review_text" rows="4" required></textarea></label>
        <button type="submit">Save Parent Review</button>
    </form>
</section>
<section class="panel"><div class="panel-header"><div><p class="eyebrow">Testimonials</p><h3>Review List</h3></div></div><div class="website-manager-media-grid website-review-admin-grid">
<?php foreach ($items as $item): ?><article><?php if (!empty($item['parent_image'])): ?><img src="<?= e(public_upload_url($item['parent_image'])) ?>" alt="<?= e($item['parent_name']) ?>"><?php else: ?><span class="website-review-avatar"><?= e(strtoupper(substr($item['parent_name'], 0, 2))) ?></span><?php endif; ?><strong><?= e($item['parent_name']) ?></strong><span><?= e($item['parent_title']) ?> | <?= str_repeat('★', (int) $item['rating']) ?></span><p><?= e($item['review_text']) ?></p><small><?= !empty($item['is_published']) ? 'Published' : 'Hidden' ?> <?= !empty($item['is_featured']) ? '| Featured' : '' ?></small><form method="post" action="/website-manager/reviews/delete" onsubmit="return confirm('Delete this parent review?');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($item['id']) ?>"><button type="submit">Delete</button></form></article><?php endforeach; ?>
<?php if (empty($items)): ?><p class="muted">No parent reviews have been added yet.</p><?php endif; ?>
</div></section>
