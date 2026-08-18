<?php
    $isTeacher = ($user['role'] ?? '') === 'teacher';
    $staffName = $staff ? trim($staff['name'] . ' ' . ($staff['middle_name'] ?? '') . ' ' . ($staff['surname'] ?? '')) : ($user['name'] ?? 'Staff');
    $photo = $staff['staff_photo'] ?? '';
    $initials = strtoupper(substr($staffName, 0, 2));
    $dashboard = $dashboard ?? [];
    $teacherClasses = $dashboard['classes'] ?? ($assignments ?? []);
    $teacherSubjects = $dashboard['subjects'] ?? [];
    $teacherStudents = $dashboard['students'] ?? ($students ?? []);
    $todayTimetable = $dashboard['today_timetable'] ?? [];
    $recentParentMessages = $dashboard['recent_parent_messages'] ?? [];
    $recentTeacherMessages = $dashboard['recent_teacher_messages'] ?? [];
    $latestPayslip = $dashboard['latest_payslip'] ?? [];
    $attendanceSummary = $dashboard['attendance_summary'] ?? [];
    $todayLabel = $dashboard['today_label'] ?? date('l, d F Y');
    $quickLinks = [
        ['title' => 'My Classes', 'subtitle' => 'Open assigned class sections', 'path' => '/teacher/classes', 'icon' => 'school'],
        ['title' => 'My Students', 'subtitle' => 'Browse assigned students', 'path' => '/teacher/classes/students', 'icon' => 'users'],
        ['title' => 'Class Timetable', 'subtitle' => 'See today and upcoming lessons', 'path' => '/teacher/classes/timetable', 'icon' => 'calendar-days'],
        ['title' => 'Mark Attendance', 'subtitle' => 'Record student attendance', 'path' => '/teacher/attendance', 'icon' => 'calendar-check'],
        ['title' => 'Parent Messages', 'subtitle' => 'Message assigned parents', 'path' => '/teacher/messages/parents', 'icon' => 'mail'],
        ['title' => 'Teacher Messages', 'subtitle' => 'Chat with fellow teachers', 'path' => '/teacher/messages/teachers', 'icon' => 'messages-square'],
        ['title' => 'My Payslips', 'subtitle' => 'Review your payroll history', 'path' => '/teacher/payslips', 'icon' => 'wallet'],
        ['title' => 'My Attendance', 'subtitle' => 'View your staff attendance', 'path' => '/teacher/my-attendance', 'icon' => 'badge-check'],
    ];
?>

<section class="student-portal-hero staff-dashboard-hero">
    <div>
        <?php if ($photo): ?>
            <img class="portal-avatar" src="<?= e($photo) ?>" alt="<?= e($staffName) ?>">
        <?php else: ?>
            <span class="portal-avatar placeholder"><?= e($initials) ?></span>
        <?php endif; ?>
        <div>
            <p class="eyebrow"><?= e(role_name($user['role'])) ?> workspace</p>
            <h2>Welcome back, <?= e($staffName) ?></h2>
            <p><?= e($staff['employee_no'] ?? '') ?> <?= !empty($staff['designation']) ? '| ' . e($staff['designation']) : '' ?></p>
        </div>
    </div>
</section>

