<section class="module-hero">
    <div>
        <p class="eyebrow">Website Manager</p>
        <h2>Student Gallery</h2>
        <p>Upload several pictures at once, then let each category appear as its own grouped gallery section.</p>
    </div>
</section>
<?php require __DIR__ . '/nav.php'; ?>
<?php if (!empty($error)): ?><section class="alert-error"><?= e($error) ?></section><?php endif; ?>
<?php if (!empty($success)): ?><section class="alert-success"><?= e($success) ?></section><?php endif; ?>
<section class="panel">
    <form class="form-grid website-manager-form" method="post" action="/website-manager/gallery/save" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label class="form-wide">
            <span>Gallery Images</span>
            <input type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple required>
            <small>Select or drag multiple images at once.</small>
        </label>
        <label>
            <span>Category / Section</span>
            <input name="category" list="gallery_categories" placeholder="Primary 2 Graduation" value="General" required>
        </label>
        <datalist id="gallery_categories">
            <?php foreach ($categories as $category): ?>
                <option value="<?= e($category) ?>"></option>
            <?php endforeach; ?>
        </datalist>
        <label><span>Batch Title</span><input name="title" placeholder="Optional heading for this upload batch"></label>
        <label><span>Sort Order</span><input type="number" name="sort_order" value="0"></label>
        <label class="check-row"><input type="checkbox" name="is_featured" value="1"> Featured</label>
        <label class="check-row"><input type="checkbox" name="is_published" value="1" checked> Published</label>
        <label class="form-wide"><span>Description</span><textarea name="description" rows="3" placeholder="Optional description for all images in this batch"></textarea></label>
        <button type="submit">Upload Gallery Batch</button>
    </form>
</section>
<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Gallery</p>
            <h3>Uploaded Images</h3>
        </div>
    </div>
    <div class="website-gallery-groups">
        <?php foreach ($items as $group): ?>
            <section class="website-gallery-group">
                <div class="website-gallery-group-header">
                    <div>
                        <p class="eyebrow">Category</p>
                        <h4><?= e($group['category']) ?></h4>
                    </div>
                    <small><?= count($group['items']) ?> photo<?= count($group['items']) === 1 ? '' : 's' ?></small>
                </div>
                <div class="website-manager-media-grid">
                    <?php foreach ($group['items'] as $item): ?>
                        <article>
                            <img src="<?= e(public_upload_url($item['image_path'])) ?>" alt="<?= e($item['title'] ?: $group['category']) ?>">
                            <strong><?= e($item['title'] ?: 'Gallery Image') ?></strong>
                            <span><?= !empty($item['is_published']) ? 'Published' : 'Hidden' ?> | <?= !empty($item['is_featured']) ? 'Featured' : 'Regular' ?></span>
                            <?php if (!empty($item['description'])): ?><p><?= e($item['description']) ?></p><?php endif; ?>
                            <form method="post" action="/website-manager/gallery/delete" onsubmit="return confirm('Delete this gallery item?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= e($item['id']) ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
        <?php if (empty($items)): ?><p class="muted">No gallery images have been uploaded yet.</p><?php endif; ?>
    </div>
</section>
