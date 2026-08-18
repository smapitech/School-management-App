<?php $fullName = $student ? trim(($student['first_name'] ?: $student['applicant']) . ' ' . $student['middle_name'] . ' ' . $student['last_name']) : ''; ?>
<section class="module-hero"><div><p class="eyebrow">Student profile</p><h2>Profile</h2><p>Profile details, promotion history, parent information, and editable personal records.</p></div></section>
<?php require __DIR__ . '/nav.php'; ?>
<?php if ($student): ?>
<section class="student-portal-grid">
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Profile detail</p><h3><?= e($fullName) ?></h3></div><a href="/student_portal/profile?edit=1">Edit</a></div>
        <div class="profile-detail-grid">
            <span>Register No<strong><?= e($student['registration_no']) ?></strong></span>
            <span>Class<strong><?= e($student['class_name']) ?> <?= e($student['section']) ?></strong></span>
            <span>Gender<strong><?= e($student['gender']) ?></strong></span>
            <span>Date of Birth<strong><?= e($student['date_of_birth']) ?></strong></span>
            <span>Religion<strong><?= e($student['religion']) ?></strong></span>
            <span>Mother Tongue<strong><?= e($student['mother_tongue']) ?></strong></span>
            <span>Email<strong><?= e($student['email']) ?></strong></span>
            <span>Address<strong><?= e($student['address']) ?></strong></span>
        </div>
    </article>
    <article class="panel">
        <div class="panel-header"><div><p class="eyebrow">Parent information</p><h3>Guardian Details</h3></div></div>
        <div class="profile-detail-grid">
            <span>Guardian<strong><?= e($student['guardian_full_name'] ?: $student['guardian']) ?></strong></span>
            <span>Relationship<strong><?= e($student['guardian_relationship']) ?></strong></span>
            <span>Father<strong><?= e($student['father_name']) ?></strong></span>
            <span>Mother<strong><?= e($student['mother_name']) ?></strong></span>
            <span>Occupation<strong><?= e($student['guardian_occupation']) ?></strong></span>
            <span>Mobile<strong><?= e($student['guardian_mobile']) ?></strong></span>
        </div>
    </article>
</section>
<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Promotion history</p><h3>Class Movement</h3></div></div>
    <div class="table-wrap"><table><thead><tr><th>Session</th><th>Term</th><th>Class</th><th>Status</th></tr></thead><tbody>
        <tr><td><?= e($student['academic_year'] ?: '2026-2027') ?></td><td><?= e($student['school_term'] ?: 'First Term') ?></td><td><?= e($student['class_name']) ?> <?= e($student['section']) ?></td><td><span class="status">Current</span></td></tr>
    </tbody></table></div>
</section>
<?php endif; ?>
