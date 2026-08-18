<section class="role-tabs">
    <?php if (($user['role'] ?? '') === 'teacher'): ?>
        <a class="<?= is_active('/teacher/attendance') ?>" href="/teacher/attendance">Mark Student Attendance</a>
        <a class="<?= is_active('/teacher/attendance/history') ?>" href="/teacher/attendance/history">Attendance History</a>
        <?php if (can('attendance', 'view_own_staff_attendance')): ?><a class="<?= is_active('/teacher/my-attendance') ?>" href="/teacher/my-attendance">My Staff Attendance</a><?php endif; ?>
    <?php else: ?>
        <a class="<?= is_active('/attendance') ?>" href="/attendance">Overview</a>
        <a class="<?= is_active('/attendance/students') ?>" href="/attendance/students">Student Attendance</a>
        <a class="<?= is_active('/attendance/staff') ?>" href="/attendance/staff">Staff Attendance</a>
    <?php endif; ?>
</section>
