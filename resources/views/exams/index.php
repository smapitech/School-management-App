<?php include __DIR__ . '/nav.php'; ?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Exam master</p>
        <h2>Exams</h2>
        <p>Filter configured exam records, expected marks, pass marks, and student performance from one exam workspace.</p>
    </div>
</section>

<section class="stat-grid">
    <article class="stat-card"><span>configured exams</span><strong><?= e(count($settings)) ?></strong><p>class-subject records</p></article>
    <article class="stat-card"><span>mark types</span><strong><?= e(count($distributions)) ?></strong><p>assessment components</p></article>
    <article class="stat-card"><span>grade ranges</span><strong><?= e(count($grades)) ?></strong><p>result grading</p></article>
    <article class="stat-card"><span>schedules</span><strong><?= e(count($schedules)) ?></strong><p>planned papers</p></article>
</section>

<?php if (($user['role'] ?? '') === 'teacher' && empty($teacherAssignments)): ?>
    <section class="panel empty-state">
        <h3>No assigned class yet</h3>
        <p>Your teacher account must be connected to a staff profile email and assigned to a class before exam records can be entered.</p>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Class filter</p><h3>Configured Exam Records</h3></div></div>
    <form class="student-search-form exam-overview-form" method="get" action="/exams">
        <label><span>Class</span><select name="class_name" required><option value="">Select class</option><?php foreach ($classOptions as $class): ?><option value="<?= e($class) ?>" <?= $filters['class_name'] === $class ? 'selected' : '' ?>><?= e($class) ?></option><?php endforeach; ?></select></label>
        <label><span>Section</span><select name="section"><option value="">All sections</option><?php foreach ($sections as $section): ?><option value="<?= e($section) ?>" <?= $filters['section'] === $section ? 'selected' : '' ?>><?= e($section) ?></option><?php endforeach; ?></select></label>
        <label><span>Term</span><select name="school_term"><option value="">All terms</option><?php foreach ($schoolTerms as $term): ?><option value="<?= e($term) ?>" <?= $filters['school_term'] === $term ? 'selected' : '' ?>><?= e($term) ?></option><?php endforeach; ?></select></label>
        <label><span>Session</span><select name="school_session"><option value="">All sessions</option><?php foreach ($schoolSessions as $session): ?><option value="<?= e($session) ?>" <?= $filters['school_session'] === $session ? 'selected' : '' ?>><?= e($session) ?></option><?php endforeach; ?></select></label>
        <button type="submit">Show Class</button>
        <a class="secondary-action" href="/exams">Reset</a>
    </form>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Records</p><h3>Class-Subject Exam Records</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Record</th><th>Class</th><th>Section</th><th>Term</th><th>Session</th><th>Subject</th><th>Total Mark</th><th>Pass Mark</th></tr></thead>
            <tbody>
                <?php foreach ($overviewSubjects as $row): ?>
                    <tr>
                        <td><?= e($row['exam_name']) ?></td>
                        <td><?= e($row['class_name']) ?></td>
                        <td><?= e($row['section']) ?></td>
                        <td><?= e($row['school_term']) ?></td>
                        <td><?= e($row['school_session']) ?></td>
                        <td><?= e($row['subject_name']) ?></td>
                        <td><?= e($row['total_mark']) ?></td>
                        <td><?= e($row['pass_mark']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($overviewSubjects)): ?><tr><td colspan="8">No configured exam record matches this filter.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Student filter</p><h3>Student Exam Scores</h3></div></div>
    <form class="student-search-form exam-overview-form" method="get" action="/exams">
        <label>
            <span>Student Name</span>
            <select name="student_id" required>
                <option value="0">Select student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= e($student['id']) ?>" <?= (int) $filters['student_id'] === (int) $student['id'] ? 'selected' : '' ?>><?= e($student['registration_no']) ?> - <?= e($student['applicant']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label><span>Term</span><select name="school_term"><option value="">All terms</option><?php foreach ($schoolTerms as $term): ?><option value="<?= e($term) ?>" <?= $filters['school_term'] === $term ? 'selected' : '' ?>><?= e($term) ?></option><?php endforeach; ?></select></label>
        <label><span>Session</span><select name="school_session"><option value="">All sessions</option><?php foreach ($schoolSessions as $session): ?><option value="<?= e($session) ?>" <?= $filters['school_session'] === $session ? 'selected' : '' ?>><?= e($session) ?></option><?php endforeach; ?></select></label>
        <button type="submit">Show Student</button>
        <a class="secondary-action" href="/exams">Reset</a>
    </form>
    <?php if ((int) $filters['student_id'] > 0): ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Subject</th><th>Class</th><th>Term</th><th>Session</th><th>Obtainable Mark</th><th>Pass Mark</th><th>Score Obtained</th><th>Grade</th></tr></thead>
                <tbody>
                    <?php foreach ($studentResults as $row): ?>
                        <tr>
                            <td><?= e($row['subject_name']) ?></td>
                            <td><?= e($row['class_name']) ?> <?= e($row['section']) ?></td>
                            <td><?= e($row['school_term']) ?></td>
                            <td><?= e($row['school_session']) ?></td>
                            <td><?= e($row['total_mark']) ?></td>
                            <td><?= e($row['pass_mark']) ?></td>
                            <td><?= e($row['student_score']) ?></td>
                            <td><span class="status"><?= e($row['grade']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($studentResults)): ?><tr><td colspan="8">No marks have been entered for this student yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
