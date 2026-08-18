<?php
$fieldGroups = [
    'Brand & SEO' => ['site_title','school_logo','favicon','meta_description','meta_keywords','og_title','og_description','og_image','footer_text'],
    'Hero Slideshow' => ['hero_title','hero_subtitle','hero_button_text','hero_button_link','hero_image','hero_slide_2_title','hero_slide_2_subtitle','hero_slide_2_button_text','hero_slide_2_button_link','hero_slide_2_image','hero_slide_3_title','hero_slide_3_subtitle','hero_slide_3_button_text','hero_slide_3_button_link','hero_slide_3_image'],
    'About, Mission & Vision' => ['about_title','about_content','about_image','mission_title','mission_content','vision_title','vision_content'],
    'Homepage Highlights' => ['why_choose_title','why_choose_points','programs_title','programs_content','gallery_title','teachers_title'],
    'Contact Information' => ['contact_title','contact_intro','contact_address','contact_phone','contact_email','contact_whatsapp','opening_hours'],
    'Social Media' => ['facebook_url','instagram_url','youtube_url','tiktok_url','linkedin_url'],
];
$labels = $sectionKeys;
$labels['site_title'] = 'School website title (used when no logo is uploaded)'; $labels['school_logo'] = 'Full school logo / wordmark'; $labels['favicon'] = 'Favicon'; $labels['meta_description'] = 'Meta description'; $labels['meta_keywords'] = 'Meta keywords'; $labels['og_title'] = 'Social sharing title'; $labels['og_description'] = 'Social sharing description'; $labels['og_image'] = 'Social sharing image'; $labels['footer_text'] = 'Footer description';
$values = array_merge($sections, $websiteSettings);
$fileFields = ['school_logo','favicon','og_image','hero_image','hero_slide_2_image','hero_slide_3_image','about_image'];
$textareas = ['meta_description','og_description','footer_text','hero_subtitle','hero_slide_2_subtitle','hero_slide_3_subtitle','about_content','mission_content','vision_content','why_choose_points','programs_content','contact_intro'];
$hints = [
    'school_logo' => 'Upload the complete school logo/wordmark. It will replace the written school name in the header. Recommended: transparent PNG or WebP, approximately 700 × 220 px.', 'favicon' => 'Recommended: square PNG, 64 × 64 px.', 'og_image' => 'Recommended: 1200 × 630 px.', 'hero_image' => 'Slide 1. Recommended: 1920 × 900 px, under 3 MB.', 'hero_slide_2_image' => 'Slide 2. Recommended: 1920 × 900 px, under 3 MB.', 'hero_slide_3_image' => 'Slide 3. Recommended: 1920 × 900 px, under 3 MB.', 'about_image' => 'Recommended: 1200 × 900 px.',
    'why_choose_points' => 'Enter one school strength per line.', 'programs_content' => 'Enter one programme or school level per line.', 'meta_description' => 'Aim for 140–160 characters for search results.',
];
?>
<section class="website-pro-manager-hero website-pro-manager-hero-compact"><div><span class="website-pro-manager-kicker">Content Studio</span><h2>Edit your school website</h2><p>Update public content, school identity, contact details and search visibility. Existing routes and saved records remain unchanged.</p></div><div class="website-pro-manager-actions"><a class="secondary-action" href="/website" target="_blank" rel="noopener">Preview Website</a></div></section>
<?php require __DIR__ . '/nav.php'; ?>
<form class="website-pro-editor" method="post" action="/website-manager/save" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <aside class="website-pro-editor-index"><span class="website-pro-manager-kicker">Page sections</span><?php foreach (array_keys($fieldGroups) as $index => $group): ?><a href="#website-group-<?= $index + 1 ?>"><span><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><?= e($group) ?></a><?php endforeach; ?><div class="website-pro-editor-privacy"><strong>Teacher contact privacy</strong><label><input type="checkbox" name="show_teacher_phone" value="1" <?= !empty($websiteSettings['show_teacher_phone']) ? 'checked' : '' ?>> Show teacher phone numbers</label><label><input type="checkbox" name="show_teacher_email" value="1" <?= !empty($websiteSettings['show_teacher_email']) ? 'checked' : '' ?>> Show teacher email addresses</label></div></aside>
    <div class="website-pro-editor-groups">
        <?php foreach ($fieldGroups as $groupIndex => $fields): ?><section id="website-group-<?= $groupIndex + 1 ?>" class="website-pro-editor-group"><header><span><?= str_pad((string) ($groupIndex + 1), 2, '0', STR_PAD_LEFT) ?></span><div><h3><?= e($groupIndex) ?></h3><p><?= e(match ($groupIndex) { 'Brand & SEO' => 'Set the identity displayed in browsers, search results and social sharing.', 'Hero Slideshow' => 'Manage up to three large homepage banners. Empty slides are automatically hidden.', 'About, Mission & Vision' => 'Tell your school story and explain the purpose behind your work.', 'Homepage Highlights' => 'Present your strengths, programmes, gallery and teaching team.', 'Contact Information' => 'Make it easy for families to reach the school office.', default => 'Connect your public website to the school’s official social channels.' }) ?></p></div></header><div class="website-pro-editor-grid">
            <?php foreach ($fields as $field): ?>
                <?php $value = (string) ($values[$field] ?? ''); $isWide = in_array($field, $textareas, true); ?>
                <label class="<?= $isWide ? 'is-wide' : '' ?>"><span><?= e($labels[$field] ?? ucwords(str_replace('_', ' ', $field))) ?></span>
                    <?php if (in_array($field, $fileFields, true)): ?>
                        <div class="website-pro-upload-field"><?php if ($value !== ''): ?><img src="<?= e(public_upload_url($value)) ?>" alt="Current <?= e($labels[$field] ?? $field) ?> preview"><?php else: ?><span>No image uploaded</span><?php endif; ?><input type="file" name="<?= e($field) ?>" accept=".jpg,.jpeg,.png,.webp"></div>
                    <?php elseif (in_array($field, $textareas, true)): ?>
                        <textarea name="<?= e($field) ?>" rows="<?= in_array($field, ['about_content','why_choose_points','programs_content'], true) ? '6' : '4' ?>"><?= e($value) ?></textarea>
                    <?php else: ?>
                        <input name="<?= e($field) ?>" value="<?= e($value) ?>" <?= str_ends_with($field, '_url') ? 'type="url"' : 'type="text"' ?>>
                    <?php endif; ?>
                    <?php if (!empty($hints[$field])): ?><small><?= e($hints[$field]) ?></small><?php endif; ?>
                </label>
            <?php endforeach; ?>
        </div></section><?php endforeach; ?>
    </div>
    <div class="website-pro-save-bar"><div><strong>Ready to publish your updates?</strong><span>Review the preview, then save all changes together.</span></div><div><a href="/website" target="_blank" rel="noopener">Preview</a><button type="submit">Save Website Changes</button></div></div>
</form>
