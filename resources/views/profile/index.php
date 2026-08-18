<?php
    $role = $user['role'] ?? '';
    $displayName = $user['name'] ?? 'User';
    if ($staff) {
        $displayName = trim($staff['name'] . ' ' . ($staff['middle_name'] ?? '') . ' ' . ($staff['surname'] ?? ''));
    }
    $photo = $profilePhoto ?? '';
?>

<section class="module-hero">
    <div>
        <p class="eyebrow"><?= e(role_name($role)) ?> profile</p>
        <h2>Profile</h2>
        <p>View your login identity, designation, contact details, and linked portal records.</p>
    </div>
</section>

<section class="student-portal-hero">
    <div>
        <?php if ($photo): ?>
            <img class="portal-avatar" src="<?= e($photo) ?>" alt="<?= e($displayName) ?>">
        <?php else: ?>
            <span class="portal-avatar placeholder"><?= e(strtoupper(substr($displayName, 0, 2))) ?></span>
        <?php endif; ?>
        <div>
            <p class="eyebrow">Signed in as <?= e(role_name($role)) ?></p>
            <h2><?= e($displayName) ?></h2>
            <p><?= e($user['email'] ?? '') ?></p>
        </div>
    </div>
</section>

<section class="student-portal-grid">
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Login profile</p><h3>Account Details</h3></div></div>
        <div class="profile-detail-grid">
            <span>Name<strong><?= e($displayName) ?></strong></span>
            <span>Role<strong><?= e(role_name($role)) ?></strong></span>
            <span>Login<strong><?= e($user['email'] ?? '') ?></strong></span>
            <span>Status<strong>Active</strong></span>
        </div>
    </article>

    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Quick links</p><h3>Account Actions</h3></div></div>
        <div class="message-list">
            <article><strong>Change Password</strong><p>Update your account password.</p><a class="secondary-action" href="/profile/password">Open</a></article>
            <article><strong>Mailbox</strong><p>Open your role mailbox and communication area.</p><a class="secondary-action" href="<?= $role === 'student' ? '/student_portal/messages' : ($role === 'parent' ? '/parent_portal' : ($role === 'teacher' ? '/teacher/messages' : '/communication/internal')) ?>">Open</a></article>
        </div>
    </article>
</section>

<?php if ($staff): ?>
    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">Staff record</p><h3>Designation Details</h3></div></div>
        <div class="profile-detail-grid">
            <span>Staff ID<strong><?= e($staff['employee_no']) ?></strong></span>
            <span>Designation<strong><?= e($staff['designation'] ?: $staff['role']) ?></strong></span>
            <span>Email<strong><?= e($staff['email']) ?></strong></span>
            <span>Mobile<strong><?= e($staff['mobile_no']) ?></strong></span>
            <span>Department<strong><?= e($staff['department']) ?></strong></span>
            <span>Joining Date<strong><?= e($staff['joining_date']) ?></strong></span>
        </div>
    </section>
<?php endif; ?>

<?php if ($children): ?>
    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">Parent portal</p><h3>Linked Children</h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Student</th><th>Register No</th><th>Class</th><th>Section</th></tr></thead><tbody>
            <?php foreach ($children as $child): ?>
                <tr><td><?= e($child['applicant']) ?></td><td><?= e($child['registration_no']) ?></td><td><?= e($child['class_name']) ?></td><td><?= e($child['section']) ?></td></tr>
            <?php endforeach; ?>
        </tbody></table></div>
    </section>
<?php endif; ?>
