<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2>My Staff Attendance</h2>
        <p>Review only the attendance records stored for your own staff profile.</p>
    </div>
    <div class="hero-actions">
        <a class="primary-action" href="/teacher/attendance">Mark Student Attendance</a>
        <a class="secondary-action" href="/teacher/attendance/history">Attendance History</a>
    </div>
</section>

<?php require __DIR__ . '/../attendance/nav.php'; ?>

<?php
    $summary = $summary ?? [];
    $filters = $filters ?? [];
?>

<?php if (!empty($workspaceWarning)): ?>
    <section class="alert-error"><p><?= e($workspaceWarning) ?></p></section>
<?php endif; ?>

<section class="stat-grid">
    <article class="stat-card"><span>records</span><strong><?= e($summary['total'] ?? 0) ?></strong><p>attendance rows for the selected month</p></article>
    <article class="stat-card"><span>present</span><strong><?= e($summary['Present'] ?? 0) ?></strong><p>days marked present</p></article>
    <article class="stat-card"><span>absent</span><strong><?= e($summary['Absent'] ?? 0) ?></strong><p>days marked absent</p></article>
    <article class="stat-card"><span>late</span><strong><?= e($summary['Late'] ?? 0) ?></strong><p>days marked late</p></article>
    <article class="stat-card"><span>excused</span><strong><?= e($summary['Excused'] ?? 0) ?></strong><p>days marked excused</p></article>
    <article class="stat-card"><span>leave</span><strong><?= e($summary['Leave'] ?? 0) ?></strong><p>days marked leave</p></article>
    <article class="stat-card"><span>half day</span><strong><?= e($summary['Half Day'] ?? 0) ?></strong><p>days marked half day</p></article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Filter</p>
            <h3>Attendance Month and Status</h3>
        </div>
    </div>
    <form class="student-search-form" method="get" action="/teacher/my-attendance">
        <label>
            <span>Month</span>
            <input type="month" name="month" value="<?= e($filters['month'] ?? date('Y-m')) ?>" required>
        </label>
        <label>
            <span>Status</span>
            <select name="status">
                <option value="">All Statuses</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Filter Attendance</button>
        <a class="secondary-action" href="/teacher/my-attendance">Reset</a>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow"><?= e($filters['month'] ?? date('Y-m')) ?></p>
            <h3><?= e(trim(($staff['name'] ?? '') . ' ' . ($staff['middle_name'] ?? '') . ' ' . ($staff['surname'] ?? ''))) ?> Attendance</h3>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th>Status</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e($row['attendance_date']) ?></td>
                        <td><?= e($row['clock_in'] ?: '-') ?></td>
                        <td><?= e($row['clock_out'] ?: '-') ?></td>
                        <td><span class="status"><?= e($row['status']) ?></span></td>
                        <td><?= e($row['note_text'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="5">No staff attendance records match the selected filters.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
