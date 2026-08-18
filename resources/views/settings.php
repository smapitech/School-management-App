<section class="module-hero">
    <div>
        <p class="eyebrow">Superadmin and admin configuration</p>
        <h2>Global School Settings</h2>
        <p>Edit school identity, academic year, contact details, logo, and role access policy.</p>
    </div>
</section>

<?php if (!empty($settingsError)): ?>
    <div class="alert alert-error"><?= e($settingsError) ?></div>
<?php endif; ?>
<?php if (!empty($settingsSuccess)): ?>
    <div class="alert alert-success"><?= e($settingsSuccess) ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">School profile</p>
            <h3>Brand and contact information</h3>
        </div>
    </div>

    <form class="settings-form" method="post" action="/settings/update" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label>
            <span>School Name</span>
            <input name="school_name" value="<?= e($settings['school_name'] ?? '') ?>" required>
        </label>
        <label>
            <span>Short Name</span>
            <input name="school_short_name" value="<?= e($settings['school_short_name'] ?? '') ?>" required>
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="school_email" value="<?= e($settings['school_email'] ?? '') ?>">
        </label>
        <label>
            <span>Phone</span>
            <input name="school_phone" value="<?= e($settings['school_phone'] ?? '') ?>">
        </label>
        <label>
            <span>Academic Year</span>
            <input name="academic_year" value="<?= e($settings['academic_year'] ?? '') ?>">
        </label>
        <label class="settings-wide">
            <span>Address</span>
            <input name="school_address" value="<?= e($settings['school_address'] ?? '') ?>">
        </label>

        <?php if (can('settings', 'upload')): ?>
            <div class="settings-upload-card">
                <div>
                    <span>School Logo</span>
                    <?php if (!empty($settings['logo_path'])): ?>
                        <img src="<?= e($settings['logo_path']) ?>" alt="Current school logo">
                    <?php else: ?>
                        <strong>No logo uploaded</strong>
                    <?php endif; ?>
                </div>
                <label>
                    <span>Replace Logo</span>
                    <input type="file" name="logo" accept=".jpg,.jpeg,.png,.gif,.webp,.svg">
                </label>
                <label class="settings-check">
                    <input type="checkbox" name="delete_logo" value="1">
                    <span>Delete current logo</span>
                </label>
            </div>

            <div class="settings-upload-card">
                <div>
                    <span>Favicon</span>
                    <?php if (!empty($settings['favicon_path'])): ?>
                        <img src="<?= e($settings['favicon_path']) ?>" alt="Current favicon">
                    <?php else: ?>
                        <strong>No favicon uploaded</strong>
                    <?php endif; ?>
                </div>
                <label>
                    <span>Replace Favicon</span>
                    <input type="file" name="favicon" accept=".ico,.png,.jpg,.jpeg,.svg,.webp">
                </label>
                <label class="settings-check">
                    <input type="checkbox" name="delete_favicon" value="1">
                    <span>Delete current favicon</span>
                </label>
            </div>

            <div class="settings-upload-card settings-wide">
                <div>
                    <span>Login Background</span>
                    <?php if (!empty($settings['login_background_path'])): ?>
                        <img class="settings-bg-preview" src="<?= e($settings['login_background_path']) ?>" alt="Current login background">
                    <?php else: ?>
                        <strong>No login background uploaded</strong>
                    <?php endif; ?>
                </div>
                <label>
                    <span>Replace Login Background</span>
                    <input type="file" name="login_background" accept=".jpg,.jpeg,.png,.gif,.webp">
                </label>
                <label class="settings-check">
                    <input type="checkbox" name="delete_login_background" value="1">
                    <span>Delete current login background</span>
                </label>
            </div>
        <?php endif; ?>

        <button type="submit">Save Settings</button>
    </form>
</section>

