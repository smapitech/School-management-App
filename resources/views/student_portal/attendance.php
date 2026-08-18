<section class="module-hero"><div><p class="eyebrow">Attendance</p><h2>My Attendance</h2><p>Filter attendance by month and view daily remarks from the school.</p></div></section>
<?php require __DIR__ . '/nav.php'; ?>
<?php if ($student): ?>
<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Filter</p><h3>Select month</h3></div></div>
    <form class="student-search-form" method="get" action="/student_portal/attendance">
        <label><span>Month</span><input type="month" name="month" value="<?= e($attendanceMonth) ?>"></label>
        <button type="submit">View Attendance</button>
    </form>
</section>
<section class="panel">
    <div class="panel-header"><div><p class="eyebrow"><?= e($attendanceMonth) ?></p><h3>Attendance Records</h3></div></div>
    <div class="table-wrap"><table><thead><tr><th>Date</th><th>Status</th><th>Remark</th></tr></thead><tbody>
        <?php foreach ($monthlyAttendance as $row): ?><tr><td><?= e($row['attendance_date']) ?></td><td><span class="status"><?= e($row['status']) ?></span></td><td><?= e($row['remark']) ?></td></tr><?php endforeach; ?>
        <?php if (empty($monthlyAttendance)): ?><tr><td colspan="3">No attendance record is available for this month.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?php endif; ?>
