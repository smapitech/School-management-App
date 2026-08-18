<?php
    $portalLinks = [
        'dashboard' => ['/student_portal', 'Dashboard'],
        'profile' => ['/student_portal/profile', 'Profile'],
        'teachers' => ['/student_portal/teachers', 'Teachers'],
        'classes' => ['/student_portal/classes', 'Classes'],
        'homework' => ['/student_portal/homework', 'Homework'],
        'report' => ['/student_portal/report', 'Report'],
        'attendance' => ['/student_portal/attendance', 'Attendance'],
        'messages' => ['/student_portal/messages', 'Message'],
        'accounting' => ['/student_portal/accounting', 'Accounting'],
    ];
?>

<nav class="portal-module-nav">
    <?php foreach ($portalLinks as $key => [$path, $label]): ?>
        <a class="<?= ($activePortal ?? 'dashboard') === $key ? 'is-active' : '' ?>" href="<?= e($path) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</nav>

<?php if (!$student): ?>
    <section class="panel empty-state"><h3>No student profile linked</h3><p>Ask the admin to connect this login to an admitted student record.</p></section>
<?php endif; ?>