<section class="panel website-manager-admin">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Portal appearance</p>
            <h3>UI Theme & Color Settings</h3>
        </div>
    </div>

    <script type="application/json" id="ui-theme-presets-json"><?= json_encode($themePresets ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?></script>

    <form class="settings-form settings-theme-form" method="post" action="/settings/update" data-ui-theme-form>
        <?= csrf_field() ?>
        <input type="hidden" name="school_name" value="<?= e($settings['school_name'] ?? '') ?>">
        <input type="hidden" name="school_short_name" value="<?= e($settings['school_short_name'] ?? '') ?>">
        <input type="hidden" name="school_email" value="<?= e($settings['school_email'] ?? '') ?>">
        <input type="hidden" name="school_phone" value="<?= e($settings['school_phone'] ?? '') ?>">
        <input type="hidden" name="school_address" value="<?= e($settings['school_address'] ?? '') ?>">
        <input type="hidden" name="academic_year" value="<?= e($settings['academic_year'] ?? '') ?>">
        <input type="hidden" name="email_driver" value="<?= e($settings['email_driver'] ?? 'SMTP') ?>">
        <input type="hidden" name="smtp_host" value="<?= e($settings['smtp_host'] ?? '') ?>">
        <input type="hidden" name="smtp_port" value="<?= e($settings['smtp_port'] ?? '') ?>">
        <input type="hidden" name="smtp_username" value="<?= e($settings['smtp_username'] ?? '') ?>">
        <input type="hidden" name="smtp_password" value="<?= e($settings['smtp_password'] ?? '') ?>">
        <input type="hidden" name="smtp_from_email" value="<?= e($settings['smtp_from_email'] ?? '') ?>">
        <input type="hidden" name="sms_api_url" value="<?= e($settings['sms_api_url'] ?? '') ?>">
        <input type="hidden" name="sms_api_key" value="<?= e($settings['sms_api_key'] ?? '') ?>">
        <input type="hidden" name="sms_sender_id" value="<?= e($settings['sms_sender_id'] ?? '') ?>">
        <input type="hidden" name="whatsapp_api_url" value="<?= e($settings['whatsapp_api_url'] ?? '') ?>">
        <input type="hidden" name="whatsapp_api_token" value="<?= e($settings['whatsapp_api_token'] ?? '') ?>">
        <input type="hidden" name="whatsapp_phone_number_id" value="<?= e($settings['whatsapp_phone_number_id'] ?? '') ?>">

        <label class="settings-wide">
            <span>Theme Preset</span>
            <select name="ui_theme_preset" data-theme-preset>
                <?php foreach (($themePresets ?? []) as $presetKey => $preset): ?>
                    <option value="<?= e($presetKey) ?>" <?= (($theme['theme_preset'] ?? 'gmail_material') === $presetKey) ? 'selected' : '' ?>>
                        <?= e($preset['name'] ?? ucfirst((string) $presetKey)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <?php
            $themeFields = [
                'app_background' => ['App Background', '--smapis-app-bg'],
                'sidebar_background' => ['Sidebar Background', '--smapis-sidebar-bg'],
                'primary_button_background' => ['Primary Button Background', '--smapis-primary-btn-bg'],
                'primary_button_text' => ['Primary Button Text', '--smapis-primary-btn-text'],
                'active_sidebar_background' => ['Active Sidebar Background', '--smapis-active-sidebar-bg'],
                'active_sidebar_text' => ['Active Sidebar Text', '--smapis-active-sidebar-text'],
                'inactive_sidebar_text' => ['Inactive Sidebar Text/Icon', '--smapis-inactive-sidebar-text'],
                'topbar_background' => ['Topbar/Header Background', '--smapis-topbar-bg'],
                'banner_background' => ['Banner Accent Background', '--smapis-banner-bg'],
                'card_background' => ['Card Background', '--smapis-card-bg'],
            ];
        ?>
        <div class="settings-theme-grid settings-wide">
            <?php foreach ($themeFields as $field => [$label, $cssVar]): ?>
                <label class="theme-color-row">
                    <span><?= e($label) ?></span>
                    <input type="color" name="ui_<?= e($field) ?>" value="<?= e($theme[$field] ?? '#FFFFFF') ?>" data-theme-color="<?= e($field) ?>" data-css-var="<?= e($cssVar) ?>">
                    <input type="text" name="ui_<?= e($field) ?>_hex" value="<?= e($theme[$field] ?? '#FFFFFF') ?>" data-theme-hex="<?= e($field) ?>" pattern="^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$">
                </label>
            <?php endforeach; ?>
        </div>

        <div class="settings-actions settings-wide">
            <button type="submit">Save Theme</button>
            <button type="submit" name="reset_ui_theme" value="1" class="secondary-action">Reset to Gmail Material Default</button>
        </div>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Communication setup</p>
            <h3>Email, SMS and WhatsApp API</h3>
        </div>
    </div>
    <form class="settings-form" method="post" action="/settings/update" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="school_name" value="<?= e($settings['school_name'] ?? '') ?>">
        <input type="hidden" name="school_short_name" value="<?= e($settings['school_short_name'] ?? '') ?>">
        <input type="hidden" name="school_email" value="<?= e($settings['school_email'] ?? '') ?>">
        <input type="hidden" name="school_phone" value="<?= e($settings['school_phone'] ?? '') ?>">
        <input type="hidden" name="school_address" value="<?= e($settings['school_address'] ?? '') ?>">
        <input type="hidden" name="academic_year" value="<?= e($settings['academic_year'] ?? '') ?>">
        <label><span>Email Driver</span><input name="email_driver" value="<?= e($settings['email_driver'] ?? 'SMTP') ?>"></label>
        <label><span>SMTP Host</span><input name="smtp_host" value="<?= e($settings['smtp_host'] ?? '') ?>"></label>
        <label><span>SMTP Port</span><input name="smtp_port" value="<?= e($settings['smtp_port'] ?? '') ?>"></label>
        <label><span>SMTP Username</span><input name="smtp_username" value="<?= e($settings['smtp_username'] ?? '') ?>"></label>
        <label><span>SMTP Password</span><input type="password" name="smtp_password" value="<?= e($settings['smtp_password'] ?? '') ?>"></label>
        <label><span>From Email</span><input type="email" name="smtp_from_email" value="<?= e($settings['smtp_from_email'] ?? '') ?>"></label>
        <label class="settings-wide"><span>SMS API URL</span><input name="sms_api_url" value="<?= e($settings['sms_api_url'] ?? '') ?>"></label>
        <label><span>SMS API Key</span><input name="sms_api_key" value="<?= e($settings['sms_api_key'] ?? '') ?>"></label>
        <label><span>SMS Sender ID</span><input name="sms_sender_id" value="<?= e($settings['sms_sender_id'] ?? '') ?>"></label>
        <label class="settings-wide"><span>WhatsApp API URL</span><input name="whatsapp_api_url" value="<?= e($settings['whatsapp_api_url'] ?? '') ?>"></label>
        <label><span>WhatsApp API Token</span><input name="whatsapp_api_token" value="<?= e($settings['whatsapp_api_token'] ?? '') ?>"></label>
        <label><span>WhatsApp Phone Number ID</span><input name="whatsapp_phone_number_id" value="<?= e($settings['whatsapp_phone_number_id'] ?? '') ?>"></label>
        <button type="submit">Save Communication Setup</button>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Access control</p>
            <h3>Role Permission Manager</h3>
        </div>
        <?php if (($user['role'] ?? '') === 'superadmin'): ?>
            <a class="primary-action" href="/settings/role-permissions">Open manager</a>
        <?php endif; ?>
    </div>

    <div class="permission-grid permission-grid-teaser">
        <?php foreach (['admin', 'teacher', 'accountant', 'receptionist'] as $roleKey): ?>
            <article class="permission-teaser-card">
                <strong><?= e($roles[$roleKey] ?? ucfirst($roleKey)) ?></strong>
                <p>Manage module visibility and access for this role from the dedicated permission manager.</p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
