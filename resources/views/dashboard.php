<section class="hero-band">
    <div>
        <p class="eyebrow">Operations overview</p>
        <h2>Run academics, finance, admissions, and communication from one clean workspace.</h2>
    </div>
    <a class="primary-action" href="/students">Manage Students</a>
</section>

<section class="stat-grid">
    <?php foreach ($stats as $stat): ?>
        <article class="stat-card">
            <span><?= e($stat['detail']) ?></span>
            <strong><?= e($stat['value']) ?></strong>
            <p><?= e($stat['label']) ?></p>
        </article>
    <?php endforeach; ?>
</section>


<section class="analytics-grid" aria-label="School analytics">
    <article class="panel chart-panel">
        <div class="panel-header">
            <div><p class="eyebrow">Attendance analytics</p><h3>Student attendance overview</h3></div>
            <span class="chart-badge"><?= e($attendanceSummary['rate']) ?>% present</span>
        </div>
        <div class="chart-canvas-wrap chart-canvas-wrap--donut">
            <canvas class="pro-chart" data-chart="doughnut" data-values='<?= e(json_encode([
                'labels' => ['Present', 'Absent'],
                'values' => [(int) ($attendanceSummary['present'] ?? 0), (int) ($attendanceSummary['absent'] ?? 0)],
                'centerLabel' => ((int) ($attendanceSummary['rate'] ?? 0)) . '%',
                'centerText' => 'Attendance'
            ])) ?>' aria-label="Student attendance chart"></canvas>
        </div>
        <div class="chart-legend">
            <span><i class="legend-dot legend-dot--primary"></i>Present <strong><?= e($attendanceSummary['present']) ?></strong></span>
            <span><i class="legend-dot legend-dot--accent"></i>Absent <strong><?= e($attendanceSummary['absent']) ?></strong></span>
        </div>
    </article>

    <article class="panel chart-panel panel-wide">
        <div class="panel-header">
            <div><p class="eyebrow">Finance analytics</p><h3>Fee status distribution</h3></div>
            <a href="/accounting/student">Open accounting</a>
        </div>
        <div class="chart-canvas-wrap">
            <canvas class="pro-chart" data-chart="bar" data-values='<?= e(json_encode([
                'labels' => array_keys($feeSummary),
                'values' => array_values(array_map('floatval', $feeSummary)),
                'prefix' => '₦'
            ])) ?>' aria-label="Fee status bar chart"></canvas>
        </div>
    </article>
</section>

<section class="dashboard-grid">
    <article class="panel panel-wide">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Student registry</p>
                <h3>Recent Students</h3>
            </div>
            <a href="/students">View all</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student No</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Guardian</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentStudents as $student): ?>
                        <tr>
                            <td><?= e($student['student_no']) ?></td>
                            <td><?= e($student['name']) ?></td>
                            <td><?= e($student['class_name']) ?></td>
                            <td><?= e($student['guardian']) ?></td>
                            <td><span class="status"><?= e($student['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Attendance</p>
                <h3>Today</h3>
            </div>
            <strong><?= e($attendanceSummary['rate']) ?>%</strong>
        </div>
        <div class="progress">
            <span style="width: <?= e($attendanceSummary['rate']) ?>%"></span>
        </div>
        <div class="split-list">
            <span>Present <strong><?= e($attendanceSummary['present']) ?></strong></span>
            <span>Absent <strong><?= e($attendanceSummary['absent']) ?></strong></span>
        </div>
    </article>

    <article class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Finance</p>
                <h3>Student Accounting</h3>
            </div>
            <a href="/accounting/student">Open</a>
        </div>
        <div class="money-list">
            <?php foreach ($feeSummary as $label => $value): ?>
                <div>
                    <span><?= e($label) ?></span>
                    <strong><?= money($value) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>

<section class="module-grid">
    <?php foreach ($modules as $key => $module): ?>
        <?php $modulePath = $module['role_paths'][$user['role'] ?? ''] ?? $module['path'] ?? '/' . $key; ?>
        <a class="module-card" href="<?= e($modulePath) ?>">
            <span><?= module_icon($module['icon']) ?></span>
            <strong><?= e($module['title']) ?></strong>
            <p><?= e($module['description']) ?></p>
        </a>
    <?php endforeach; ?>
</section>
