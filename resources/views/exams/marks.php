<?php include __DIR__ . '/nav.php'; ?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher entry</p>
        <h2>Exam Mark</h2>
        <p>Select class, section, term, session, and subject to enter marks. The system will find or create the matching hidden exam record automatically.</p>
    </div>
</section>

<section class="panel">
    <div class="panel-header"><div><p class="eyebrow">Filter</p><h3>Select Class and Subject</h3></div></div>
    <form class="student-search-form" method="get" action="/exams/marks">
        <label><span>Class</span><select name="class_name" required><option value="">Class</option><?php foreach ($classOptions as $class): ?><option value="<?= e($class) ?>" <?= $filters['class_name'] === $class ? 'selected' : '' ?>><?= e($class) ?></option><?php endforeach; ?></select></label>
        <label><span>Section</span><select name="section" required><option value="">Section</option><?php foreach ($sections as $section): ?><option value="<?= e($section) ?>" <?= $filters['section'] === $section ? 'selected' : '' ?>><?= e($section) ?></option><?php endforeach; ?></select></label>
        <label><span>Term</span><select name="school_term" required><option value="">Term</option><?php foreach ($schoolTerms as $term): ?><option value="<?= e($term) ?>" <?= $filters['school_term'] === $term ? 'selected' : '' ?>><?= e($term) ?></option><?php endforeach; ?></select></label>
        <label><span>Session</span><select name="school_session" required><option value="">Session</option><?php foreach ($schoolSessions as $session): ?><option value="<?= e($session) ?>" <?= $filters['school_session'] === $session ? 'selected' : '' ?>><?= e($session) ?></option><?php endforeach; ?></select></label>
        <label><span>Subject</span><select name="subject_id" required><option value="">Subject</option><?php foreach ($subjects as $subject): ?><option value="<?= e($subject['subject_id']) ?>" <?= (int) $filters['subject_id'] === (int) $subject['subject_id'] ? 'selected' : '' ?>><?= e($subject['class_name']) ?> <?= e($subject['section']) ?> - <?= e($subject['subject_name']) ?></option><?php endforeach; ?></select></label>
        <button type="submit">Load</button>
    </form>
</section>

<?php if (!empty($filtersComplete) && !$setting): ?>
    <section class="panel empty-state">
        <h3>No student record found for this class and subject</h3>
        <p>Please confirm the class, section, term, session, and subject selection.</p>
    </section>
<?php endif; ?>

<?php if ($notice !== ''): ?><div class="alert-error"><?= e($notice) ?></div><?php endif; ?>

