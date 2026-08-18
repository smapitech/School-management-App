<?php
    $selectedRole = strtolower(trim((string) ($selectedRole ?? 'teacher')));
    $rolePermissions = is_array($rolePermissions ?? null) ? $rolePermissions : [];
    $permissionModules = is_array($permissionModules ?? null) ? $permissionModules : [];
    $permissionActions = array_values($permissionActions ?? ['view', 'create', 'edit', 'delete', 'upload', 'print', 'export', 'manage']);
    $permissionActionLabels = [
        'view' => 'View',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'upload' => 'Upload',
        'print' => 'Print',
        'export' => 'Export',
        'manage' => 'Manage',
    ];

    $groupedModules = [];
    foreach ($permissionModules as $module) {
        $category = trim((string) ($module['category'] ?? 'General')) ?: 'General';
        $groupedModules[$category][] = $module;
    }

    $visibleModuleCount = 0;
    $totalModuleCount = count($permissionModules);
    $lockedModuleCount = 0;
    $visiblePreviewModules = [];
    foreach ($permissionModules as $module) {
        $moduleKey = strtolower(trim((string) ($module['module_key'] ?? '')));
        if ($moduleKey === '') {
            continue;
        }
        if (!empty($module['locked'])) {
            $lockedModuleCount++;
        }
        if (!empty($rolePermissions[$moduleKey]['view'])) {
            $visibleModuleCount++;
            $visiblePreviewModules[] = $module;
        }
    }
    $manageableRoleLabels = [];
    foreach (($roles ?? []) as $roleKey) {
        $manageableRoleLabels[$roleKey] = role_name($roleKey);
    }
?>

<section class="module-hero permission-manager-hero no-print">
    <div>
        <p class="eyebrow">Global Settings</p>
        <h2>Role Permission Manager</h2>
        <p>Control which staff roles can access each module in the portal.</p>
    </div>
    <div class="permission-manager-actions">
        <a class="secondary-action" href="/settings">Back to Settings</a>
        <button type="submit" form="permission-manager-form" name="permission_action" value="reset" class="secondary-action" onclick="return confirm('Reset this role to the default permission set?');">Reset to Default</button>
        <button type="submit" form="permission-manager-form" class="primary-action">Save Permissions</button>
    </div>
</section>

<?php if (!empty($permissionNotice)): ?>
    <div class="alert alert-error"><?= e($permissionNotice) ?></div>
<?php endif; ?>
<?php if (!empty($permissionSuccess)): ?>
    <div class="alert alert-success"><?= e($permissionSuccess) ?></div>
<?php endif; ?>