<?php if ($isTeacher): ?>
    <section class="stat-grid">
        <article class="stat-card"><span>assigned classes</span><strong><?= e(count($teacherClasses)) ?></strong><p>class sections linked to you</p></article>
        <article class="stat-card"><span>assigned subjects</span><strong><?= e(count($teacherSubjects)) ?></strong><p>subjects you can teach</p></article>
        <article class="stat-card"><span>assigned students</span><strong><?= e(count($teacherStudents)) ?></strong><p>students in your assigned scope</p></article>
        <article class="stat-card"><span>pending attendance</span><strong><?= e((int) ($dashboard['pending_attendance_count'] ?? 0)) ?></strong><p>students not marked today</p></article>
    </section>

    <section class="analytics-grid teacher-analytics" aria-label="Teacher analytics">
        <article class="panel chart-panel">
            <div class="panel-header"><div><p class="eyebrow">Attendance workload</p><h3>Today’s marking progress</h3></div></div>
            <?php
                $markedToday = max(0, count($teacherStudents) - (int) ($dashboard['pending_attendance_count'] ?? 0));
                $pendingToday = (int) ($dashboard['pending_attendance_count'] ?? 0);
                $markingRate = count($teacherStudents) > 0 ? (int) round(($markedToday / count($teacherStudents)) * 100) : 0;
            ?>
            <div class="chart-canvas-wrap chart-canvas-wrap--donut">
                <canvas class="pro-chart" data-chart="doughnut" data-values='<?= e(json_encode([
                    'labels' => ['Marked', 'Pending'],
                    'values' => [$markedToday, $pendingToday],
                    'centerLabel' => $markingRate . '%',
                    'centerText' => 'Completed'
                ])) ?>' aria-label="Attendance marking progress"></canvas>
            </div>
            <div class="chart-legend">
                <span><i class="legend-dot legend-dot--primary"></i>Marked <strong><?= e($markedToday) ?></strong></span>
                <span><i class="legend-dot legend-dot--accent"></i>Pending <strong><?= e($pendingToday) ?></strong></span>
            </div>
        </article>
        <article class="panel chart-panel panel-wide">
            <div class="panel-header"><div><p class="eyebrow">Teaching scope</p><h3>Classes, subjects and students</h3></div></div>
            <div class="chart-canvas-wrap">
                <canvas class="pro-chart" data-chart="bar" data-values='<?= e(json_encode([
                    'labels' => ['Classes', 'Subjects', 'Students', 'Lessons today'],
                    'values' => [count($teacherClasses), count($teacherSubjects), count($teacherStudents), count($todayTimetable)]
                ])) ?>' aria-label="Teacher workload chart"></canvas>
            </div>
        </article>
    </section>

    <section class="dashboard-grid">
        <article class="panel panel-wide">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Schedule</p>
                    <h3>Today's Timetable</h3>
                </div>
                <a href="/teacher/classes/timetable">Open timetable</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Class</th><th>Subject</th><th>Time</th><th>Room</th><th>Note</th></tr></thead>
                    <tbody>
                        <?php foreach ($todayTimetable as $row): ?>
                            <tr>
                                <td><?= e(trim(($row['class_name'] ?? '') . ' ' . ($row['section'] ?? ''))) ?></td>
                                <td><?= e($row['subject_name'] ?? $row['subject'] ?? '') ?></td>
                                <td><?= e(trim(($row['start_time'] ?? '') . ' - ' . ($row['end_time'] ?? ''))) ?></td>
                                <td><?= e($row['room'] ?? '') ?></td>
                                <td><?= e($row['note'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($todayTimetable)): ?><tr><td colspan="5">No timetable entry is scheduled for <?= e($todayLabel) ?>.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Payroll</p>
                    <h3>Latest Payslip</h3>
                </div>
                <a href="/teacher/payslips">View all</a>
            </div>
            <?php if (!empty($latestPayslip)): ?>
                <div class="split-list">
                    <span>Pay period <strong><?= e($latestPayslip['pay_period'] ?? '') ?></strong></span>
                    <span>Basic salary <strong><?= money((float) ($latestPayslip['basic_salary'] ?? 0)) ?></strong></span>
                    <span>Allowances <strong><?= money((float) ($latestPayslip['total_allowances'] ?? 0)) ?></strong></span>
                    <span>Deductions <strong><?= money((float) ($latestPayslip['total_deductions'] ?? 0)) ?></strong></span>
                    <span>Net pay <strong><?= money((float) ($latestPayslip['net_pay'] ?? 0)) ?></strong></span>
                    <span>Status <strong><?= e(ucwords(strtolower((string) ($latestPayslip['payment_status'] ?? 'Pending')))) ?></strong></span>
                    <span>Payment date <strong><?= e($latestPayslip['payment_date'] ?: 'Not paid yet') ?></strong></span>
                </div>
                <?php if (!empty($latestPayslip['payslip_id'])): ?>
                    <div class="panel-actions">
                        <a class="secondary-action" href="/teacher/payslips/view?id=<?= e($latestPayslip['payslip_id']) ?>">Open payslip</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p class="muted">No payslip is available yet.</p>
            <?php endif; ?>
        </article>
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Attendance</p>
                    <h3>Staff Attendance Summary</h3>
                </div>
                <a href="/teacher/my-attendance">Open</a>
            </div>
            <div class="split-list">
                <span>Month <strong><?= e($attendanceSummary['month'] ?? date('Y-m')) ?></strong></span>
                <span>Total <strong><?= e((int) ($attendanceSummary['total'] ?? 0)) ?></strong></span>
                <span>Present <strong><?= e((int) ($attendanceSummary['Present'] ?? 0)) ?></strong></span>
                <span>Absent <strong><?= e((int) ($attendanceSummary['Absent'] ?? 0)) ?></strong></span>
                <span>Late <strong><?= e((int) ($attendanceSummary['Late'] ?? 0)) ?></strong></span>
                <span>Excused <strong><?= e((int) ($attendanceSummary['Excused'] ?? 0)) ?></strong></span>
                <span>Leave <strong><?= e((int) ($attendanceSummary['Leave'] ?? 0)) ?></strong></span>
                <span>Half Day <strong><?= e((int) ($attendanceSummary['Half Day'] ?? 0)) ?></strong></span>
            </div>
        </article>
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Communication</p>
                    <h3>Recent Parent Messages</h3>
                </div>
                <a href="/teacher/messages/parents">Open</a>
            </div>
            <div class="message-list">
                <?php foreach ($recentParentMessages as $message): ?>
                    <article>
                        <span><?= e($message['participant_name'] ?? 'Parent') ?> | <?= e($message['last_activity_at'] ?? $message['created_at'] ?? '') ?></span>
                        <strong><?= e($message['subject'] ?? 'Conversation') ?></strong>
                        <p><?= e($message['student_name'] ?? '') ?><?= !empty($message['last_message_preview']) ? ' | ' . e($message['last_message_preview']) : '' ?></p>
                        <a class="secondary-action" href="/teacher/messages/view?id=<?= e($message['id']) ?>">Open</a>
                    </article>
                <?php endforeach; ?>
                <?php if (empty($recentParentMessages)): ?><p class="muted">No parent conversation is available yet.</p><?php endif; ?>
            </div>
        </article>
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Communication</p>
                    <h3>Recent Teacher Messages</h3>
                </div>
                <a href="/teacher/messages/teachers">Open</a>
            </div>
            <div class="message-list">
                <?php foreach ($recentTeacherMessages as $message): ?>
                    <article>
                        <span><?= e($message['participant_name'] ?? 'Teacher') ?> | <?= e($message['last_activity_at'] ?? $message['created_at'] ?? '') ?></span>
                        <strong><?= e($message['subject'] ?? 'Conversation') ?></strong>
                        <p><?= e($message['last_message_preview'] ?? '') ?></p>
                        <a class="secondary-action" href="/teacher/messages/view?id=<?= e($message['id']) ?>">Open</a>
                    </article>
                <?php endforeach; ?>
                <?php if (empty($recentTeacherMessages)): ?><p class="muted">No teacher conversation is available yet.</p><?php endif; ?>
            </div>
        </article>
    </section>

    <section class="module-grid">
        <?php foreach ($quickLinks as $link): ?>
            <a class="module-card" href="<?= e($link['path']) ?>">
                <span><?= module_icon($link['icon']) ?></span>
                <strong><?= e($link['title']) ?></strong>
                <p><?= e($link['subtitle']) ?></p>
            </a>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <section class="stat-grid">
        <article class="stat-card"><span>role</span><strong><?= e(role_name($user['role'])) ?></strong><p>personal staff workspace</p></article>
        <article class="stat-card"><span>messages</span><strong><?= e(count($messages)) ?></strong><p>recent communication items</p></article>
        <article class="stat-card"><span>profile</span><strong><?= $staff ? 'Ready' : 'Pending' ?></strong><p>staff record connection</p></article>
        <article class="stat-card"><span>access</span><strong>Limited</strong><p>only modules assigned to your role</p></article>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Communication</p><h3>Recent Messages</h3></div></div>
    <div class="message-list">
        <?php foreach ($messages as $message): ?>
            <?php if ($isTeacher && isset($message['conversation_type'])): ?>
                <article><span><?= e(ucwords(str_replace('_', ' ', $message['conversation_type']))) ?> | <?= e($message['last_activity_at'] ?? $message['created_at']) ?></span><strong><?= e($message['subject']) ?></strong><p><?= e(($message['participant_name'] ?? 'Conversation') . ' | ' . ($message['last_message_preview'] ?? '')) ?></p><a class="secondary-action" href="/teacher/messages/view?id=<?= e($message['id']) ?>">Open</a></article>
            <?php else: ?>
                <article><span><?= e($message['channel']) ?> | <?= e($message['created_at']) ?></span><strong><?= e($message['subject']) ?></strong><p><?= e(substr(strip_tags($message['message']), 0, 180)) ?></p></article>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?><p class="muted">No message is available yet.</p><?php endif; ?>
    </div>
</section>