<?php if ($setting): ?>
    <?php
        $canSaveExamMarks = can('exam_mark', 'create') || can('exam_mark', 'edit') || can('exams', 'create') || can('exams', 'edit');
        $canEditExamMarks = can('exam_mark', 'edit') || can('exams', 'edit');
        $canDeleteExamMarks = can('exam_mark', 'delete') || can('exams', 'delete');
        $classPreviewQuery = http_build_query([
            'class_name' => $setting['class_name'],
            'section' => $setting['section'],
            'school_term' => $setting['school_term'],
            'school_session' => $setting['school_session'],
        ]);
        $selectedStudentPreviewQuery = $editStudentId > 0
            ? http_build_query([
                'class_name' => $setting['class_name'],
                'section' => $setting['section'],
                'school_term' => $setting['school_term'],
                'school_session' => $setting['school_session'],
                'student_id' => $editStudentId,
            ])
            : '';
    ?>
    <div class="preview-links no-print">
        <a class="secondary-action" href="/exams/result-preview?<?= e($classPreviewQuery) ?>">Preview Class Result</a>
        <?php if ($selectedStudentPreviewQuery !== ''): ?>
            <a class="secondary-action" href="/exams/result-preview/student?<?= e($selectedStudentPreviewQuery) ?>">View Full Report Sheet</a>
        <?php endif; ?>
    </div>

    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow"><?= e($setting['exam_name']) ?></p><h3><?= e($setting['class_name']) ?> <?= e($setting['section']) ?> - <?= e($setting['subject_name']) ?></h3></div></div>
        <form method="post" action="/exams/marks/save">
            <?= csrf_field() ?>
            <input type="hidden" name="exam_setting_id" value="<?= e($setting['id']) ?>">
            <input type="hidden" name="edit_student_id" value="<?= e($editStudentId) ?>">
            <?php if ($canSaveExamMarks): ?>
                <div class="form-actions exam-mark-save-actions exam-mark-save-actions-top no-print">
                    <button type="submit"><?= $editStudentId > 0 ? 'Update Marks' : 'Save Marks' ?></button>
                    <span class="muted">Enter marks, then click save before leaving this page.</span>
                </div>
            <?php endif; ?>
            <div class="table-wrap exam-mark-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Register No</th>
                            <th>Student Name</th>
                            <?php foreach ($distributions as $distribution): ?>
                                <th><?= e($distribution['name']) ?> / <?= e($distribution['max_mark']) ?> obtainable</th>
                            <?php endforeach; ?>
                            <th>Total</th>
                            <th>Remark</th>
                            <th>Teacher Comment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ((array) $students as $student): ?>
                            <?php
                                $studentId = (int) $student['id'];
                                $total = 0;
                                $studentMarks = is_array($marks[$studentId] ?? null) ? $marks[$studentId] : [];
                                $studentComment = is_array($comments[$studentId] ?? null) ? $comments[$studentId] : [];
                                $hasSavedMarks = !empty($studentMarks);
                                $isEditing = $editStudentId === $studentId;
                                $isLocked = $hasSavedMarks && !$isEditing;
                                $markQuery = http_build_query(['setting_id' => $setting['id'], 'student_id' => $studentId]);
                                $reportQuery = http_build_query([
                                    'class_name' => $setting['class_name'],
                                    'section' => $setting['section'],
                                    'school_term' => $setting['school_term'],
                                    'school_session' => $setting['school_session'],
                                    'student_id' => $studentId,
                                ]);
                                $returnQuery = http_build_query([
                                    'class_name' => $setting['class_name'],
                                    'section' => $setting['section'],
                                    'school_term' => $setting['school_term'],
                                    'school_session' => $setting['school_session'],
                                    'subject_id' => $setting['subject_id'],
                                    'edit_student' => $studentId,
                                ]);
                            ?>
                            <tr class="exam-mark-row">
                                <td><?= e($student['registration_no']) ?></td>
                                <td><?= e($student['applicant']) ?></td>
                                <?php foreach ((array) $distributions as $distribution): ?>
                                    <?php $value = $studentMarks[(int) $distribution['id']] ?? ''; $total += (float) $value; ?>
                                    <td>
                                    <?php if ($isLocked): ?>
                                        <?= $value === '' ? '-' : e($value) ?>
                                    <?php else: ?>
                                        <input class="table-input exam-mark-input" type="number" step="0.01" min="0" max="<?= e($distribution['max_mark']) ?>" name="marks[<?= e($studentId) ?>][<?= e($distribution['id']) ?>]" value="<?= e($value) ?>">
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                                <td><span class="exam-total-value"><?= e($total) ?></span></td>
                                <td>
                                    <?php if ($isLocked): ?>
                                        <?= e($studentComment['teacher_remark'] ?? '') ?>
                                    <?php else: ?>
                                        <input class="table-input" name="comments[<?= e($studentId) ?>][teacher_remark]" value="<?= e($studentComment['teacher_remark'] ?? '') ?>" placeholder="Pass, improve, excellent">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isLocked): ?>
                                        <?= e($studentComment['teacher_comment'] ?? '') ?>
                                    <?php else: ?>
                                        <input class="table-input" name="comments[<?= e($studentId) ?>][teacher_comment]" value="<?= e($studentComment['teacher_comment'] ?? '') ?>" placeholder="Teacher comment">
                                    <?php endif; ?>
                                </td>
                                <td><span class="status"><?= $hasSavedMarks ? ($isEditing ? 'Editing' : 'Saved') : 'New' ?></span></td>
                                <td class="row-actions no-print">
                                    <?php if ($hasSavedMarks): ?>
                                        <a href="/exams/marks/view?<?= e($markQuery) ?>">View</a>
                                        <a href="/exams/marks/view?<?= e($markQuery) ?>" onclick="setTimeout(() => window.print(), 500)">Print</a>
                                        <a href="/exams/result-preview/student?<?= e($reportQuery) ?>">Preview Student Report Sheet</a>
                                        <a href="/exams/result-preview/student?<?= e($reportQuery . '&auto_print=1') ?>">Print Report Sheet</a>
                                        <?php if ($canEditExamMarks): ?><a href="/exams/marks?<?= e($returnQuery) ?>">Edit</a><?php endif; ?>
                                        <?php if ($isEditing): ?><a href="/exams/result-preview/student?<?= e($reportQuery) ?>">View Full Report Sheet</a><?php endif; ?>
                                        <?php if ($canDeleteExamMarks): ?>
                                            <form method="post" action="/exams/marks/delete">
                                                <input type="hidden" name="exam_setting_id" value="<?= e($setting['id']) ?>">
                                                <input type="hidden" name="student_id" value="<?= e($studentId) ?>">
                                                <button type="submit">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="muted">Not saved</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php $distributionCount = is_countable($distributions) ? count($distributions) : 0; ?>
                        <?php if (empty($students)): ?><tr><td colspan="<?= e($distributionCount + 7) ?>">No student record found for this class and subject.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($canSaveExamMarks): ?><div class="form-actions exam-mark-save-actions exam-mark-save-actions-bottom"><button type="submit"><?= $editStudentId > 0 ? 'Update Marks' : 'Save Marks' ?></button></div><?php endif; ?>
        </form>
    </section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const formatTotal = (value) => {
        if (!Number.isFinite(value)) {
            return '0';
        }

        const rounded = Math.round(value * 100) / 100;
        if (Number.isInteger(rounded)) {
            return String(rounded);
        }

        return rounded.toFixed(2).replace(/\.00$/, '').replace(/(\.\d*[1-9])0+$/, '$1');
    };

    document.querySelectorAll('tr.exam-mark-row').forEach((row) => {
        const totalEl = row.querySelector('.exam-total-value');
        const inputs = row.querySelectorAll('input.exam-mark-input');

        if (!totalEl || !inputs.length) {
            return;
        }

        const updateTotal = () => {
            const total = Array.from(inputs).reduce((sum, input) => {
                const value = parseFloat(String(input.value).replace(',', '.'));
                return sum + (Number.isFinite(value) ? value : 0);
            }, 0);

            totalEl.textContent = formatTotal(total);
        };

        inputs.forEach((input) => {
            input.addEventListener('input', updateTotal);
            input.addEventListener('change', updateTotal);
        });

        updateTotal();
    });
});
</script>