<form method="post" action="/settings/role-permissions/save" class="permission-manager" id="permission-manager-form" data-permission-manager>
    <?= csrf_field() ?>
    <input type="hidden" name="role" value="<?= e($selectedRole) ?>" data-permission-role-field>

    <section class="panel permission-manager-shell">
        <div class="permission-manager-header">
            <div class="permission-manager-tabs">
                <?php foreach (($roles ?? []) as $roleKey): ?>
                    <a class="<?= $selectedRole === $roleKey ? 'is-active' : '' ?>" href="/settings/role-permissions?role=<?= e($roleKey) ?>">
                        <span><?= e($manageableRoleLabels[$roleKey] ?? role_name($roleKey)) ?></span>
                        <small><?= e(ucfirst($roleKey)) ?></small>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="permission-manager-stats">
                <article>
                    <strong><?= e($visibleModuleCount) ?></strong>
                    <span>enabled modules</span>
                </article>
                <article>
                    <strong><?= e($totalModuleCount) ?></strong>
                    <span>available modules</span>
                </article>
                <article>
                    <strong><?= e($lockedModuleCount) ?></strong>
                    <span>locked core items</span>
                </article>
                <article>
                    <strong><?= e(role_name($selectedRole)) ?></strong>
                    <span>current role</span>
                </article>
            </div>
        </div>

        <div class="permission-manager-toolbar">
            <label class="permission-toolbar-field permission-search-field">
                <span>Search modules</span>
                <input type="search" placeholder="Search module name, key, or route..." data-permission-search>
            </label>

            <label class="permission-toolbar-field">
                <span>Category</span>
                <select data-permission-category-filter>
                    <option value="">All categories</option>
                    <?php foreach (array_keys($groupedModules) as $category): ?>
                        <option value="<?= e($category) ?>"><?= e($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="permission-toolbar-field">
                <span>Copy from role</span>
                <select name="copy_from_role">
                    <option value="">Choose role</option>
                    <?php foreach (($roles ?? []) as $roleKey): ?>
                        <option value="<?= e($roleKey) ?>" <?= $roleKey === $selectedRole ? 'disabled' : '' ?>>
                            <?= e($manageableRoleLabels[$roleKey] ?? role_name($roleKey)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="permission-toolbar-actions">
                <button type="button" class="secondary-action" data-permission-bulk="view-all">Select all for role</button>
                <button type="button" class="secondary-action" data-permission-bulk="clear-all">Clear all</button>
                <button type="button" class="secondary-action" data-permission-bulk="view-only">Grant view only</button>
                <button type="submit" class="secondary-action" name="permission_action" value="copy">Copy Permissions</button>
            </div>
        </div>

        <div class="permission-manager-grid">
            <div class="permission-manager-main">
                <?php foreach ($groupedModules as $category => $modules): ?>
                    <section class="panel permission-category-panel" data-permission-category-block="<?= e($category) ?>">
                        <div class="panel-header">
                            <div>
                                <p class="eyebrow"><?= e($category) ?></p>
                                <h3><?= e($category) ?> Modules</h3>
                            </div>
                            <span class="permission-category-count"><?= e(count($modules)) ?> modules</span>
                        </div>

                        <div class="permission-category-table-wrap">
                            <table class="permission-category-table">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>Route</th>
                                        <?php foreach ($permissionActions as $action): ?>
                                            <th><?= e($permissionActionLabels[$action] ?? ucfirst($action)) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modules as $module): ?>
                                        <?php
                                            $moduleKey = strtolower(trim((string) ($module['module_key'] ?? '')));
                                            if ($moduleKey === '') {
                                                continue;
                                            }
                                            $modulePermissions = $rolePermissions[$moduleKey] ?? [];
                                            $availableActions = array_map('strtolower', (array) ($module['actions'] ?? $permissionActions));
                                            $locked = !empty($module['locked']);
                                            $route = trim((string) ($module['primary_route'] ?? ''));
                                            $parentKey = trim((string) ($module['parent_key'] ?? ''));
                                        ?>
                                        <tr data-permission-row
                                            data-module-key="<?= e($moduleKey) ?>"
                                            data-module-name="<?= e(strtolower((string) ($module['module_name'] ?? ''))) ?>"
                                            data-module-route="<?= e(strtolower($route)) ?>"
                                            data-module-category="<?= e(strtolower($category)) ?>">
                                            <td>
                                                <strong><?= e($module['module_name'] ?? $moduleKey) ?></strong>
                                                <div class="permission-module-meta">
                                                    <span><?= e($moduleKey) ?></span>
                                                    <?php if ($parentKey !== ''): ?>
                                                        <span>Parent: <?= e($parentKey) ?></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($module['description'])): ?>
                                                        <span><?= e($module['description']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($route !== ''): ?>
                                                    <code><?= e($route) ?></code>
                                                <?php else: ?>
                                                    <span class="permission-muted">Managed through parent module</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php foreach ($permissionActions as $action): ?>
                                                <?php $actionAllowed = in_array($action, $availableActions, true); ?>
                                                <td>
                                                    <?php if ($actionAllowed): ?>
                                                        <label class="permission-toggle <?= $locked ? 'is-locked' : '' ?>">
                                                            <input
                                                                type="checkbox"
                                                                name="permissions[<?= e($moduleKey) ?>][<?= e($action) ?>]"
                                                                value="1"
                                                                <?= !empty($modulePermissions[$action]) ? 'checked' : '' ?>
                                                                <?= $locked ? 'disabled' : '' ?>
                                                                data-permission-action="<?= e($action) ?>"
                                                                data-permission-role="<?= e($selectedRole) ?>"
                                                            >
                                                            <span><?= e($permissionActionLabels[$action] ?? ucfirst($action)) ?></span>
                                                        </label>
                                                        <?php if ($locked && $action === 'view'): ?>
                                                            <input type="hidden" name="permissions[<?= e($moduleKey) ?>][view]" value="1">
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="permission-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>

            <aside class="panel permission-preview-panel">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Live Preview</p>
                        <h3><?= e(role_name($selectedRole)) ?> access snapshot</h3>
                    </div>
                </div>

                <div class="permission-preview-summary">
                    <article>
                        <span>Visible modules</span>
                        <strong><?= e($visibleModuleCount) ?></strong>
                    </article>
                    <article>
                        <span>Categories</span>
                        <strong><?= e(count($groupedModules)) ?></strong>
                    </article>
                    <article>
                        <span>Core rule</span>
                        <strong>Superadmin always has access</strong>
                    </article>
                </div>

                <div class="permission-preview-list">
                    <h4>Modules currently visible</h4>
                    <?php if (!empty($visiblePreviewModules)): ?>
                        <?php foreach (array_slice($visiblePreviewModules, 0, 10) as $module): ?>
                            <article>
                                <strong><?= e($module['module_name'] ?? '') ?></strong>
                                <span><?= e($module['primary_route'] ?? '') ?></span>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="permission-muted">No modules are enabled yet for this role.</p>
                    <?php endif; ?>
                </div>

                <div class="permission-preview-note">
                    <strong>Need a quick tip?</strong>
                    <p>Grant at least one child item under Exams, Classes, Attendance, or Communication to keep the parent group visible in the sidebar.</p>
                </div>
            </aside>
        </div>

        <div class="permission-sticky-savebar">
            <div>
                <strong>You have unsaved changes</strong>
                <span>Review the module visibility for <?= e(role_name($selectedRole)) ?> before saving.</span>
            </div>
            <div class="permission-sticky-actions">
                <a class="secondary-action" href="/settings">Cancel</a>
                <button type="submit" class="primary-action">Save Permissions</button>
            </div>
        </div>
    </section>
</form>
