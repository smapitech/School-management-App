<?php
    $fullName = $student ? trim(($student['first_name'] ?: $student['applicant']) . ' ' . $student['middle_name'] . ' ' . $student['last_name']) : '';
    $initials = $student ? strtoupper(substr($student['first_name'] ?: $student['applicant'], 0, 1) . substr($student['last_name'] ?: $student['applicant'], 0, 1)) : '';
    $feeTotal = max(1, array_sum($feeSummary ?? []));
    $presentCount = count(array_filter($attendance ?? [], fn ($row) => ($row['status'] ?? '') === 'Present'));
    $lateCount = count(array_filter($attendance ?? [], fn ($row) => ($row['status'] ?? '') === 'Late'));
    $absentCount = count(array_filter($attendance ?? [], fn ($row) => ($row['status'] ?? '') === 'Absent'));
?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Student dashboard</p>
        <h2><?= $student ? 'Welcome to ' . e($fullName) : 'Student Portal' ?></h2>
        <p>Your personal school workspace for fees, attendance, reports, homework, timetables, and messages.</p>
    </div>
    <?php if ($student): ?><a class="secondary-action" href="/student_portal/report-card">Print Report Card</a><?php endif; ?>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if ($student): ?>
    <section class="student-portal-hero">
        <div>
            <?php if (!empty($student['profile_picture'])): ?><img class="portal-avatar" src="<?= e($student['profile_picture']) ?>" alt="<?= e($fullName) ?>"><?php else: ?><span class="portal-avatar placeholder"><?= e($initials) ?></span><?php endif; ?>
            <div>
                <p class="eyebrow">Current profile</p>
                <h2><?= e($fullName) ?></h2>
                <p><?= e($student['registration_no']) ?> | <?= e($student['class_name']) ?> <?= e($student['section']) ?></p>
            </div>
        </div>
    </section>

    <section class="student-portal-grid">
        <article class="panel chart-panel">
            <div class="panel-header"><div><p class="eyebrow">My annual fee summary</p><h3>Fee status</h3></div></div>
            <div class="chart-canvas-wrap">
                <canvas class="pro-chart" data-chart="bar" data-values='<?= e(json_encode([
                    'labels' => array_keys($feeSummary ?? []),
                    'values' => array_values(array_map('floatval', $feeSummary ?? [])),
                    'prefix' => '₦'
                ])) ?>' aria-label="Student fee status chart"></canvas>
            </div>
            <div class="split-list">
                <?php foreach (($feeSummary ?? []) as $label => $amount): ?>
                    <span><?= e($label) ?><strong><?= money($amount) ?></strong></span>
                <?php endforeach; ?>
            </div>
        </article>
        <article class="panel chart-panel">
            <div class="panel-header"><div><p class="eyebrow">Attendance</p><h3>Recent attendance</h3></div><a href="/student_portal/attendance">Open</a></div>
            <?php $attendanceTotal = max(1, $presentCount + $lateCount + $absentCount); ?>
            <div class="chart-canvas-wrap chart-canvas-wrap--donut">
                <canvas class="pro-chart" data-chart="doughnut" data-values='<?= e(json_encode([
                    'labels' => ['Present', 'Late', 'Absent'],
                    'values' => [$presentCount, $lateCount, $absentCount],
                    'centerLabel' => round(($presentCount / $attendanceTotal) * 100) . '%',
                    'centerText' => 'Present'
                ])) ?>' aria-label="Student attendance chart"></canvas>
            </div>
            <div class="chart-legend">
                <span><i class="legend-dot legend-dot--primary"></i>Present <strong><?= e($presentCount) ?></strong></span>
                <span><i class="legend-dot" style="background:#d7a85b"></i>Late <strong><?= e($lateCount) ?></strong></span>
                <span><i class="legend-dot legend-dot--accent"></i>Absent <strong><?= e($absentCount) ?></strong></span>
            </div>
        </article>
    </section>

    <section class="student-portal-grid">
        <article class="panel">
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Quick view</p><h3>My School Work</h3></div></div>
            <div class="mini-stat-grid">
                <article><span>Report scores</span><strong><?= e(count($results)) ?></strong></article>
                <article><span>Homework</span><strong><?= e(count($assignments)) ?></strong></article>
                <article><span>Exam papers</span><strong><?= e(count($examSchedules)) ?></strong></article>
                <article><span>Messages</span><strong><?= e(count($messages)) ?></strong></article>
            </div>
        </article>
    </section>
<?php endif; ?>
